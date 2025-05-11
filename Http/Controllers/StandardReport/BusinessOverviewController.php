<?php

namespace Modules\MiniReportB1\Http\Controllers\StandardReport;

use App\Account;
use App\AccountType;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Accounting\Utils\AccountingUtil;
use PDF;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ExpenseCategory;
use App\TaxRate;
use Spatie\LaravelIgnition\Support\Composer\FakeComposer;
use Faker\Factory as Faker;
use Modules\MiniReportB1\Http\Services\DateFilterService;
use App\Charts\CommonChart;
use App\CustomerGroup;
use App\ExchangeRate;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use Carbon\Carbon;

class BusinessOverviewController extends Controller
{


    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;

    protected $dateFilterService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */



    public function __construct(DateFilterService $dateFilterService, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->dateFilterService = $dateFilterService;
    }


    public function getBankbook(Request $request, DateFilterService $dateFilterService)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range from DateFilterService
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        if ($request->ajax()) {
            $query = \App\AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', '=', 'accounts.id')
                ->leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', '=', 'transaction_payments.id')
                ->leftjoin('contacts', 'transaction_payments.payment_for', '=', 'contacts.id')
                ->leftjoin('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
                ->where('accounts.business_id', $business_id)
                ->select([
                    'account_transactions.operation_date as date',
                    'transaction_payments.payment_ref_no as voucher_no',
                    'accounts.name as payee', // Changed to contacts.name for actual payee name
                    'account_transactions.note as description', // Changed to transaction note
                    'accounts.account_number as ac_code',
                    DB::raw("IF(account_transactions.type='credit', account_transactions.amount, 0) as cash_in"),
                    DB::raw("IF(account_transactions.type='debit', account_transactions.amount, 0) as expense"),
                    DB::raw("SUM(IF(account_transactions.type='credit', account_transactions.amount, -1 * account_transactions.amount)) OVER (ORDER BY account_transactions.operation_date, account_transactions.id) as balance"),
                    'transactions.invoice_no',
                    'transactions.ref_no',
                    'account_transactions.operation_date' // Added to help with exchange rate lookup
                ])
                ->whereBetween('account_transactions.operation_date', [$start_date, $end_date]);

            // Filter by account if requested
            if ($request->filled('account_id')) {
                $query->where('accounts.id', $request->account_id);
            }

            // Filter by payee if requested
            if ($request->filled('payee_filter')) {
                $query->where('contacts.name', 'like', '%' . $request->payee_filter . '%');
            }

            $data = $query->get();
            
            // Get exchange rates for the date range
            $exchange_rates = [];
            
            // Always include exchange rates regardless of request parameter
            // Create a mapping of transaction dates to their exchange rates
            foreach ($data as $transaction) {
                $transaction_date = $transaction->date;
                
                // Skip if we already have an exchange rate for this date
                if (isset($exchange_rates[$transaction_date])) {
                    continue;
                }
                
                // Try to get exchange rate for the exact transaction date
                $exchangeRate = \App\ExchangeRate::where('business_id', $business_id)
                    ->whereDate('date_1', $transaction_date)
                    ->first();
                
                if ($exchangeRate) {
                    $exchange_rates[$transaction_date] = $exchangeRate->KHR_3;
                } else {
                    // If no exact match, try the closest previous date
                    $exchangeRate = \App\ExchangeRate::where('business_id', $business_id)
                        ->where('date_1', '<', $transaction_date)
                        ->orderBy('date_1', 'desc')
                        ->first();
                        
                    // If still no match, use default rate (4100)
                    $exchange_rates[$transaction_date] = $exchangeRate ? $exchangeRate->KHR_3 : 4100;
                }
            }
            
            // Add exchange rate to each transaction
            $formattedData = $data->map(function ($item) use ($exchange_rates) {
                // Clean the description field by removing HTML tags
                $item->description = $item->description ? strip_tags($item->description) : 'Bank Transaction';

                // Ensure voucher_no falls back to invoice_no or ref_no if empty
                $item->voucher_no = $item->voucher_no ?: $item->invoice_no ?: $item->ref_no ?: '';
                
                // Always add exchange rate
                $item->exchange_rate = $exchange_rates[$item->date] ?? 4100; // Default to 4100 if no rate is found

                return $item;
            });

            return response()->json([
                'data' => $formattedData,
                'total' => $formattedData->count(),
                'exchange_rates' => $exchange_rates
            ]);
        }

        $accounts = Account::forDropdown($business_id, false);
        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.bankbook')
            ->with(compact('accounts', 'start_date', 'end_date'));
    }


