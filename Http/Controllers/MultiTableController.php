<?php

namespace Modules\MiniReportB1\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use App\Audit;
use App\BusinessLocation;
use Yajra\DataTables\Facades\DataTables;
use Modules\MiniReportB1\Entities\MiniReportB1;
use Modules\MiniReportB1\Entities\MiniReportB1Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use Modules\MiniReportB1\Entities\MiniReportB1Folder;
use Modules\MiniReportB1\Entities\MiniReportB1File;
use Modules\MiniReportB1\Http\Controllers\SaleController;
use Modules\MiniReportB1\Entities\Report;
use PDF;
use App\Utils\ProductUtil;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use Modules\Essentials\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Transaction;
use App\Http\Controllers\ExpenseController;

class MultiTableController extends Controller
{
    protected $transactionUtil;
    protected $businessUtil;
    protected $moduleUtil;
    protected $salesData;
    protected $productUtil;
    protected $sell;
    protected $purchase;
    protected $payroll;
    protected $product;
    protected $expense;

    /**
     * Filter data to include only visible columns.
     *
     * @param array $data The original data.
     * @param array $visibleColumnNames The list of visible columns.
     * @return array The filtered data.
     */


    /**
     * Constructor
     */
    public function __construct(
        ProductUtil $productUtil,
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ModuleUtil $moduleUtil,
        SaleController $salesData,
        SellController $sell,
        PurchaseController $purchase,
        PayrollController $payroll,
        ProductController $product,
        ExpenseController $expense
    ) {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->salesData = $salesData;
        $this->sell = $sell;
        $this->purchase = $purchase;
        $this->payroll = $payroll;
        $this->product = $product;
        $this->expense = $expense;
    }

    ///////////////////////////////////////////////
    //                                           //
    //             View File                     //
    //                                           //
    ///////////////////////////////////////////////

    public function multiViewManager(Request $request, $id = null)
    {
        // Check user permissions
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
    
        $business_id = request()->session()->get('user.business_id');
    
        // Get folders as a collection
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();
    
        // Initialize variables for file-specific data
        $file_name = null;
        $report_name = null;
        $visibleColumnNames = [];
        $filterCriteria = [];
        $data = [];
    
        // If an ID is provided, retrieve the file and its layout
        if ($id) {
            $file = MiniReportB1File::where('id', $id)
                ->where('business_id', $business_id)
                ->first();
    
            if (!$file) {
                abort(404, 'File not found');
            }
    
            // Decode JSON layout
            $layout = json_decode($file->layout, true);
            $file_name = $file->file_name;
            $report_name = $layout['reportName'];
            $visibleColumnNames = $layout['visibleColumnNames'] ?? []; 
            $filterCriteria = $layout['filterCriteria'];
        }

    
        // Fetch data based on the report name (if provided)
        switch ($report_name) {
            case "saleReport":
                // Call the saleReport function to fetch sales data
                $response = $this->sell->index();
                $data = (array) $response->getData();
                break;
    
            case "purchaseReport":
                // Call the purchaseReport function to fetch purchase data
                $response = $this->purchase->index();
                $data = (array) $response->getData();
                break;

            case "productReport":
                    // Call the purchaseReport function to fetch purchase data
                $response = $this->product->index();
                $data = (array) $response->getData();
                break;

            case "PayRollReport":
                    // Call the purchaseReport function to fetch purchase data
                $response = $this->payroll->index();
                $data = (array) $response->getData();
                break;
            case "stockReport":
                // Call the purchaseReport function to fetch purchase data
                $response = $this->product->index();
                $data = (array) $response->getData();
                break;

            case "exspenseReport":
                // Call the purchaseReport function to fetch purchase data
                $response = $this->expense->index();
                $data = (array) $response->getData();
                break;
            
            case "pay_componentsReport":
                $response = $this->payroll->index();
                $data = (array) $response->getData();
                break;

                
            default:
                // Default data if no report name is provided
                $data = [];
                break;
        }
    
        // Add folders, file-specific data, and visibleColumnNames to the response
        $data['folders'] = $folders;
        $data['file_name'] = $file_name;
        $data['report_name'] = $report_name;
        $data['visibleColumnNames'] = $visibleColumnNames;
        $data['filterCriteria'] = $filterCriteria;

        // dd($data['report_name']);
    
        // dd($visibleColumnNames);
        // Dynamically load the view based on the report name
        $viewName = 'minireportb1::MiniReportB1.multitable.' . $report_name;
    
        // Check if the view exists
        if (!view()->exists($viewName)) {
            abort(404, "View for report '$report_name' not found.");
        }
    
        // Return the view with the merged data
        return view($viewName, $data);
    }


