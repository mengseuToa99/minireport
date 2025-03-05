<?php

namespace Modules\MiniReportB1\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\MiniReportB1\Entities\Report;
use Modules\MiniReportB1\Entities\ReportField;
use Illuminate\Support\Facades\DB;
use Modules\MiniReportB1\Entities\Report as EntitiesReport;

class ReportController1 extends Controller
{
    public function showTablesAndFields()
    {
        // Fetch all tables in the database
        $tables = DB::select('SHOW TABLES');

        // Prepare an array to hold table names and their fields
        $tablesWithFields = [];

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . env('DB_DATABASE')}; // Adjust based on your database name

            // Escape the table name using backticks
            $fields = DB::select("DESCRIBE `$tableName`"); // Fetch fields for the table

            $tablesWithFields[$tableName] = $fields;
        }

        // Pass the data to the Blade view
        return view('minireportb1::MiniReportB1.select-tables-fields', compact('tablesWithFields'));
    }


    /**
     * Save a new report with selected fields.
     */
    public function store(Request $request)
    {
        // Retrieve the business_id from the session
        $business_id = $request->session()->get('user.business_id');
    
        // Validate the request data
        $validatedData = $request->validate([
            '*.name' => 'required|string|max:255',
            '*.fields' => 'required|array',
            '*.fields.*.table_name' => 'required|string',
            '*.fields.*.field_name' => 'required|string',
        ]);
    
        // Iterate through each report in the request
        foreach ($validatedData as $reportData) {
            // Insert into the `reports` table
            $reportId = DB::table('minireportb1_reports')->insertGetId([
                'name' => $reportData['name'], // Ensure `name` is included
                'business_id' => $business_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Save the selected fields
            foreach ($reportData['fields'] as $field) {
                DB::table('report_fields')->insert([
                    'report_id' => $reportId,
                    'table_name' => $field['table_name'],
                    'field_name' => $field['field_name'],
                    'business_id' => $business_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    
        // Return a success response
        return response()->json([
            'message' => 'Reports saved successfully!',
        ], 201);
    }

    public function show()
    {
        $business_id = 11;
        // Fetch all reports with their fields for the given business_id
        $reports = Report::with('fields')->where('business_id', $business_id)->get();

        $allFormattedData = [];

        foreach ($reports as $report) {
            // Get the tables and fields from the report
            $tables = $report->fields->pluck('table_name')->unique();
            $fields = $report->fields->pluck('field_name', 'table_name');

            // Start building the query
            $query = DB::table($tables->first());

            // Add fields to the select clause
            $selectFields = [];
            foreach ($fields as $table => $field) {
                $selectFields[] = "{$table}.{$field}";
            }
            $query->select($selectFields);

            // Handle JOINs if multiple tables are involved
            if ($tables->count() > 1) {
                $primaryTable = $tables->first();
                foreach ($tables->skip(1) as $table) {
                    $query->join($table, function ($join) use ($primaryTable, $table, $business_id) {
                        $join->on("{$primaryTable}.id", '=', "{$table}.id")
                            ->where("{$table}.business_id", '=', $business_id);
                    });
                }
            }

            // Add business_id filter
            $query->where("{$tables->first()}.business_id", $business_id);

            // Execute the query
            $data = $query->get();

            // Format the data for the view
            $formattedData = $data->map(function ($row) use ($fields) {
                $formattedRow = [];
                foreach ($fields as $table => $field) {
                    $formattedRow[$field] = $row->$field;
                }
                return $formattedRow;
            });

            $allFormattedData[$report->id] = [
                'report' => $report,
                'data' => $formattedData
            ];
        }

        return view('minireportb1::MiniReportB1.show', [
            'allData' => $allFormattedData,
        ]);
    }

    public function view(Request $request)
    {
        $business_id = $request->session()->get('user.business_id', 1); // Default to 1 if not set

        // Get header rows from the layout component
        $headerRows = [
            ['field' => 'invoice_no', 'value' => 'Invoice No', 'visibility' => 'true'],
            ['field' => 'transaction_date', 'value' => 'Date', 'visibility' => 'true'],
            ['field' => 'customer_name', 'value' => 'Customer', 'visibility' => 'true'],
            ['field' => 'final_total', 'value' => 'Total Amount', 'visibility' => 'true'],
            ['field' => 'payment_status', 'value' => 'Payment Status', 'visibility' => 'true'],
            ['field' => 'payment_method', 'value' => 'Payment Method', 'visibility' => 'true'],
            ['field' => 'location_name', 'value' => 'Location', 'visibility' => 'true']
        ];

        $columnVisibility = collect($headerRows)->pluck('visibility')->toArray();

        try {
            $query = DB::table('transactions as t')
                ->leftJoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftJoin('transaction_payments as tp', 't.id', '=', 'tp.transaction_id')
                ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->select(
                    't.invoice_no',
                    't.transaction_date',
                    'c.name as customer_name',
                    't.final_total',
                    't.payment_status',
                    'tp.method as payment_method',
                    'bl.name as location_name'
                )
                ->orderBy('t.transaction_date', 'desc');

            $paginatedData = $query->paginate(10);
            
            return view('minireportb1::MiniReportB1.view', [
                'headerRows' => $headerRows,
                'columnVisibility' => $columnVisibility,
                'salesData' => $paginatedData,
                'page' => $paginatedData->currentPage(),
                'totalPages' => $paginatedData->lastPage()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching sales data: ' . $e->getMessage());
            return view('minireportb1::MiniReportB1.view', [
                'headerRows' => $headerRows,
                'columnVisibility' => $columnVisibility,
                'salesData' => collect([]), // Empty collection if query fails
                'page' => 1,
                'totalPages' => 1
            ])->withErrors(['error' => 'Failed to fetch sales data']);
        }
    }
}
