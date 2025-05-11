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
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use Modules\Essentials\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use Modules\Crm\Http\Controllers\ScheduleController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ManageUserController;


use App\Business;
use App\CustomerGroup;
use Razorpay\Api\Customer;

class MiniReportB1Controller extends Controller
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
    protected $followup;
    protected $customer;
    protected $account;
    protected $employee;


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
        ExpenseController $expense,
        ScheduleController $followup,
        ContactController $customer,
        AccountController $account,
        ManageUserController $employee,
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
        $this->followup = $followup;
        $this->customer = $customer;
        $this->account = $account;
        $this->employee = $employee;
    }


    function supplier(Request $request) // Remove "= null"
    {
        // Set the 'type' parameter to 'customer' in the request
        $request->merge(['type' => 'supplier']);

        // Call the index method
        $response = $this->customer->index();

        // Check if the response is a redirect
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response; // Or throw an exception
        }

        // If the response is a view, extract the data and pass it to your view
        if ($response instanceof \Illuminate\View\View) {
            $data = $response->getData(); // Get the data passed to the view
            return view('minireportb1::MiniReportB1.multitable.supplierReport', (array) $data);
        }

        // Handle other cases (e.g., JSON response)
        return $response;
    }


    function employee(Request $response)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->employee->index();
        // dd($response);
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.employeeReport', $data);
    }


    function account(Request $response)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->followup->index();
        // dd($response);
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.accountReport', $data);
    }

    function customer(Request $request) // Remove "= null"
    {
        // Set the 'type' parameter to 'customer' in the request
        $request->merge(['type' => 'customer']);

        // Call the index method
        $response = $this->customer->index();

        // Check if the response is a redirect
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response; // Or throw an exception
        }

        // If the response is a view, extract the data and pass it to your view
        if ($response instanceof \Illuminate\View\View) {
            $data = $response->getData(); // Get the data passed to the view
            return view('minireportb1::MiniReportB1.multitable.customerReport', (array) $data);
        }

        // Handle other cases (e.g., JSON response)
        return $response;
    }

    function followup(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->followup->index();
        // dd($response);
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.followupReport', $data);
    }

    function expense(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->expense->index();
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.exspenseReport', $data);
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

    public function getFolders()
    {
        // Retrieve the business_id from the session
        $business_id = request()->session()->get('user.business_id');

        // Query the folders based on business_id and type
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        // Return the folders as a JSON response
        return response()->json([
            'success' => true,
            'data' => $folders,
        ]);
    }

    public function productReport(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Get folders as a collection
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        $response = $this->product->index();
        $data = (array) $response->getData();
        $data['folders'] = $folders;

        // Pass the merged data to the view
        return view('minireportb1::MiniReportB1.multitable.productReport', $data);
    }

    public function saleReport(Request $request)
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
        return view('minireportb1::MiniReportB1.multitable.saleReport', $data);
    }


    public function purchaseReport(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Get folders as a collection
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        $response = $this->purchase->index();
        $data = (array) $response->getData();
        $data['folders'] = $folders;

        // Pass the merged data to the view
        return view('minireportb1::MiniReportB1.multitable.purchaseReport', $data);
    }

    function payroll(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->payroll->index();
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.payrollReport', $data);
    }

    function payroll1(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->payroll->index();
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.pay_componentsReport', $data);
    }

    function payroll2(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $response = $this->payroll->index();
        $data = (array) $response->getData();

        return view('minireportb1::MiniReportB1.multitable.payroll_groupReport', $data);
    }




    function testAll(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // 1. Fetch folders
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->where('type', 'report_section')
            ->get();

        // 2. Fetch data for Sales (similar to $this->sell->index())
        //    If your SellController has an index() that returns data in JSON or as an array,
        //    adapt accordingly. This is an example if index() is returning a JSON response.
        $response_sells = $this->sell->index();
        $data_sells = (array) $response_sells->getData();

        // 3. Fetch data for Purchases (similar to $this->purchase->index())
        $response_purchases = $this->purchase->index();
        $data_purchases = (array) $response_purchases->getData();

        // 4. Fetch other data needed (folders, customers, payment types, etc.)
        //    a) Folders already fetched above
        //    b) Customers:
        $customers = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');

        //    c) Payment types (adjust usage to match your actual logic)
        $payment_types = $this->productUtil->payment_types(null, true, $business_id);

        // 5. Merge all relevant data into a single array
        //    This approach merges the arrays from sells and purchases. 
        //    If you have name collisions (e.g., same array keys in both),
        //    rename or selectively merge them.
        $data = array_merge($data_sells, $data_purchases);

        // Add your custom data
        $data['folders']       = $folders;
        $data['customers']     = $customers;
        $data['payment_types'] = $payment_types;

        return view('minireportb1::MiniReportB1.multitable.test_all', $data);
    }


    public function getConsolidatedData()
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Initialize main data structure
            $consolidatedData = [
                'categories' => [],
                'dataMapping' => [],
            ];

            // 1. Get Purchase Data with limited fields
            $purchaseData = $this->getLimitedPurchaseData($business_id);
            $consolidatedData['categories']['purchase'] = $purchaseData['categories'];
            $consolidatedData['dataMapping'] = array_merge($consolidatedData['dataMapping'], $purchaseData['dataMapping']);

            // 2. Get Sales Data with limited fields
            $salesData = $this->getLimitedSalesData($business_id);
            $consolidatedData['categories']['sales'] = $salesData['categories'];
            $consolidatedData['dataMapping'] = array_merge($consolidatedData['dataMapping'], $salesData['dataMapping']);

            return $consolidatedData;
        } catch (\Exception $e) {            throw $e;
        }
    }

    protected function getLimitedSalesData($business_id)
    {
        $sales = $this->transactionUtil->getListSells($business_id);

        $sales->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
            ->leftJoin('contacts as customers', 'transactions.contact_id', '=', 'customers.id')
            ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id');

        // Add only essential select fields for sales
        $sales->select([
            'transactions.transaction_date',
            'transactions.invoice_no',
            'transactions.final_total',
            'business_locations.name as location_name',
            'customers.name as customer_name',
            DB::raw('SUM(transaction_payments.amount) as amount_paid')
        ]);

        $sales->groupBy('transactions.id')
            ->limit(5);  // Limit to 5 records

        $salesData = $sales->get();

        return [
            'categories' => [
                'date' => ['transaction_date'],
                'invoice' => ['invoice_no'],
                'customer_info' => ['customer_name'],
                'location' => ['location_name'],
                'financial' => ['final_total', 'amount_paid']
            ],
            'dataMapping' => $this->formatDataForMapping($salesData)
        ];
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
            $report_name = $layout['reportName'] ?? null;
            $visibleColumnNames = $layout['visibleColumnNames'] ?? [];
            $filterCriteria = $layout['filterCriteria'] ?? [];
            $tab = null;
        }
    
        // Fetch data based on the report name (if provided)
        switch ($report_name) {
            case "saleReport":
                $response = $this->sell->index();
                $data = (array) $response->getData();
                break;
    
            case "purchaseReport":
                $response = $this->purchase->index();
                $data = (array) $response->getData();
                break;
    
            case "productReport":
                $response = $this->product->index();
                $data = (array) $response->getData();
                break;
    
            case "PayRollReport":
                $response = $this->payroll->index();
                $data = (array) $response->getData();
                break;
    
            case "stockReport":
                $response = $this->product->index();
                $data = (array) $response->getData();
                break;
    
            case "expenseReport":
                $response = $this->expense->index();
                $data = (array) $response->getData();
                break;
    
            case "pay_componentsReport":
                $response = $this->payroll->index();
                $data = (array) $response->getData();
                break;
    
            case "followupReport":
                $response = $this->followup->index();
                $data = (array) $response->getData();
                $data['file_name'] = $file_name; // Use the original file_name
                $data['tab'] = "followupReport";
                break;
    
            case "recursiveFollowupReport":
                $response = $this->followup->index(); // Adjust if different method needed
                $data = (array) $response->getData();
                $data['file_name'] = $file_name; // Use the same file_name as followupReport
                $data['tab'] = "recursiveFollowupReport";
                break;
    
            case "customerReport":
                // Set the 'type' parameter to 'customer' in the request
                $request->merge(['type' => 'customer']);
    
                // Call the index method
                $response = $this->customer->index();
    
                // Check if the response is a redirect
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response; // Or throw an exception
                }
    
                // If the response is a view, extract the data and pass it to your view
                if ($response instanceof \Illuminate\View\View) {
                    $data = (array) $response->getData(); // Get the data passed to the view
                }
                break;

            case "supplierReport":
                // Set the 'type' parameter to 'customer' in the request
                $request->merge(['type' => 'supplier']);
    
                // Call the index method
                $response = $this->customer->index();
    
                // Check if the response is a redirect
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response; // Or throw an exception
                }
    
                // If the response is a view, extract the data and pass it to your view
                if ($response instanceof \Illuminate\View\View) {
                    $data = (array) $response->getData(); // Get the data passed to the view
                }
                break;
    
            case "employeeReport":
                $response = $this->employee->index();
                $data = (array) $response->getData();
                break;
    
            default:
                $data = [];
                break;
        }
    
        // Add folders, file-specific data, and visibleColumnNames to the response
        $data['folders'] = $folders;
        $data['file_name'] = $file_name; // Default assignment, overridden by switch if set
        $data['report_name'] = $report_name;
        $data['visibleColumnNames'] = $visibleColumnNames;
        $data['filterCriteria'] = $filterCriteria;
    
        // Override file_name for recursiveFollowupReport to match followupReport behavior
        if ($report_name === "recursiveFollowupReport") {
            $report_name = "followupReport"; // Ensure it uses the same file_name
        }
    
        // Dynamically load the view based on the report name
        $viewName = 'minireportb1::MiniReportB1.multitable.' . $report_name;
    
        // Check if the view exists
        if (!view()->exists($viewName)) {
            abort(404, "View for report '$report_name' not found.");
        }
    
        // Return the view with the merged data
        return view($viewName, $data);
    }

    protected function getLimitedPurchaseData($business_id)
    {
        $purchases = $this->transactionUtil->getListPurchases($business_id);

        $purchases->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
            ->leftJoin('contacts as suppliers', 'transactions.contact_id', '=', 'suppliers.id')
            ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id');

        // Add only essential select fields for purchases
        $purchases->select([
            'transactions.transaction_date',
            'transactions.ref_no',
            'transactions.final_total',
            'business_locations.name as location_name',
            'suppliers.name as supplier_name',
            DB::raw('SUM(transaction_payments.amount) as amount_paid')
        ]);

        $purchases->groupBy('transactions.id')
            ->limit(5);  // Limit to 5 records

        $purchaseData = $purchases->get();

        return [
            'categories' => [
                'date' => ['transaction_date'],
                'reference' => ['ref_no'],
                'supplier_info' => ['supplier_name'],
                'location' => ['location_name'],
                'financial' => ['final_total', 'amount_paid']
            ],
            'dataMapping' => $this->formatDataForMapping($purchaseData)
        ];
    }

    public function indexs()
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            // Get consolidated data
            $consolidatedData = $this->getConsolidatedData();

            // Get folders for the view
            $folders = MiniReportB1Folder::where('business_id', $business_id)
                ->where('type', 'report_section')
                ->get();

            // Prepare additional view data
            $viewData = [
                'folders' => $folders,
                'rawData' => $consolidatedData,
                'business_locations' => BusinessLocation::forDropdown($business_id),
                'orderStatuses' => $this->productUtil->orderStatuses(),
                'payment_status_array' => $this->transactionUtil->payment_status_dropdown(),
                'expense_categories' => ExpenseCategory::where('business_id', $business_id)->pluck('name', 'id')
            ];

            // Return view with data
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $consolidatedData,
                ]);
            }

            return view('minireportb1::MiniReportB1.create', $viewData);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
            }

            return back()->with('status', [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ]);
        }
    }

    public function dashboard()
    {
        // Get the business ID from the session
        $business_id = request()->session()->get('user.business_id');

        // Check if the user is authorized to access this module


        // Fetch the business details
        $business = Business::findOrFail($business_id);

        // Get the totals
        $total_minireportb1 = MiniReportB1::where('business_id', $business_id)->count();
        $total_minireportb1_category = MiniReportB1Category::where('business_id', $business_id)->count();

        // Get category data
        $minireportb1_category = DB::table('minireportb1_main as minireportb1')
            ->leftJoin('minireportb1_category as minireportb1category', 'minireportb1.category_id', '=', 'minireportb1category.id')
            ->select(
                DB::raw('COUNT(minireportb1.id) as total'),
                'minireportb1category.name as category'
            )
            ->where('minireportb1.business_id', $business_id)
            ->groupBy('minireportb1category.id')
            ->get();

        // Get folders ordered by their order field
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->orderBy('order')
            ->get();

        // Get files
        $files = MiniReportB1File::where('business_id', $business_id)->get();

        // Construct the full logo path if it exists
        $business_logo = $business->logo ? '/uploads/business_logos/' . $business->logo : null;

        // Return the view with all necessary data
        return view('minireportb1::MiniReportB1.dashboard')
            ->with(array_merge(
                compact(
                    'total_minireportb1',
                    'total_minireportb1_category',
                    'minireportb1_category',
                    'folders',
                    'files'
                ),
                [
                    'logo' => $business->logo, // Raw logo filename for reference if needed
                    'business_logo' => $business_logo, // Full path or null
                    'business_name' => $business->name,
                ]
            ));
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        // $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        

        // Get the totals
        $total_minireportb1 = MiniReportB1::where('business_id', $business_id)->count();
        $total_minireportb1_category = MiniReportB1Category::where('business_id', $business_id)->count();

        // Get category data
        $minireportb1_category = DB::table('minireportb1_main as minireportb1')
            ->leftJoin('minireportb1_category as minireportb1category', 'minireportb1.category_id', '=', 'minireportb1category.id')
            ->select(
                DB::raw('COUNT(minireportb1.id) as total'),
                'minireportb1category.name as category'
            )
            ->where('minireportb1.business_id', $business_id)
            ->groupBy('minireportb1category.id')
            ->get();

        // Get folders ordered by their order field
        $folders = MiniReportB1Folder::where('business_id', $business_id)
            ->orderBy('order')
            ->get();

        // Get files
        $files = MiniReportB1File::where('business_id', $business_id)->get();

        return view('minireportb1::MiniReportB1.dashboard')
            ->with(compact(
                'total_minireportb1',
                'total_minireportb1_category',
                'minireportb1_category',
                // 'module',
                'folders',
                'files'
            ));
    }

    /**
     * Store a newly created report
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');

            // Get the raw request content
            $rawContent = $request->getContent();

            // Log the raw content for debugging
            // Decode the JSON content manually to ensure proper handling
            $input = json_decode($rawContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {                return response()->json([
                    'success' => false,
                    'msg' => 'Invalid JSON data: ' . json_last_error_msg()
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Validate required fields
            if (empty($input['file_name'])) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Report name is required'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            // Create the report
            $report = new MiniReportB1();
            $report->business_id = $business_id;
            $report->name = $input['file_name'];
            $report->parent_id = $input['parent_id'] ?? null;

            // Handle table_data
            $tableData = $input['table_data'];

            // If table_data is a string (already JSON), decode it first
            if (is_string($tableData)) {
                $tableData = json_decode($tableData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {                    // Use it as is if decode fails
                    $tableData = $input['table_data'];
                }
            }

            // Store the data as JSON with options to preserve Unicode characters
            $report->data = json_encode($tableData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

            // Log the encoded data for debugging
            // Save the report
            $report->save();

            return response()->json([
                'success' => true,
                'msg' => 'Report saved successfully',
                'report_id' => $report->id
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to save report: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        

        $minireportb1 = MiniReportB1::where('business_id', $business_id)->findOrFail($id);

        return view('minireportb1::MiniReportB1.show')->with(compact('minireportb1'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'file_name' => 'required|string|max:255',
                'parent_id' => 'required|integer',
                'table_data' => 'required|array'
            ]);

            $business_id = request()->session()->get('user.business_id');

            DB::beginTransaction();

            $file = new MiniReportB1File();
            $file->business_id = $business_id;
            $file->file_name = $request->file_name;
            $file->parent_id = $request->parent_id;
            $file->layout = json_encode($request->table_data);

            if (!$file->save()) {
                throw new \Exception('Failed to save file record');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'File created successfully',
                'data' => [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'redirect_url' => route('MiniReportB1.viewFile', $file->id)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'msg' => 'Error saving file: ' . $e->getMessage()
            ], 500);
        }
    }
    

    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type');
        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);





        $minireportb1 = MiniReportB1::find($id);
        $minireportb1_categories = MiniReportB1Category::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
            ->where('type', 'supplier')
            ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
            ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        return view('minireportb1::MiniReportB1.edit', compact('minireportb1', 'minireportb1_categories', 'users', 'customer', 'supplier', 'product', 'business_locations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'minireportb1_category_id' => 'nullable|integer',






            'Date_1' => 'nullable',


            'Title_1' => 'nullable',

        ]);

        try {
            $minireportb1 = MiniReportB1::find($id);
            $minireportb1->category_id = $request->minireportb1_category_id;
            $minireportb1->created_by = auth()->user()->id;






            $minireportb1->{'Date_1'} = $request->{'Date_1'};


            $minireportb1->{'Title_1'} = $request->{'Title_1'};



            $minireportb1->save();


            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }



    public function destroy($id)
    {
        try {
            MiniReportB1::destroy($id);
            return response()->json(['success' => true, 'msg' => __('minireportb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'minireportb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);



        if (request()->ajax()) {
            $categories = MiniReportB1Category::where('business_id', $business_id)->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('MiniReportB1-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('MiniReportB1-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('minireportb1::Category.index')->with(compact('module'));
    }
    public function viewFile($id)
    {
        try {
            // Get business ID from session
            $business_id = request()->session()->get('user.business_id');
            // Basic file retrieval
            $file = MiniReportB1File::where('id', $id)
                ->where('business_id', $business_id)
                ->first();

            if (!$file) {                throw new \Exception('File not found or unauthorized');
            }

            // Decode JSON layout
            $layout = json_decode($file->layout, true);
            if (json_last_error() !== JSON_ERROR_NONE) {                throw new \Exception('Invalid file layout format');
            }

            $visibleColumnNames = $layout['visibleColumnNames'] ?? [];
            $businessLocations = DB::table('business_locations')
                ->where('business_id', $business_id)
                ->pluck('name', 'id');


            // Build basic query
            $query = DB::table('transactions')
                ->select([
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'transactions.final_total',
                    'transactions.payment_status',
                    'contacts.name as customer_name',
                    'contacts.mobile as contact_number',
                    'business_locations.name as location_name',
                    DB::raw('GROUP_CONCAT(DISTINCT transaction_payments.method) as payment_methods'),
                    DB::raw('COALESCE(SUM(transaction_payments.amount), 0) as total_paid')
                ])
                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->groupBy(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'transactions.final_total',
                    'transactions.payment_status',
                    'contacts.name',
                    'contacts.mobile',
                    'business_locations.name'
                );
            // Execute query
            $results = $query->get();
            // Format the results
            $formattedRows = [];
            foreach ($results as $result) {
                $row = [];
                foreach ($visibleColumnNames as $columnName) {
                    $value = $this->getFormattedValue($result, $columnName);
                    $row[] = [
                        'value' => $value,
                        'align' => in_array($columnName, ['Total Amount', 'Total Paid', 'Sell Due']) ? 'right' : 'left'
                    ];
                }
                $formattedRows[] = $row;
            }

            $customers = Contact::customersDropdown($business_id, false);
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            // Return the view
            return view('minireportb1::MiniReportB1.view', [
                'file' => $file,
                'visibleColumnNames' => $visibleColumnNames,
                'paginatedRows' => $formattedRows,
                'business_locations' => $businessLocations,
                'customers' => $customers,
                'payment_types' => '$payment_types'
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('minireportb1.index')
                ->with('error', 'Error viewing file: ' . $e->getMessage());
        }
    }

    private function getFormattedValue($result, $columnName)
    {
        switch ($columnName) {
            case 'Date':
                return $result->transaction_date ? date('Y-m-d', strtotime($result->transaction_date)) : '';
            case 'Invoice No':
                return $result->invoice_no ?? '';
            case 'Customer Name':
                return $result->customer_name ?? '';
            case 'Contact No':
                return $result->contact_number ?? '';
            case 'Location':
                return $result->location_name ?? '';
            case 'Payment Status':
                return $result->payment_status ?? '';
            case 'Payment Method':
                return $result->payment_methods ?? '';
            case 'Total Amount':
                return number_format($result->final_total ?? 0, 2);
            case 'Total Paid':
                return number_format($result->total_paid ?? 0, 2);
            case 'Sell Due':
                $due = ($result->final_total ?? 0) - ($result->total_paid ?? 0);
                return number_format($due, 2);
            default:
                return '';
        }
    }

    private function formatPaymentStatus($status)
    {
        $labels = [
            'paid' => '<span class="badge badge-success">Paid</span>',
            'due' => '<span class="badge badge-danger">Due</span>',
            'partial' => '<span class="badge badge-warning">Partial</span>'
        ];

        return $labels[$status] ?? $status;
    }

    private function formatAmount($amount)
    {
        return number_format($amount, 2);
    }

    private function getColumnAlignment($columnName)
    {
        $rightAligned = [
            'Total Amount',
            'Total Paid',
            'Sell Due',
            'Sell Return Due',
            'Total Items'
        ];

        return in_array($columnName, $rightAligned) ? 'right' : 'left';
    }






    private function getDataInternal()
    {
        $business_id = request()->session()->get('user.business_id');

        // Retrieve the file ID from the session
        $file_id = request()->session()->get('current_file_id');
        if (!$file_id) {
            return ['error' => 'File ID not found in session.'];
        }

        // Retrieve the file with validation
        $file = MiniReportB1File::where('business_id', $business_id)
            ->where('id', $file_id)
            ->first();
        if (!$file) {
            return ['error' => 'File not found.'];
        }

        // Decode layout JSON with validation
        $layout = json_decode($file->layout, true);
        if (!is_array($layout)) {
            return ['error' => 'Invalid layout format in the file record.'];
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
        $totalPages = ceil($totalRows / $perPage);

        return [
            'headerRows' => $headerRows,
            'rows' => $paginatedRows,
            'totalRows' => $totalRows,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'perPage' => $perPage,
            'columnVisibility' => $columnVisibility,
            'error' => null,
        ];
    }
    /**
     * Get dynamic fields for filtering.
     */
    private function getDynamicFields($headerRows)
    {
        $dynamicFields = [];

        foreach ($headerRows as $headerRow) {
            foreach ($headerRow as $header) {
                $field = $header['field'] ?? null;
                if ($field) {
                    $dynamicFields[$field] = ucfirst(str_replace('_', ' ', $field));
                }
            }
        }

        return $dynamicFields;
    }

    /**
     * Get possible values for each field.
     */
    private function getFieldOptions($rows, $dynamicFields)
    {
        $fieldOptions = [];

        foreach ($dynamicFields as $field => $label) {
            $options = [];
            foreach ($rows as $row) {
                if (isset($row[$field])) {
                    $options[$row[$field]] = $row[$field];
                }
            }
            $fieldOptions[$field] = array_unique($options);
        }

        return $fieldOptions;
    }

    private function mergeRawDataWithRows($rows, $rawData, $dataMapping)
    {
        $rawDataArray = $rawData->toArray();
        $mergedRows = [];

        foreach ($rawDataArray as $dataRow) {
            $newRow = [];
            foreach ($rows[0] as $cell) { // Use first row as template
                $newCell = array_merge([], $cell); // Clone cell structure

                // Get field name from cell
                $fieldName = $cell['field'] ?? null;
                if ($fieldName && isset($dataRow->$fieldName)) {
                    $value = $dataRow->$fieldName;

                    // Format based on field type
                    if (str_contains($fieldName, 'date')) {
                        $newCell['value'] = date('Y-m-d', strtotime($value));
                    } elseif (str_contains($fieldName, 'amount') || str_contains($fieldName, 'total')) {
                        $newCell['value'] = number_format((float)$value, 2);
                    } else {
                        $newCell['value'] = $value;
                    }
                }

                $newRow[] = $newCell;
            }
            $mergedRows[] = $newRow;
        }

        return $mergedRows;
    }

    protected function applyColumnFilter($rawData, $columns)
    {
        try {
            $filteredData = [];

            foreach ($rawData['data'] as $row) {
                $filteredRow = [];
                foreach ($columns as $column) {
                    $field = $column['field'];
                    $filteredRow[$field] = $row[$field] ?? '';
                }
                $filteredData[] = $filteredRow;
            }

            return $filteredData;
        } catch (\Exception $e) {            return [];
        }
    }
}
