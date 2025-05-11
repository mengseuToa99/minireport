<?php

namespace Modules\MiniReportB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Schema;
use Modules\MiniReportB1\Entities\MiniReportB1Report;
use Modules\MiniReportB1\Entities\Report;
use App\Utils\ProductUtil;
use App\BusinessLocation;
use App\Contact;
use App\User;
use Modules\MiniReportB1\Entities\MiniReportB1File;
use Modules\MiniReportB1\Entities\MiniReportB1Folder;

class SaleController extends Controller
{
    protected $transactionUtil;
    protected $businessUtil;
    protected $moduleUtil;
    protected $productUtil;


    public function __construct(
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ModuleUtil $moduleUtil,
        ProductUtil $productUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
    }



    public function index()
    {
        // Check if the user is authorized
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        // Retrieve the business ID from the session
        $business_id = request()->session()->get('user.business_id');

        // Fetch folders related to the business
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        // Fetch business locations for the filter component
        $business_locations = BusinessLocation::forDropdown($business_id, false);

        // Fetch customers for the filter component
        $customers = Contact::customersDropdown($business_id, false);

        // Fetch payment types and sources for the filter component
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
        $sources = $this->transactionUtil->getSources($business_id);


        // Return the view with necessary data
        return view('minireportb1::MiniReportB1.create')
            ->with(compact('folders', 'payment_types', 'sources', 'business_locations', 'customers'));
    }

    public function getData()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
    
            // Retrieve the file ID from the session
            $file_id = request()->session()->get('current_file_id');
            if (!$file_id) {
                throw new \Exception('File ID not found in session.');
            }
    
            // Retrieve the file with validation
            $file = MiniReportB1File::where('business_id', $business_id)
                ->where('id', $file_id)
                ->firstOrFail();
    
            // Decode layout JSON with validation
            $layout = json_decode($file->layout, true);
            if (!is_array($layout)) {
                throw new \Exception('Invalid layout format in the file record.');
            }
    
            // Extract layout components with defaults
            $headerRows = $layout['headerRows'] ?? [];
            $rows = $layout['rows'] ?? [];
            $columnVisibility = $layout['columnVisibility'] ?? [];
    
            // Paginate the rows
            $page = (int) request()->get('page', 1);
            $perPage = (int) request()->get('per_page', 25); // Default rows per page
            $totalRows = count($rows);
            $paginatedRows = array_slice($rows, ($page - 1) * $perPage, $perPage);
    
            return response()->json([
                'rows' => $paginatedRows,
                'totalRows' => $totalRows,
                'totalPages' => ceil($totalRows / $perPage),
                'currentPage' => $page,
            ]);
        } catch (\Exception $e) {    
            return response()->json([
                'error' => 'Error retrieving data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRawSalesData($usedFields = [], $filters = [], $limit = null)
    {
        $business_id = request()->session()->get('user.business_id');

        // If no usedFields are provided, fetch all fields from reports
        if (empty($usedFields)) {
            $usedFields = Report::with('fields')
                ->where('business_id', $business_id)
                ->get()
                ->flatMap(function ($report) {
                    return $report->fields->map(function ($field) {
                        return [
                            'table_name' => $field->table_name,
                            'field_name' => $field->field_name
                        ];
                    });
                })
                ->unique(function ($item) {
                    return $item['field_name'];
                })
                ->values();
        }

        // Initialize query with account_transactions table
        $query = DB::table('account_transactions as at');

        // Track joined tables to avoid duplicate joins
        $joinedTables = [];

        // Build select clause based on selected fields
        $selectFields = [];

        foreach ($usedFields as $field) {
            $fieldName = $field['field_name'];
            $tableName = $field['table_name'];

            // Handle fields from different tables
            if ($tableName === 'purchase_lines' && !isset($joinedTables['purchase_lines'])) {
                $query->leftJoin('purchase_lines as pl', 'at.transaction_id', '=', 'pl.transaction_id');
                $joinedTables['purchase_lines'] = true;
                $selectFields[] = "pl.{$fieldName} as {$fieldName}";
            } elseif ($tableName === 'accounting_accounts' && !isset($joinedTables['accounting_accounts'])) {
                $query->leftJoin('accounting_accounts as aa', 'at.account_id', '=', 'aa.id');
                $joinedTables['accounting_accounts'] = true;
                $selectFields[] = "aa.{$fieldName} as {$fieldName}";
            }
            // Handle account_transactions fields
            elseif (Schema::hasColumn('account_transactions', $fieldName)) {
                if (in_array($fieldName, ['operation_date', 'created_at'])) {
                    $selectFields[] = DB::raw("DATE_FORMAT(at.{$fieldName}, '%Y-%m-%d') as {$fieldName}");
                } else {
                    $selectFields[] = "at.{$fieldName} as {$fieldName}";
                }
            }
        }

        // Ensure we have at least the ID field
        if (empty($selectFields)) {
            $selectFields[] = 'at.id';
        }

        $query->select(array_unique($selectFields));

        // Apply filters
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (!empty($value)) {
                    if (in_array($field, ['operation_date', 'created_at'])) {
                        $query->whereDate($field, $value);
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
        }

        // Add ordering
        $query->orderBy('at.id', 'desc');

        // Apply limit only if specified
        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function saveReportConfig(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $validated = $request->validate([
            'report_name' => 'required|string|max:255',
            'visible_columns' => 'required|array',
            'filters' => 'nullable|array'
        ]);

        $report = new MiniReportB1Report();
        $report->business_id = $business_id;
        $report->report_name = $validated['report_name'];
        $report->visible_columns = json_encode($validated['visible_columns']);
        $report->filters = json_encode($validated['filters'] ?? []);
        $report->created_by = auth()->user()->id;
        $report->save();

        return response()->json(['success' => true, 'report_id' => $report->id]);
    }


    public function getReportConfig($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $report = MiniReportB1Report::where('business_id', $business_id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($report);
    }

    public function getUserReports()
    {
        $business_id = request()->session()->get('user.business_id');

        $reports = MiniReportB1Report::where('business_id', $business_id)
            ->select('id', 'report_name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }
}