    public function multiViewManager1(Request $request, $id = null)
    {
        // Check user permissions
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
    
        $business_id = request()->session()->get('user.business_id');
    
        // Get folders as a collection
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();
    
        // Initialize variables for file-specific data
        $visibleColumnNames = [];
        $filterCriteria = [];
        $file_name = null;
        $report_name = null;
        $data = [];
        $columnMapping = [];
    
        // If an ID is provided, retrieve the file and its layout
        if ($id) {
            $file = MiniReportB1File::where('id', $id)
                ->where('business_id', $business_id)
                ->first();
    
            if (!$file) {
                abort(404, 'File not found');
            }
    
            // Decode JSON layout
            $layout = json_decode($file->layout, true);
            $file_name = $file->file_name;
            $report_name = $layout['reportName'];
    
            // Get visible column names and filter criteria from the layout
            $visibleColumnNames = $layout['visibleColumnNames'] ?? [];
            $filterCriteria = $layout['filterCriteria'] ?? [];
        }
    
        // Fetch data based on the report name (if provided)
        switch ($report_name) {
            case "saleReport":
                // Call the SaleData function to fetch sales data and column mapping
                $result = $this->SaleData($request, $visibleColumnNames);
                $data = $result['data'];
                $columnMapping = $result['columnMapping'];
                break;
    
            case "purchaseReport":
                // Call the purchaseData function to fetch purchase data and column mapping
                $result = $this->purchaseData($request, $visibleColumnNames);
                $data = $result['data'];
                $columnMapping = $result['columnMapping'];
                break;
    
            default:
                // Default data if no report name is provided
                $data = [];
                break;
        }
    
        // Merge all data to pass to the view
        $mergedData = [
            'folders' => $folders,
            'visibleColumnNames' => $visibleColumnNames,
            'filterCriteria' => $filterCriteria,
            'file_name' => $file_name,
            'report_name' => $report_name,
            'data' => $data, // Pass the filtered data to the view
            'columnMapping' => $columnMapping, // Pass the column mapping to the view
        ];
    
        // Return the view with the merged data
        return view('minireportb1::MiniReportB1.multitable.multiview', $mergedData);
    }

    public function SaleData(Request $request, $columnsToShow = [])
    {
        // Check user permissions
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
    
        $business_id = request()->session()->get('user.business_id');
    
        // Define all possible columns and their corresponding database fields
        $allColumns = [
            'Date' => 'transactions.transaction_date',
            'Invoice No' => 'transactions.invoice_no',
            'Customer Name' => 'contacts.name as customer_name',
            'Contact Number' => 'contacts.mobile as contact_number',
            'Location' => 'bl.name as location',
            'Payment Status' => 'transactions.payment_status',
            'Final Total' => 'transactions.final_total',
            'Total Paid' => DB::raw('COALESCE(SUM(tp.amount), 0) as total_paid'),
            'Total Remaining' => DB::raw('(transactions.final_total - COALESCE(SUM(tp.amount), 0)) as total_remaining'),
            'Shipping Status' => 'transactions.shipping_status',
            // 'Total Items' => 'transactions.total_items',
            'Types of Service' => 'tos.name as types_of_service_name',
            'Service Custom Field 1' => 'transactions.service_custom_field_1',
            'Custom Field 1' => 'transactions.custom_field_1',
            'Custom Field 2' => 'transactions.custom_field_2',
            'Custom Field 3' => 'transactions.custom_field_3',
            'Custom Field 4' => 'transactions.custom_field_4',
            'Added By' => DB::raw('CONCAT(u.first_name, " ", u.last_name) as added_by'),
            'Additional Notes' => 'transactions.additional_notes',
            'Staff Note' => 'transactions.staff_note',
            'Shipping Details' => 'transactions.shipping_details',
            'Waiter' => DB::raw('CONCAT(ss.first_name, " ", ss.last_name) as waiter'),
        ];
    
        // If no columns are specified, show all columns by default
        if (empty($columnsToShow)) {
            $columnsToShow = array_keys($allColumns);
        }
    
        // Build the SELECT clause based on the columns to show
        $selectColumns = [];
        foreach ($columnsToShow as $column) {
            if (isset($allColumns[$column])) {
                $selectColumns[] = $allColumns[$column];
            }
        }
    
        // Start building the query
        $query = Transaction::where('transactions.business_id', $business_id)
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('business_locations as bl', 'transactions.location_id', '=', 'bl.id')
            ->leftJoin('types_of_services as tos', 'transactions.types_of_service_id', '=', 'tos.id')
            ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
            ->leftJoin('users as ss', 'transactions.res_waiter_id', '=', 'ss.id')
            ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
            ->select($selectColumns)
            ->groupBy('transactions.id'); 
    
        // Apply filters based on request
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('transactions.transaction_date', [$request->start_date, $request->end_date]);
        }
    
        if ($request->has('location_id')) {
            $query->where('transactions.location_id', $request->location_id);
        }
    
        if ($request->has('payment_status')) {
            $query->where('transactions.payment_status', $request->payment_status);
        }
    
        if ($request->has('created_by')) {
            $query->where('transactions.created_by', $request->created_by);
        }
    
        // Execute the query and get the results
        $data = $query->get()->toArray();
    
        // Define column mapping (key => display name)
        $columnMapping = [];
        foreach ($columnsToShow as $column) {
            $columnMapping[$column] = strtolower(str_replace(' ', '_', $column));
        }
    