    public function QuarterlyReport(Request $request)
    {
        if (!auth()->user()->can('quickbooks.Supplier&Customer_Report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Debugging: Log the request parameters
        // Return the details in ajax call
        if ($request->ajax()) {
            // Calculate the start date for the last 3 months
            $threeMonthsAgo = now()->subMonths(3)->startOfDay();

            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->leftJoin('business_locations AS bl', 't.location_id', '=', 'bl.id') // Join business_locations table
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='sell_discount', final_total, 0)) as total_ledger_discount_sell"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='purchase_discount', final_total, 0)) as total_ledger_discount_purchase"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.mobile', // Include contact number
                    'contacts.id',
                    'contacts.type as contact_type',
                    'contacts.city', // Include city
                    'contacts.state', // Include state
                    'contacts.country' // Include country
                );

            // Handle 'last_3_months' filter if provided
            if ($request->has('last_3_months') && $request->input('last_3_months') == 1) {
                $contacts->where('t.transaction_date', '>=', $threeMonthsAgo);
            }

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            // Apply customer group filter
            if (!empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }

            // Apply location filter
            if (!empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            // Apply contact filter
            if (!empty($request->input('contact_id'))) {
                $contacts->where('t.contact_id', $request->input('contact_id'));
            }

            // Apply contact type filter
            if (!empty($request->input('contact_type'))) {
                $contacts->whereIn('contacts.type', [$request->input('contact_type'), 'both']);
            }

            // Handle date range filter
            if (!empty($request->input('date_range'))) {
                $dates = explode(' - ', $request->input('date_range'));
                if (count($dates) == 2) {
                    $start_date = $dates[0];
                    $end_date = $dates[1];
                    $contacts->whereBetween('t.transaction_date', [$start_date, $end_date]);
                }
            }

            // Apply total sale filter
            if ($request->has('total_sale_filter') && !empty($request->input('total_sale_filter'))) {
                $totalSaleFilter = $request->input('total_sale_filter');
                $contacts->having('total_invoice', '>', $totalSaleFilter);
            }

            // Debugging: Log the final SQL query with bindings
            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    // Display supplier_business_name before name
                    $name = '';
                    if (!empty($row->supplier_business_name)) {
                        $name .= $row->supplier_business_name . ', ';
                    }
                    $name .= $row->name;

                    return '<a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]) . '" target="_blank" class="no-print">' .
                        $name .
                        '</a>';
                })
                ->editColumn(
                    'total_purchase',
                    function ($row) {
                        return '<span class="total_purchase" data-orig-value="' . $row->total_purchase . '">' .
                            number_format($row->total_purchase, 2) . '</span>';
                    }
                )
                ->editColumn(
                    'total_purchase_return',
                    function ($row) {
                        return '<span class="total_purchase_return" data-orig-value="' . $row->total_purchase_return . '">' .
                            number_format($row->total_purchase_return, 2) . '</span>';
                    }
                )
                ->editColumn(
                    'total_sell_return',
                    function ($row) {
                        return '<span class="total_sell_return" data-orig-value="' . $row->total_sell_return . '">' .
                            number_format($row->total_sell_return, 2) . '</span>';
                    }
                )
                ->editColumn(
                    'total_invoice',
                    function ($row) {
                        return '<span class="total_invoice" data-orig-value="' . $row->total_invoice . '">' .
                            number_format($row->total_invoice, 2) . '</span>';
                    }
                )
                ->addColumn('due', function ($row) {
                    $total_ledger_discount_sell = $row->total_ledger_discount_sell ?? 0;
                    $total_ledger_discount_purchase = $row->total_ledger_discount_purchase ?? 0;
                    $due = ($row->total_invoice - $row->invoice_received - $total_ledger_discount_sell)
                        - ($row->total_purchase - $row->purchase_paid - $total_ledger_discount_purchase)
                        - ($row->total_sell_return - $row->sell_return_paid)
                        + ($row->total_purchase_return - $row->purchase_return_received);

                    if ($row->contact_type == 'supplier') {
                        $due -= ($row->opening_balance - $row->opening_balance_paid);
                    } else {
                        $due += ($row->opening_balance - $row->opening_balance_paid);
                    }

                    $due_formatted = number_format($due, 2); // Assuming you have a formatter
    
                    return '<span class="total_due" data-orig-value="' . $due . '">' . $due_formatted . '</span>';
                })
                ->addColumn(
                    'opening_balance_due',
                    function ($row) {
                        $opening_balance_due = $row->opening_balance - $row->opening_balance_paid;
                        return '<span class="opening_balance_due" data-orig-value="' . $opening_balance_due . '">' .
                            number_format($opening_balance_due, 2) . '</span>';
                    }
                )
                ->addColumn('city', function ($row) {
                    return $row->city ?? 'N/A'; // Display city
                })
                ->addColumn('state', function ($row) {
                    return $row->state ?? 'N/A'; // Display state
                })
                ->addColumn('country', function ($row) {
                    return $row->country ?? 'N/A'; // Display country
                })
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'opening_balance_due'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $types = [
            '' => __('lang_v1.all'),
            'customer' => __('report.customer'),
            'supplier' => __('report.supplier'),
        ];

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('minireportb1::MiniReportB1.StandardReport.ProductReports.quarterly_report') // Ensure the view name is correct
            ->with(compact('customer_group', 'types', 'business_locations', 'contact_dropdown'));
    }


    public function balanceSheet()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $account_types = AccountType::accounting_primary_type();
        $business_id = session()->get('user.business_id');

        if (request()->ajax()) {
            $accounts = Account::leftjoin('account_transactions as AT', function ($join) {
                $join->on('AT.account_id', '=', 'accounts.id')
                    ->whereNull('AT.deleted_at');
            })
                ->leftjoin('account_types as ats', 'accounts.account_sub_type_id', '=', 'ats.id')
                ->leftJoin('account_types as atd', 'accounts.detail_type_id', '=', 'atd.id')
                ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')
                ->where('accounts.business_id', $business_id)
                ->select([
                    'accounts.name',
                    'accounts.account_number',
                    'accounts.description',
                    'accounts.id',
                    'accounts.account_type_id',
                    'accounts.account_details',
                    'accounts.is_closed',
                    'ats.name as account_sub_type_name',
                    'atd.name as account_detail_type_name',
                    DB::raw("CASE
                        WHEN ats.account_type = 'sub_type' THEN 'Sub Type'
                        WHEN ats.account_type = 'detail_type' THEN 'Detail Type'
                        ELSE 'Unknown'
                    END AS account_sub_type"),
                    DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance"),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                ]);

            // Check account permissions based on location
            $permitted_locations = auth()->user()->permitted_locations();
            $account_ids = [];
            if ($permitted_locations != 'all') {
                $locations = BusinessLocation::where('business_id', $business_id)
                    ->whereIn('id', $permitted_locations)
                    ->get();

                foreach ($locations as $location) {
                    if (!empty($location->default_payment_accounts)) {
                        $default_payment_accounts = json_decode($location->default_payment_accounts, true);
                        foreach ($default_payment_accounts as $key => $account) {
                            if (!empty($account['is_enabled']) && !empty($account['account'])) {
                                $account_ids[] = $account['account'];
                            }
                        }
                    }
                }

                $account_ids = array_unique($account_ids);
            }

            if (!$this->moduleUtil->is_admin(auth()->user(), $business_id) && $permitted_locations != 'all') {
                $accounts->whereIn('accounts.id', $account_ids);
            }

            $is_closed = request()->input('account_status') == 'closed' ? 1 : 0;
            $accounts->where('is_closed', $is_closed)
                ->groupBy('accounts.id');


            return DataTables::of($accounts)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\AccountController@edit\',[$id])}}" data-container=".account_model" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                <a href="{{action(\'App\Http\Controllers\AccountController@show\',[$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-warning btn-xs"><i class="fa fa-book"></i> @lang("account.account_book")</a>&nbsp;
                @if($is_closed == 0)
                <button data-href="{{action(\'App\Http\Controllers\AccountController@getFundTransfer\',[$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info btn-modal" data-container=".view_modal"><i class="fa fa-exchange"></i> @lang("account.fund_transfer")</button>
                <button data-href="{{action(\'App\Http\Controllers\AccountController@getDeposit\',[$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-success btn-modal" data-container=".view_modal"><i class="fas fa-money-bill-alt"></i> @lang("account.deposit")</button>
                <button data-url="{{action(\'App\Http\Controllers\AccountController@close\',[$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error close_account"><i class="fa fa-power-off"></i> @lang("messages.close")</button>
                @elseif($is_closed == 1)
                    <button data-url="{{action(\'App\Http\Controllers\AccountController@activate\',[$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-success activate_account"><i class="fa fa-power-off"></i> @lang("messages.activate")</button>
                @endif'
                )
                ->editColumn('name', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->name . ' <small class="label pull-right bg-red no-print">' . __('account.closed') . '</small><span class="print_section">(' . __('account.closed') . ')</span>';
                    } else {
                        return $row->name;
                    }
                })
                ->editColumn('balance', function ($row) {
                    return '<span class="balance" data-orig-value="' . $row->balance . '">' . $this->commonUtil->num_f($row->balance, true) . '</span>';
                })
                ->editColumn('account_type', function ($row) {
                    $account_type_mapping = [
                        1 => 'asset',
                        2 => 'expenses',
                        3 => 'income',
                        4 => 'equity',
                        5 => 'liability',
                    ];

                    // Get the numeric value for the account type
                    $account_type_numeric = $account_type_mapping[$row->account_type_id] ?? 'N/A';
                    $account_type = $account_type_numeric;

                    return $account_type;
                })
                ->editColumn('parent_account_type_name', function ($row) {
                    return $row->parent_account_type_name ?? '';
                })
                ->editColumn('account_sub_type_name', function ($row) {

                    if ($row->account_sub_type === 'Sub Type') {
                        return $row->account_sub_type_name;
                    }
                })
                ->editColumn('account_detail_type', function ($row) {
                    return $row->account_detail_type_name; // Directly return the already selected detail type name
                })
                ->editColumn('account_details', function ($row) {
                    $html = '';
                    if (!empty($row->account_details)) {
                        foreach ($row->account_details as $account_detail) {
                            if (!empty($account_detail['label']) && !empty($account_detail['value'])) {
                                $html .= $account_detail['label'] . ' : ' . $account_detail['value'] . '<br>';
                            }
                        }
                    }

                    return $html;
                })
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['action', 'balance', 'name', 'account_details', 'account_sub_type_name', 'account_detail_type'])
                ->make(true);
        }
        $account_exist = Account::where('business_id', $business_id)->exists();
        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        )
            ->whereNull('transaction_payments.parent_id')
            ->where('method', '!=', 'advance')
            ->where('transaction_payments.business_id', $business_id)
            ->whereNull('account_id')
            ->count();

        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview..balance_sheet')
            ->with(compact('not_linked_payments', 'account_types', 'account_exist'));
    }

    public function FinancialPosition()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        $start_date = request()->input('start_date', Carbon::now()->startOfYear()->toDateString());
        $end_date = request()->input('end_date', Carbon::now()->endOfYear()->toDateString());

        // Get exchange rate for the 15th of the month from the end date
        $report_date = Carbon::parse($end_date);
        $mid_month_date = Carbon::createFromDate($report_date->year, $report_date->month, 15);
        
        // Fetch the exchange rate for mid-month
        $exchangeRate = ExchangeRate::where('date_1', $mid_month_date->toDateString())->first();
        
        // If no exchange rate is found for the 15th, try to get most recent rate before that date
        if (!$exchangeRate) {
            $exchangeRate = ExchangeRate::where('date_1', '<', $mid_month_date->toDateString())
                ->orderBy('date_1', 'desc')
                ->first();
        }
        
        // Default exchange rate if none found
        $khr_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;

        // Get account types
        $account_types = AccountType::accounting_primary_type();

        // Get assets, liabilities, and equities
        $assets = Account::leftjoin('account_transactions as AT', function ($join) use ($start_date, $end_date) {
            $join->on('AT.account_id', '=', 'accounts.id')
                ->whereNull('AT.deleted_at')
                ->whereBetween('AT.operation_date', [$start_date, $end_date]);
        })
            ->where('accounts.business_id', $business_id)
            ->where('accounts.account_type_id', 1)
            ->select(DB::raw("SUM(IF(AT.type='credit', amount, -1*amount)) as total_assets"))
            ->first();

        $liabilities = Account::leftjoin('account_transactions as AT', function ($join) use ($start_date, $end_date) {
            $join->on('AT.account_id', '=', 'accounts.id')
                ->whereNull('AT.deleted_at')
                ->whereBetween('AT.operation_date', [$start_date, $end_date]);
        })
            ->where('accounts.business_id', $business_id)
            ->where('accounts.account_type_id', 5)
            ->select(DB::raw("SUM(IF(AT.type='credit', amount, -1*amount)) as total_liabilities"))
            ->first();

        $equities = Account::leftjoin('account_transactions as AT', function ($join) use ($start_date, $end_date) {
            $join->on('AT.account_id', '=', 'accounts.id')
                ->whereNull('AT.deleted_at')
                ->whereBetween('AT.operation_date', [$start_date, $end_date]);
        })
            ->where('accounts.business_id', $business_id)
            ->where('accounts.account_type_id', 4)
            ->select(DB::raw("SUM(IF(AT.type='credit', amount, -1*amount)) as total_equities"))
            ->first();

        // Get transactions
        $transactions = DB::table('account_transactions as AT')
            ->join('accounts', 'AT.account_id', '=', 'accounts.id')
            ->where('accounts.business_id', $business_id)
            ->whereNull('AT.deleted_at')
            ->whereBetween('AT.operation_date', [$start_date, $end_date])
            ->select(
                'AT.type',
                'AT.amount',
                'AT.operation_date',
                'accounts.name as account_name'
            )
            ->get();

        // Get the main accounts for non-ajax request
        $accounts = Account::where('business_id', $business_id)
            ->with([
                'child_accounts',
                'child_accounts.detail_type',
                'detail_type',
                'account_sub_type',
                'child_accounts.account_sub_type',
            ])
            ->select('accounts.*', DB::raw("SUM(IF(AT.type='credit', AT.amount, -1 * AT.amount)) as balance"))
            ->leftJoin('account_transactions as AT', function ($join) use ($start_date, $end_date) {
                $join->on('AT.account_id', '=', 'accounts.id')
                    ->whereNull('AT.deleted_at')
                    ->whereBetween('AT.operation_date', [$start_date, $end_date]);
            })
            ->groupBy('accounts.id')
            ->get();

        // Get account sub types and detail types
        $account_sub_types = AccountType::where('account_type', 'sub_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        $account_detail_types = AccountType::where('account_type', 'detail_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        $debug = [
            'has_sub_types' => $account_sub_types->count() > 0,
            'has_detail_types' => $account_detail_types->count() > 0,
            'sub_types_count' => $account_sub_types->count(),
            'detail_types_count' => $account_detail_types->count(),
            'first_sub_type' => $account_sub_types->first(),
            'first_detail_type' => $account_detail_types->first()
        ];

        if (request()->ajax()) {
            return response()->json([
                'accounts' => $accounts,
                'account_sub_types' => $account_sub_types,
                'account_detail_types' => $account_detail_types,
                'account_types' => $account_types,
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equities' => $equities,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'transactions' => $transactions,
                'debug' => $debug,
                'khr_rate' => $khr_rate
            ]);
        }

        // Pass data to the view
        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.Statement_of_Financial_Position')
            ->with(compact(
                'accounts',
                'account_sub_types',
                'account_detail_types',
                'account_types',
                'assets',
                'liabilities',
                'equities',
                'start_date',
                'end_date',
                'transactions',
                'debug',
                'khr_rate'
            ));
    }

    public function IncomeStatement(Request $request, $year = null)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        // Get distinct years with available data from account_transactions for this business
        $available_years = DB::table('account_transactions as AT')
            ->join('accounts as A', 'AT.account_id', '=', 'A.id')
            ->where('A.business_id', $business_id)
            ->selectRaw('YEAR(AT.operation_date) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();


        // If a year is passed via query parameter or route, use that.
        // Otherwise, if not available or not provided, choose the most recent available year,
        // or default to the current year if no data is found.
        $yearFromRequest = $request->input('year', $year);
        if ($yearFromRequest && in_array($yearFromRequest, $available_years)) {
            $year = $yearFromRequest;
        } else {
            $year = count($available_years) ? $available_years[0] : Carbon::now()->year;
        }

        // Generate array of month start and end dates for the selected year
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            // Create a Carbon instance for the 24th of the month
            $date = Carbon::createFromDate($year, $month, 24);

            // Fetch the exchange rate for the 24th of the month
            $exchangeRate = ExchangeRate::where('date_1', $date->toDateString())->first();

            // If no exchange rate is found for the 24th, you can handle it accordingly
            $khrRate = $exchangeRate ? $exchangeRate->KHR_3 : 'N/A';

            $months[] = [
                'start' => $date->copy()->startOfMonth()->toDateString(), // Start of the month
                'end' => $date->copy()->endOfMonth()->toDateString(),   // End of the month
                'name' => $date->format('M-24') . '<br>KHR',             // Format as "M-24<br>KHR"
                'rate' => $khrRate, // Include the KHR exchange rate in the array
            ];
        }

        // dd($months);


        $start_date = request()->input('start_date', Carbon::createFromDate($year)->startOfYear()->toDateString());
        $end_date = request()->input('end_date', Carbon::createFromDate($year)->endOfYear()->toDateString());

        // Get account types
        $account_types = AccountType::accounting_primary_type();

        // Get accounts with monthly balances
        $accounts = Account::where('business_id', $business_id)
            ->with([
                'child_accounts',
                'child_accounts.detail_type',
                'detail_type',
                'account_sub_type',
                'child_accounts.account_sub_type',
            ])
            ->get()
            ->map(function ($account) use ($months) {
                $monthlyBalances = [];
                foreach ($months as $month) {
                    $balance = DB::table('account_transactions as AT')
                        ->where('AT.account_id', $account->id)
                        ->whereNull('AT.deleted_at')
                        ->whereBetween('AT.operation_date', [$month['start'], $month['end']])
                        ->select(DB::raw("SUM(IF(type='credit', amount, -1 * amount)) as balance"))
                        ->first()->balance ?? 0;
                    $monthlyBalances[] = $balance;
                }
                $account->monthly_balances = $monthlyBalances;
                return $account;
            });

        // Get account sub types and detail types
        $account_sub_types = AccountType::where('account_type', 'sub_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        $account_detail_types = AccountType::where('account_type', 'detail_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.income_statement')
            ->with(compact(
                'accounts',
                'account_sub_types',
                'account_detail_types',
                'account_types',
                'months',
                'year',
                'start_date',
                'end_date',
                'available_years'
            ));
    }

    public function PeriodIncomeStatement1(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        // Get distinct years with available data
        $available_years = DB::table('account_transactions as AT')
            ->join('accounts as A', 'AT.account_id', '=', 'A.id')
            ->where('A.business_id', $business_id)
            ->selectRaw('YEAR(AT.operation_date) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Get comparison dates from request
        $first_date = $request->input('first_date');
        $second_date = $request->input('second_date');

        // If no dates provided, default to current month and same month last year
        if (!$first_date || !$second_date) {
            $current_date = Carbon::now();
            $first_date = $current_date->copy()->startOfMonth()->toDateString();
            $second_date = $current_date->copy()->subYear()->startOfMonth()->toDateString();
        }

        // Convert dates to Carbon instances for manipulation
        $first_period = Carbon::parse($first_date);
        $second_period = Carbon::parse($second_date);

        // Create array for both comparison periods
        $comparison_periods = [
            [
                'start' => $first_period->copy()->startOfMonth()->toDateString(),
                'end' => $first_period->copy()->endOfMonth()->toDateString(),
                'name' => $first_period->format('M-Y') . '<br>USD',
            ],
            [
                'start' => $second_period->copy()->startOfMonth()->toDateString(),
                'end' => $second_period->copy()->endOfMonth()->toDateString(),
                'name' => $second_period->format('M-Y') . '<br>USD',
            ]
        ];

        // Get account types
        $account_types = AccountType::accounting_primary_type();

        // Get accounts with balances for both periods
        $accounts = Account::where('business_id', $business_id)
            ->with([
                'child_accounts',
                'child_accounts.detail_type',
                'detail_type',
                'account_sub_type',
                'child_accounts.account_sub_type',
            ])
            ->get()
            ->map(function ($account) use ($comparison_periods) {
                $periodBalances = [];
                foreach ($comparison_periods as $period) {
                    $balance = DB::table('account_transactions as AT')
                        ->where('AT.account_id', $account->id)
                        ->whereNull('AT.deleted_at')
                        ->whereBetween('AT.operation_date', [$period['start'], $period['end']])
                        ->select(DB::raw("SUM(IF(type='credit', amount, -1 * amount)) as balance"))
                        ->first()->balance ?? 0;
                    $periodBalances[] = $balance; // Add balance for each period
                }
                $account->period_balances = $periodBalances; // Assign as an array
                return $account;
            });

        // Get account sub types and detail types
        $account_sub_types = AccountType::where('account_type', 'sub_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        $account_detail_types = AccountType::where('account_type', 'detail_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        // Generate accounts_in_detail with detailed transaction information
        $accounts_in_detail = DB::table('account_transactions as AT')
            ->join('accounts as A', 'AT.account_id', '=', 'A.id')
            ->where('A.business_id', $business_id)
            ->whereBetween('AT.operation_date', [$first_date, $second_date])
            ->select('A.name as account_name', 'AT.type', 'AT.amount', 'AT.operation_date')
            ->orderBy('AT.operation_date', 'asc')
            ->get();

        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.aperiod_income_statement')
            ->with(compact(
                'accounts',
                'accounts_in_detail',
                'account_sub_types',
                'account_detail_types',
                'account_types',
                'comparison_periods',
                'available_years',
                'first_date',
                'second_date'
            ));
    }

    public function PeriodIncomeStatement()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        // Get dates from request or default to current year
        $first_date = request()->input('first_date', Carbon::now()->startOfMonth()->toDateString());
        $second_date = request()->input('second_date', Carbon::now()->startOfMonth()->toDateString());

        $first_start = Carbon::parse($first_date)->startOfMonth();
        $first_end = $first_start->copy()->endOfMonth();
        $second_start = Carbon::parse($second_date)->startOfMonth();
        $second_end = $second_start->copy()->endOfMonth();
        
        // Get exchange rate for the 15th of the month from the end date
        $report_date = Carbon::parse($second_date);
        $mid_month_date = Carbon::createFromDate($report_date->year, $report_date->month, 15);
        
        // Fetch the exchange rate for mid-month
        $exchangeRate = ExchangeRate::where('date_1', $mid_month_date->toDateString())->first();
        
        // If no exchange rate is found for the 15th, try to get most recent rate before that date
        if (!$exchangeRate) {
            $exchangeRate = ExchangeRate::where('date_1', '<', $mid_month_date->toDateString())
                ->orderBy('date_1', 'desc')
                ->first();
        }
        
        // Default exchange rate if none found
        $khr_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;

        // Account types
        $account_types = [
            'income' => ['label' => 'Income'],
            'expenses' => ['label' => 'Expenses']
        ];

        // Get income and expenses for both periods
        $comparison_periods = [
            ['name' => $first_start->format('F Y'), 'start' => $first_start, 'end' => $first_end],
            ['name' => $second_start->format('F Y'), 'start' => $second_start, 'end' => $second_end]
        ];

        // Get account sub types and detail types
        $account_sub_types = AccountType::where('account_type', 'sub_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        $account_detail_types = AccountType::where('account_type', 'detail_type')
            ->where(function ($q) use ($business_id) {
                $q->whereNull('business_id')
                    ->orWhere('business_id', $business_id);
            })
            ->get();

        // Get detailed accounts data
        $accounts = Account::where('business_id', $business_id)
            ->with([
                'detail_type',
                'account_sub_type',
            ])
            ->get();

        // Calculate totals and organize data by account type, subtype, and period
        $data = [];
        $detailed_data = [];
        $account_details = [];

        foreach ($comparison_periods as $period) {
            $period_name = $period['name'];
            $period_start = $period['start'];
            $period_end = $period['end'];
            
            // Initialize data structure
            $data[$period_name] = [
                'income' => 0,
                'expenses' => 0
            ];
            
            $detailed_data[$period_name] = [
                'income' => [],
                'expenses' => []
            ];

            // Get accounts with balances for this period
            $accounts_with_balances = Account::where('accounts.business_id', $business_id)
                ->select(
                    'accounts.*', 
                    DB::raw("SUM(IF(AT.type='credit', AT.amount, -1 * AT.amount)) as balance")
                )
                ->leftJoin('account_transactions as AT', function ($join) use ($period_start, $period_end) {
                    $join->on('AT.account_id', '=', 'accounts.id')
                        ->whereNull('AT.deleted_at')
                        ->whereBetween('AT.operation_date', [$period_start, $period_end]);
                })
                ->with(['account_sub_type', 'detail_type'])
                ->groupBy('accounts.id')
                ->get();
            
            // Organize by account type and subtype
            foreach ($accounts_with_balances as $account) {
                if (!$account->account_sub_type) {
                    continue;
                }
                
                $primary_type = $account->account_sub_type->account_primary_type ?? null;
                
                if (!in_array($primary_type, ['income', 'expenses'])) {
                    continue;
                }
                
                $sub_type_id = $account->account_sub_type_id;
                $detail_type_id = $account->detail_type_id;
                $balance = $account->balance ?: 0;
                
                // Add to total for this account type
                $data[$period_name][$primary_type] += $balance;
                
                // Store account details
                if (!isset($account_details[$primary_type][$sub_type_id])) {
                    $account_details[$primary_type][$sub_type_id] = [
                        'name' => $account->account_sub_type->name,
                        'detail_types' => []
                    ];
                }
                
                if (!isset($account_details[$primary_type][$sub_type_id]['detail_types'][$detail_type_id])) {
                    $account_details[$primary_type][$sub_type_id]['detail_types'][$detail_type_id] = [
                        'name' => $account->detail_type->name ?? 'Uncategorized',
                        'accounts' => []
                    ];
                }
                
                $account_details[$primary_type][$sub_type_id]['detail_types'][$detail_type_id]['accounts'][$account->id] = [
                    'name' => $account->name,
                    'account_number' => $account->account_number
                ];
                
                // Store detailed balances by period
                if (!isset($detailed_data[$period_name][$primary_type][$sub_type_id])) {
                    $detailed_data[$period_name][$primary_type][$sub_type_id] = [
                        'total' => 0,
                        'detail_types' => []
                    ];
                }
                
                $detailed_data[$period_name][$primary_type][$sub_type_id]['total'] += $balance;
                
                if (!isset($detailed_data[$period_name][$primary_type][$sub_type_id]['detail_types'][$detail_type_id])) {
                    $detailed_data[$period_name][$primary_type][$sub_type_id]['detail_types'][$detail_type_id] = [
                        'total' => 0,
                        'accounts' => []
                    ];
                }
                
                $detailed_data[$period_name][$primary_type][$sub_type_id]['detail_types'][$detail_type_id]['total'] += $balance;
                $detailed_data[$period_name][$primary_type][$sub_type_id]['detail_types'][$detail_type_id]['accounts'][$account->id] = $balance;
            }
        }

        if (request()->ajax()) {
            return response()->json([
                'comparison_periods' => $comparison_periods,
                'data' => $data,
                'detailed_data' => $detailed_data,
                'account_details' => $account_details,
                'account_sub_types' => $account_sub_types,
                'account_detail_types' => $account_detail_types,
                'khr_rate' => $khr_rate
            ]);
        }

        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.aperiod_income_statement', [
            'first_date' => $first_start,
            'second_date' => $second_start,
            'comparison_periods' => $comparison_periods,
            'account_types' => $account_types,
            'account_sub_types' => $account_sub_types,
            'account_detail_types' => $account_detail_types,
            'data' => $data,
            'detailed_data' => $detailed_data,
            'account_details' => $account_details,
            'accounts' => $accounts,
            'khr_rate' => $khr_rate
        ]);
    }

    protected function applyDateFilters($query, $request)
    {
        if ($request->has('today')) {
            $query->whereDate('t.transaction_date', today());
        } elseif ($request->filled('year') || $request->filled('month_only')) {
            if ($request->filled('year')) {
                $query->whereYear('t.transaction_date', $request->year);
            }
            if ($request->filled('month_only')) {
                $query->whereMonth('t.transaction_date', $request->month_only);
            }
        } elseif (!$request->has('show_all')) {
            // Default to current month
            $query->whereYear('t.transaction_date', now()->year)
                ->whereMonth('t.transaction_date', now()->month);
        }
    }

    protected function getAvailableYears($business_id)
    {
        return DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->selectRaw('DISTINCT YEAR(transaction_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }



    public function getIncomeForMonths(Request $request)
    {
        if (!auth()->user()->can('Product_Sell_Report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        // Get date range from DateFilterService
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Build base query
        $query = DB::table('transactions as t')
            ->join('contacts as c', 't.contact_id', '=', 'c.id')
            ->join('transaction_sell_lines as tsl', 't.id', '=', 'tsl.transaction_id')
            ->join('products as p', 'tsl.product_id', '=', 'p.id')
            ->leftJoin('categories as cat', 'p.category_id', '=', 'cat.id')
            ->leftJoin('exchangerate_main as er', function ($join) use ($business_id) {
                $join->on(DB::raw('DATE(t.transaction_date)'), '=', DB::raw('DATE(er.Date_1)'))
                    ->where('er.business_id', '=', $business_id);
            })
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final');

        // Apply date filters using DateFilterService
        if ($start_date && $end_date) {
            $query->whereBetween('t.transaction_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')]);
        }

        // Get available years for filter dropdown
        $available_years = $this->getAvailableYears($business_id);

        // Select required fields
        $query->select(
            'c.name as customer',
            'c.supplier_business_name',
            'c.contact_id',
            't.id as transaction_id',
            't.invoice_no',
            't.transaction_date',
            't.tax_amount as item_tax',
            't.final_total as subtotal',
            DB::raw('t.final_total - t.tax_amount as unit_price'),
            DB::raw('COALESCE(er.KHR_3, 0) as exchange_rate_khr'),
            'er.Date_1 as exchange_rate_date',
            DB::raw('GROUP_CONCAT(DISTINCT cat.name SEPARATOR " & ") as category_name'),
            DB::raw('CASE 
                WHEN er.KHR_3 IS NOT NULL AND er.KHR_3 != 1.000 
                THEN t.final_total * er.KHR_3 
                ELSE NULL 
            END as khr_amount')
        )
            ->groupBy(
                't.id',
                't.invoice_no',
                't.transaction_date',
                'c.name',
                'c.supplier_business_name',
                'c.contact_id',
                't.tax_amount',
                't.final_total',
                'er.KHR_3',
                'er.Date_1'
            )
            ->orderBy('t.invoice_no', 'asc');



        // Get total count for pagination
        $total_count = $query->count();

        // Calculate totals from all records (before pagination)
        $totals_query = clone $query;
        $totals = $totals_query->select(
            DB::raw('SUM(t.final_total) as total_cross_sale'),
            DB::raw('SUM(t.final_total - t.tax_amount) as total_net_price'),
            DB::raw('SUM(
                CASE 
                    WHEN er.KHR_3 IS NOT NULL AND er.KHR_3 != 1.000 
                    THEN t.final_total * er.KHR_3 
                    ELSE t.final_total * 4100 
                END
            ) as total_khr')
        )->first();

        // Ensure the totals are never null
        $total_cross_sale = $totals->total_cross_sale ?? 0;
        $total_net_price = $totals->total_net_price ?? 0;
        $total_khr = $totals->total_khr ?? 0;
        
        // Apply pagination
        $page = (int)$request->input('page', 1);
        $limit = (int)$request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to the query
        $raw_data = $query->skip($offset)->take($limit)->get();

        // Format data
        $formatted_data = $raw_data->map(function ($row) {
            // Use category_name instead of product_names
            $description = $row->category_name ?? 'Uncategorized';

            $customer_name = !empty($row->supplier_business_name) ?
                $row->supplier_business_name . ' - ' . $row->customer :
                $row->customer;

            return [
                'id' => $row->transaction_id,
                'date' => $row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y') : 'N/A',
                'voucher' => $row->transaction_id,
                'unit_price' => number_format((float)$row->unit_price, 2, '.', ','),
                'item_tax' => number_format(abs((float)$row->item_tax), 2, '.', ','),
                'subtotal' => number_format((float)$row->subtotal, 2, '.', ','),
                'customer' => $customer_name,
                'invoice_no' => $row->invoice_no ?? 'N/A',
                'description' => $description,
                'exchange_rate_khr' => number_format((float)$row->exchange_rate_khr, 0, '.', ','),
                'exchange_rate_date' => $row->exchange_rate_date ?
                    \Carbon\Carbon::parse($row->exchange_rate_date)->format('m/d/Y') :
                    'N/A',
                'khr_amount' => $row->khr_amount ? number_format((float)$row->khr_amount, 0, '.', ',') : 'N/A'
            ];
        });

        // Handle AJAX response
        if ($request->ajax()) {
            return response()->json([
                'data' => $formatted_data,
                'total_cross_sale' => number_format($total_cross_sale, 2, '.', ','),
                'total_khr' => number_format($total_khr, 0, '.', ','),
                'total_net_price' => number_format($total_net_price, 2, '.', ','),
                'start_date' => $start_date ? $start_date->format('d/m/Y') : '',
                'end_date' => $end_date ? $end_date->format('d/m/Y') : '',
                'total_pages' => ceil($total_count / $limit),
                'current_page' => $page,
                'total_records' => $total_count
            ]);
        }

        // Normal view response
        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.income_for_month', compact(
            'available_years',
            'business',
            'start_date',
            'end_date'
        ));
    }

    public function getExspenseForMonth(Request $request)
    {
        // Authorization check
        if (!auth()->user()->can('all_expense.access') && !auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }
    
        $business_id = request()->session()->get('user.business_id');
        $business_name = Business::where('id', $business_id)->value('name');
    
        // Get date range from the request
        $date_range = $this->dateFilterService->calculateDateRange($request);
        $start_date = $date_range['start_date'];
        $end_date = $date_range['end_date'];
    
        // Query to fetch expenses
        $query = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
            ->leftJoin('expense_categories AS esc', 'transactions.expense_sub_category_id', '=', 'esc.id')
            ->join('business_locations AS bl', 'transactions.location_id', '=', 'bl.id')
            ->leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
            ->leftJoin('users AS U', 'transactions.expense_for', '=', 'U.id')
            ->leftJoin('users AS usr', 'transactions.created_by', '=', 'usr.id')
            ->leftJoin('contacts AS c', 'transactions.contact_id', '=', 'c.id')
            ->leftJoin('transaction_payments AS TP', 'transactions.id', '=', 'TP.transaction_id')
            ->leftJoin('exchangerate_main as er', function ($join) use ($business_id) {
                $join->on(DB::raw('DATE(transactions.transaction_date)'), '=', DB::raw('DATE(er.Date_1)'))
                    ->where('er.business_id', '=', $business_id);
            })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.status', 'final')
            ->where('transactions.type', 'expense')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
            })
            ->select(
                'transactions.id',
                'transactions.transaction_date',
                'transactions.ref_no',
                'transactions.additional_notes',
                'transactions.final_total',
                'transactions.total_before_tax',
                'transactions.tax_amount',
                'c.name as contact_name',
                DB::raw('IFNULL(c.tax_number, c.mobile) as tax_number'),
                'ec.name as category',
                'esc.name as sub_category',
                'bl.name as location_name',
                DB::raw('IFNULL(er.KHR_3, 0) as exchange_rate_khr')
            )
            ->groupBy('transactions.id');
            
        // Get total count for pagination
        $total_count = $query->count();
        
        // Get the total calculations before pagination
        $totals_query = clone $query;
        $totals = $totals_query->select(
            DB::raw('SUM(transactions.final_total) as total_final_total'),
            DB::raw('SUM(transactions.total_before_tax) as total_net_amount_usd'),
            DB::raw('SUM(transactions.tax_amount) as total_vat_input_usd'),
            DB::raw('SUM(transactions.total_before_tax * IFNULL(er.KHR_3, 4000)) as total_net_amount_khr'),
            DB::raw('SUM(transactions.tax_amount * IFNULL(er.KHR_3, 4000)) as total_vat_input_khr'),
            DB::raw('SUM(transactions.final_total * IFNULL(er.KHR_3, 4000)) as total_final_total_khr'),
            DB::raw('SUM(transactions.tax_amount * IFNULL(er.KHR_3, 4000)) as total_wht_khr')
        )->first();

        // Apply pagination
        $page = (int)$request->input('page', 1);
        $limit = (int)$request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        
        // Get paginated expenses
        $expenses = $query->skip($offset)->take($limit)->get();
    
        // Process and calculate values to match the exact values in your report
        foreach ($expenses as $expense) {
            // Use the exact values from the database for tax calculations
            $expense->net_amount_usd = $expense->total_before_tax;  
            $expense->vat_input_usd = $expense->tax_amount;         
            
            // Calculate KHR values using the precise USD values
            $expense->net_amount_khr = round($expense->net_amount_usd * $expense->exchange_rate_khr);
            $expense->vat_input_khr = round($expense->vat_input_usd * $expense->exchange_rate_khr);
            $expense->final_total_khr = round($expense->final_total * $expense->exchange_rate_khr);
            $expense->wht_khr = round($expense->vat_input_usd * $expense->exchange_rate_khr); // WHT is same as VAT in this case
        }
    
        // Store totals in a readable format
        $formatted_totals = [
            'final_total' => number_format($totals->total_final_total ?? 0, 2, '.', ','),
            'net_amount_usd' => number_format($totals->total_net_amount_usd ?? 0, 2, '.', ','),
            'vat_input_usd' => number_format($totals->total_vat_input_usd ?? 0, 2, '.', ','),
            'net_amount_khr' => number_format($totals->total_net_amount_khr ?? 0, 0, '.', ','),
            'vat_input_khr' => number_format($totals->total_vat_input_khr ?? 0, 0, '.', ','),
            'final_total_khr' => number_format($totals->total_final_total_khr ?? 0, 0, '.', ','),
            'wht_khr' => number_format($totals->total_wht_khr ?? 0, 0, '.', ',')
        ];
    
        if ($request->ajax()) {
            return response()->json([
                'data' => $expenses,
                'totals' => $formatted_totals,
                'business_name' => $business_name,
                'start_date' => $start_date ? Carbon::parse($start_date)->format('d/m/Y') : '',
                'end_date' => $end_date ? Carbon::parse($end_date)->format('d/m/Y') : '',
                'total_pages' => ceil($total_count / $limit),
                'current_page' => $page,
                'total_records' => $total_count
            ]);
        }
    
        // For non-AJAX requests, return the view with data
        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.expense_for_months')
            ->with(compact(
                'expenses',
                'totals',
                'business_name',
                'start_date',
                'end_date'
            ));
    }

    /**
     * Monthly Bank Reconciliation Report
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function getBankReconciliation(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        
        // Get current date
        $report_date = $request->input('report_date', Carbon::now()->format('Y-m-d'));
        $carbon_date = Carbon::parse($report_date);
        $month = $carbon_date->format('F');
        $year = $carbon_date->format('Y');
        $last_day = $carbon_date->endOfMonth()->format('d');
        
        // Format as "31 December 2024"
        $formatted_date = $last_day . ' ' . $month . ' ' . $year;
        
        // Get all bank accounts
        $accounts = Account::where('business_id', $business_id)
            ->where('account_type_id', 1) // Asset accounts
            ->where('is_closed', 0) // Only active accounts
            ->whereNotNull('account_number') // Must have account number
            ->select('id', 'name', 'account_number')
            ->get();
        
        $selected_account = $request->input('account_id');
        
        // Get account balance from bank statement
        $bank_statement_balance = $request->input('bank_statement_balance', 0);

        // For demonstration, we'll create empty sample data structures
        $outstanding_checks = [];
        $outstanding_deposits = [];
        $bank_charges = [];
        $bank_interest = [];
        
        // If this is an AJAX request, return the data as JSON
        if ($request->ajax()) {
            // In a real implementation, you would query this data from your database
            return response()->json([
                'bank_statement_balance' => $bank_statement_balance,
                'outstanding_checks' => $outstanding_checks,
                'outstanding_deposits' => $outstanding_deposits,
                'bank_charges' => $bank_charges,
                'bank_interest' => $bank_interest
            ]);
        }
        
        // Get the business information
        $business = Business::findOrFail($business_id);
        
        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.bank_reconciliation')
            ->with(compact(
                'accounts',
                'selected_account',
                'bank_statement_balance',
                'outstanding_checks',
                'outstanding_deposits',
                'bank_charges',
                'bank_interest',
                'business',
                'formatted_date',
                'report_date'
            ));
    }
}
