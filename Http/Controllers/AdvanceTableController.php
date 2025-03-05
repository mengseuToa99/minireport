<?php

namespace Modules\MiniReportB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdvanceTableController extends Controller
{
    public function index()
    {
        $tables = DB::select('SHOW TABLES');
        $tablesWithColumns = [];
        $foreignKeyRelationships = [];
        $joinablePaths = [];

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . env('DB_DATABASE')};
            $escapedTableName = "`$tableName`";

            // Get columns
            $columns = DB::select("SHOW COLUMNS FROM $escapedTableName");
            $tablesWithColumns[$tableName] = array_column($columns, 'Field');

            // Get foreign keys
            $foreignKeys = DB::select("
                SELECT 
                    TABLE_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM 
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE 
                    TABLE_SCHEMA = ? 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                    AND TABLE_NAME = ?
            ", [env('DB_DATABASE'), $tableName]);

            foreach ($foreignKeys as $fk) {
                $foreignKeyRelationships[$tableName][] = [
                    'column' => $fk->COLUMN_NAME,
                    'referenced_table' => $fk->REFERENCED_TABLE_NAME,
                    'referenced_column' => $fk->REFERENCED_COLUMN_NAME,
                ];
            }
        }

        // Find all possible join paths between tables
        foreach ($tablesWithColumns as $sourceTable => $sourceColumns) {
            $joinablePaths[$sourceTable] = $this->findJoinPaths($sourceTable, $foreignKeyRelationships);
        }