        // dd($data, $columnMapping);
        return [
            'data' => $data,
            'columnMapping' => $columnMapping,
        ];
    }

    public function purchaseData(Request $request, $columnsToShow = [])
{
    // Check user permissions
    $is_admin = $this->businessUtil->is_admin(auth()->user());
    if (!$is_admin && !auth()->user()->hasAnyPermission(['purchase.view', 'purchase.create', 'view_own_purchase'])) {
        abort(403, 'Unauthorized action.');
    }

    $business_id = request()->session()->get('user.business_id');

    // Define all possible columns and their corresponding database fields
    $allColumns = [
        'Date' => 'transactions.transaction_date',
        'Invoice No' => 'transactions.invoice_no',
        'Supplier Name' => 'contacts.name as supplier_name',
        'Contact Number' => 'contacts.mobile as contact_number',
        'Location' => 'bl.name as location',
        'Payment Status' => 'transactions.payment_status',
        'Final Total' => 'transactions.final_total',
        'Total Paid' => DB::raw('COALESCE(SUM(tp.amount), 0) as total_paid'),
        'Total Remaining' => DB::raw('(transactions.final_total - COALESCE(SUM(tp.amount), 0)) as total_remaining'),
        'Status' => 'transactions.status',
        'Shipping Status' => 'transactions.shipping_status',
        'Added By' => DB::raw('CONCAT(u.first_name, " ", u.last_name) as added_by'),
        'Additional Notes' => 'transactions.additional_notes',
        'Staff Note' => 'transactions.staff_note',
        'Shipping Details' => 'transactions.shipping_details',
        'Custom Field 1' => 'transactions.custom_field_1',
        'Custom Field 2' => 'transactions.custom_field_2',
        'Custom Field 3' => 'transactions.custom_field_3',
        'Custom Field 4' => 'transactions.custom_field_4',
    ];

    // If no columns are specified, show all columns by default
    if (empty($columnsToShow)) {
        $columnsToShow = array_keys($allColumns);
    }

    // Build the SELECT clause based on the columns to show
    $selectColumns = [];
    foreach ($columnsToShow as $column) {
        if (isset($allColumns[$column])) {
            $selectColumns[] = $allColumns[$column];
        }
    }

    // Start building the query
    $query = Transaction::where('transactions.business_id', $business_id)
        ->where('transactions.type', 'purchase') // Filter for purchase transactions
        ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
        ->leftJoin('business_locations as bl', 'transactions.location_id', '=', 'bl.id')
        ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
        ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
        ->select($selectColumns)
        ->groupBy('transactions.id');

    // Apply filters based on request
    if ($request->has('start_date') && $request->has('end_date')) {
        $query->whereBetween('transactions.transaction_date', [$request->start_date, $request->end_date]);
    }

    if ($request->has('location_id')) {
        $query->where('transactions.location_id', $request->location_id);
    }

    if ($request->has('payment_status')) {
        $query->where('transactions.payment_status', $request->payment_status);
    }

    if ($request->has('status')) {
        $query->where('transactions.status', $request->status);
    }

    if ($request->has('supplier_id')) {
        $query->where('transactions.contact_id', $request->supplier_id);
    }

    if ($request->has('created_by')) {
        $query->where('transactions.created_by', $request->created_by);
    }

    // Execute the query and get the results
    $data = $query->get()->toArray();

    // Define column mapping (key => display name)
    $columnMapping = [];
    foreach ($columnsToShow as $column) {
        $columnMapping[$column] = strtolower(str_replace(' ', '_', $column));
    }

    return [
        'data' => $data,
        'columnMapping' => $columnMapping,
    ];
}

    private function filterVisibleColumns(array $data, array $visibleColumnNames): array
    {
        $filteredData = [];

        foreach ($data as $key => $value) {
            // If the key is in the visible columns, include it in the filtered data
            if (in_array($key, $visibleColumnNames)) {
                $filteredData[$key] = $value;
            }
        }

        return $filteredData;
    }

    function product(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        $response = $this->product->index();
        $data = (array) $response->getData();
        $data['folders'] = $folders;


        return view('minireportb1::MiniReportB1.multitable.product', $data);
    }

    public function test(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Get folders as a collection
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        $response = $this->sell->index();
        $data = (array) $response->getData();
        $data['folders'] = $folders;

        // Pass the merged data to the view
        return view('minireportb1::MiniReportB1.multitable.test', $data);
    }

    public function test2(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Fetch folders
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        // Fetch customers
        $customers = Contact::where('business_id', $business_id)
            ->where('type', 'customer') // Ensure you're fetching only customers
            ->pluck('name', 'id');

        // Fetch payment types
        $payment_types = $this->productUtil->payment_types(null, true, $business_id); // Adjust this based on your application's logic

        // Fetch purchase data
        $response = $this->purchase->index();
        $data = (array) $response->getData();

        // Merge all data
        $data['folders'] = $folders;
        $data['customers'] = $customers; // Add customers to the data array
        $data['payment_types'] = $payment_types; // Add payment types to the data array

        return view('minireportb1::MiniReportB1.multitable.test2', $data);
    }
}
