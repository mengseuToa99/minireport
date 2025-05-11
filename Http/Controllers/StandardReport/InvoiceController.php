<?php

namespace Modules\MiniReportB1\Http\Controllers\StandardReport;

use App\Business;
use App\Contact;
use App\ExchangeRate;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MiniReportB1\Http\Services\DateFilterService;
use App\BusinessLocation;

class InvoiceController extends Controller
{
    protected $moduleUtil;
    protected $businessUtil;
    protected $dateFilterService;

    /**
     * Constructor
     */
    public function __construct(
        ModuleUtil $moduleUtil,
        BusinessUtil $businessUtil,
        DateFilterService $dateFilterService
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->dateFilterService = $dateFilterService;
    }

    /**
     * Display office rental receipt
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function getOfficeReceipt(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        // Get date range
        $dateRange = app(DateFilterService::class)->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];
        
        // Format date for display
        $display_date = $end_date->format('d F Y');
        
        // Get location filter
        $location_id = $request->input('location_id');
        
        // Get expense category filter
        $expense_category_id = $request->input('expense_category_id');
        
        // Get transaction filter
        $transaction_id = $request->input('transaction_id');
        
        // Get business locations
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // Get expense categories
        $expense_categories = \App\ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        
        // Query expenses
        $expenses = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
            ->leftJoin('expense_categories AS esc', 'transactions.expense_sub_category_id', '=', 'esc.id')
            ->join('business_locations AS bl', 'transactions.location_id', '=', 'bl.id')
            ->leftJoin('contacts AS c', 'transactions.contact_id', '=', 'c.id')
            ->leftJoin('users AS U', 'transactions.expense_for', '=', 'U.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.status', 'final')
            ->whereIn('transactions.type', ['expense'])
            ->whereBetween('transactions.transaction_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')]);
        
        // Apply location filter if provided
        if (!empty($location_id)) {
            $expenses->where('transactions.location_id', $location_id);
        }
        
        // Apply expense category filter if provided
        if (!empty($expense_category_id)) {
            $expenses->where('transactions.expense_category_id', $expense_category_id);
        }
        
        // Apply transaction filter if provided
        if (!empty($transaction_id)) {
            $expenses->where('transactions.id', $transaction_id);
        }
        
        $expenses = $expenses->select(
            'transactions.id as №',
            'transactions.transaction_date',
            DB::raw("CONCAT(COALESCE(ec.name, ''), IF(esc.name IS NOT NULL, CONCAT(' - ', esc.name), '')) as DESCRIPTION"),
            'transactions.additional_notes as បរិយាយ',
            DB::raw('1 as បរិមាណ'), // Default quantity as 1
            DB::raw("'Unit' as Unit"), // Static unit value
            DB::raw("ROUND(transactions.final_total, 2) as តម្លៃរាយ"),
            DB::raw("ROUND(transactions.final_total, 2) as 'Unit Price'"),
            DB::raw("ROUND(transactions.final_total, 2) as ទឹកប្រាក់"),
            DB::raw("COALESCE(
                c.name, 
                CONCAT(COALESCE(U.surname, ''), ' ', COALESCE(U.first_name, ''), ' ', COALESCE(U.last_name, ''))
            ) as customer_name"),
            DB::raw("'' as customer_address"),
            DB::raw("COALESCE(c.mobile, U.contact_number) as customer_phone"),
            'bl.name as location_name'
        )
        ->groupBy('transactions.id')
        ->get();
        
        // Get available transactions for the filter dropdown
        $transactions = $this->getFilteredTransactions($business_id, $location_id, $expense_category_id, $start_date, $end_date);
        
        // If no transaction selected, use the first one from the results
        $selected_transaction = null;
        if (!empty($transaction_id)) {
            $selected_transaction = $expenses->where('№', $transaction_id)->first();
        } else if ($expenses->count() > 0) {
            $selected_transaction = $expenses->first();
        }
        
        // Get customer data
        $customer_data = null;
        if ($selected_transaction) {
            $customer_data = [
                'name' => $selected_transaction->customer_name,
                'address' => $selected_transaction->customer_address,
                'phone' => $selected_transaction->customer_phone
            ];
            
            // Get exchange rate for the transaction date
            $transaction_date = Carbon::parse($selected_transaction->transaction_date);
            
            // Try to get exchange rate for the transaction date
            $exchangeRate = ExchangeRate::where('business_id', $business_id)
                ->whereDate('date_1', $transaction_date->format('Y-m-d'))
                ->first();
            
            // If no exact match, get the closest rate before the transaction date
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->where('date_1', '<=', $transaction_date->format('Y-m-d'))
                    ->orderBy('date_1', 'desc')
                    ->first();
            }
            
            // If still no rate, get the latest rate
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->orderBy('date_1', 'desc')
                    ->first();
            }
            
            $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;
            
            // Set description and amount
            $description = $selected_transaction->DESCRIPTION;
            $amount = $selected_transaction->តម្លៃរាយ;
            $quantity = $selected_transaction->បរិមាណ;
            $total_amount = $amount * $quantity;
            $total_amount_khr = $total_amount * $exchange_rate;
        } else {
            // Default values if no transaction found
            $description = '';
            $amount = 0;
            $quantity = 1;
            $total_amount = 0;
            $exchange_rate = 4100;
            $total_amount_khr = 0;
        }
        
        // Get business information
        $business = Business::findOrFail($business_id);
        
        $view = view('minireportb1::MiniReportB1.StandardReport.Invoice.office_receipt', compact(
            'business_locations',
            'display_date',
            'customer_data',
            'description',
            'amount',
            'quantity',
            'total_amount',
            'exchange_rate',
            'total_amount_khr',
            'transactions',
            'transaction_id',
            'business',
            'expense_categories',
            'expense_category_id'
        ));

        if ($request->ajax()) {
            return $view;
        }
        
        return $view;
    }
    
    /**
     * Get transactions filtered by location and expense category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactions(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        // Get location filter
        $location_id = $request->input('location_id');
        
        // Get expense category filter
        $expense_category_id = $request->input('expense_category_id');
        
        // Calculate date range (default to current month)
        $start_date = Carbon::now()->startOfMonth();
        $end_date = Carbon::now()->endOfMonth();
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = Carbon::parse($request->input('start_date'));
            $end_date = Carbon::parse($request->input('end_date'));
        } else if ($request->has('date_filter')) {
            $dateRange = app(DateFilterService::class)->calculateDateRange($request);
            $start_date = $dateRange['start_date'];
            $end_date = $dateRange['end_date'];
        }
        
        $transactions = $this->getFilteredTransactions($business_id, $location_id, $expense_category_id, $start_date, $end_date);
        
        return response()->json(['transactions' => $transactions]);
    }
    
    /**
     * Helper method to get filtered transactions
     * 
     * @param int $business_id
     * @param int|null $location_id
     * @param int|null $expense_category_id
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @return \Illuminate\Support\Collection
     */
    private function getFilteredTransactions($business_id, $location_id = null, $expense_category_id = null, $start_date = null, $end_date = null)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('status', 'final')
            ->whereIn('type', ['expense']);
        
        if ($start_date && $end_date) {
            $query->whereBetween('transaction_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')]);
        }
        
        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }
        
        if (!empty($expense_category_id)) {
            $query->where('expense_category_id', $expense_category_id);
        }
        
        return $query->select('id', DB::raw("CONCAT(id, ' - ', ref_no) as display_name"))
            ->orderBy('id', 'desc')
            ->pluck('display_name', 'id');
    }
} 