        return view('minireportb1::MiniReportB1.advance', [
            'tablesWithColumns' => $tablesWithColumns,
            'foreignKeyRelationships' => $foreignKeyRelationships,
            'joinablePaths' => $joinablePaths,
            'error' => ''
        ]);
    }

    private function findJoinPaths($sourceTable, $relationships, $visited = [], $currentPath = [])
    {
        $paths = [];
        $visited[] = $sourceTable;
        $currentPath[] = $sourceTable;

        if (isset($relationships[$sourceTable])) {
            foreach ($relationships[$sourceTable] as $fk) {
                $targetTable = $fk['referenced_table'];

                if (!in_array($targetTable, $visited)) {
                    $paths[] = [
                        'path' => array_merge($currentPath, [$targetTable]),
                        'joins' => [[
                            'from_table' => $sourceTable,
                            'from_column' => $fk['column'],
                            'to_table' => $targetTable,
                            'to_column' => $fk['referenced_column']
                        ]]
                    ];

                    $subPaths = $this->findJoinPaths($targetTable, $relationships, $visited, array_merge($currentPath, [$targetTable]));
                    foreach ($subPaths as $subPath) {
                        $paths[] = [
                            'path' => $subPath['path'],
                            'joins' => array_merge([[
                                'from_table' => $sourceTable,
                                'from_column' => $fk['column'],
                                'to_table' => $targetTable,
                                'to_column' => $fk['referenced_column']
                            ]], $subPath['joins'])
                        ];
                    }
                }
            }
        }

        return $paths;
    }


    public function generateReport(Request $request)
    {
        try {
            $query = $request->input('query');
            $fields = $request->input('fields');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);

            // Create table aliases and track column names
            $tableAliases = [];
            $usedColumns = [];

            // First create table aliases
            foreach ($fields as $field) {
                if (!isset($tableAliases[$field['table']])) {
                    $tableAliases[$field['table']] = substr($field['table'], 0, 1) . '_' . count($tableAliases);
                }
            }

            // Replace table references in FROM and JOIN clauses
            foreach ($tableAliases as $table => $alias) {
                $query = preg_replace("/\b{$table}\./", "{$alias}.", $query);
                $query = preg_replace("/\bFROM\s+{$table}\b/i", "FROM {$table} AS {$alias}", $query);
                $query = preg_replace("/\bJOIN\s+{$table}\b/i", "JOIN {$table} AS {$alias}", $query);
            }

            // Build the SELECT clause with proper aliases for duplicate column names
            $selectParts = [];
            foreach ($fields as $field) {
                $alias = $tableAliases[$field['table']];
                $columnName = $field['field'];

                // Create a unique column alias if this column name was already used
                if (isset($usedColumns[$columnName])) {
                    $columnAlias = "{$field['table']}_{$columnName}";
                    $selectParts[] = "{$alias}.{$columnName} AS {$columnAlias}";
                } else {
                    $selectParts[] = "{$alias}.{$columnName}";
                    $usedColumns[$columnName] = true;
                }
            }

            // Replace the SELECT clause
            $selectClause = implode(', ', $selectParts);
            $query = preg_replace('/SELECT\s+.*?\s+FROM/is', "SELECT $selectClause FROM", $query);

            // Add WHERE clause for business_id
            $businessId = auth()->user()->business_id;
            foreach ($tableAliases as $table => $alias) {
                $hasBusinessId = Schema::hasColumn($table, 'business_id');
                if ($hasBusinessId) {
                    if (stripos($query, 'WHERE') === false) {
                        $query .= " WHERE {$alias}.business_id = {$businessId}";
                    } else {
                        $query .= " AND {$alias}.business_id = {$businessId}";
                    }
                    break;
                }
            }

            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM ({$query}) as count_table";
            $totalCount = DB::select($countQuery)[0]->total;

            // Add pagination
            $offset = ($page - 1) * $perPage;
            $query .= " LIMIT {$perPage} OFFSET {$offset}";

            Log::info('Executing Query:', ['query' => $query]);
            $results = DB::select($query);

            return response()->json([
                'data' => $results,
                'pagination' => [
                    'total' => $totalCount,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($totalCount / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Query Execution Error:', [
                'error' => $e->getMessage(),
                'query' => $query ?? 'Query not available',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    private function getJoinablePaths()
    {
        $tables = DB::select('SHOW TABLES');
        $foreignKeyRelationships = [];
        $joinablePaths = [];

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . env('DB_DATABASE')};

            $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE 
                TABLE_SCHEMA = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
                AND TABLE_NAME = ?
        ", [env('DB_DATABASE'), $tableName]);

            foreach ($foreignKeys as $fk) {
                $foreignKeyRelationships[$tableName][] = [
                    'column' => $fk->COLUMN_NAME,
                    'referenced_table' => $fk->REFERENCED_TABLE_NAME,
                    'referenced_column' => $fk->REFERENCED_COLUMN_NAME,
                ];
            }
        }

        foreach (array_keys($foreignKeyRelationships) as $sourceTable) {
            $joinablePaths[$sourceTable] = $this->findJoinPaths($sourceTable, $foreignKeyRelationships);
        }

        return $joinablePaths;
    }
    public function getJoinQuery(Request $request)
    {
        $selectedTables = $request->input('tables', []);
        $joinablePaths = $this->getJoinablePaths();

        // Find a path that includes all selected tables
        $path = $this->findPathConnectingAllTables($selectedTables, $joinablePaths);

        if (!$path) {
            return response()->json(['error' => 'No valid join path found for the selected tables']);
        }

        $query = "SELECT * FROM {$path['path'][0]}";
        foreach ($path['joins'] as $join) {
            $query .= " JOIN {$join['to_table']} ON {$join['from_table']}.{$join['from_column']} = {$join['to_table']}.{$join['to_column']}";
        }

        return response()->json(['query' => $query]);
    }

    private function findPathConnectingAllTables($selectedTables, $allPaths)
    {
        // Implement logic to find a path that connects all selected tables
        // This is a simplified example; you may need a more sophisticated algorithm
        $path = [];
        for ($i = 0; $i < count($selectedTables) - 1; $i++) {
            $source = $selectedTables[$i];
            $target = $selectedTables[$i + 1];
            $subPath = $this->findShortestJoinPath($source, $target, $allPaths);
            if (!$subPath) return null;
            $path['joins'] = array_merge($path['joins'] ?? [], $subPath['joins']);
            $path['path'] = array_unique(array_merge($path['path'] ?? [], $subPath['path']));
        }
        return $path;
    }

    private function findShortestJoinPath($sourceTable, $targetTable, $allPaths)
    {
        $validPaths = array_filter($allPaths[$sourceTable], function ($path) use ($targetTable) {
            return end($path['path']) === $targetTable;
        });

        if (empty($validPaths)) {
            return null;
        }

        return array_reduce($validPaths, function ($shortest, $current) {
            if (!$shortest || count($current['path']) < count($shortest['path'])) {
                return $current;
            }
            return $shortest;
        });
    }
}
