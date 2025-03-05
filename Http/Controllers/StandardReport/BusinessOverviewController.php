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

    public function QuarterlyReport(Request $request)
    {
        if (!auth()->user()->can('quickbooks.Supplier&Customer_Report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Debugging: Log the request parameters
        \Log::info('Request Parameters:', $request->all());

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
            \Log::info('Final SQL Query:', [
                'sql' => $contacts->toSql(),
                'bindings' => $contacts->getBindings()
            ]);

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
                        return  $row->account_sub_type_name;
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
                'transactions'
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
            $date = Carbon::createFromDate($year, $month, 1);
            $months[] = [
                'start' => $date->copy()->startOfMonth()->toDateString(),
                'end'   => $date->copy()->endOfMonth()->toDateString(),
                'name'  => $date->format('M-24') . '<br>USD', // Use <br> for HTML tables
            ];
        }


        $start_date = request()->input('start_date', Carbon::createFromDate($year)->startOfYear()->toDateString());
        $end_date   = request()->input('end_date', Carbon::createFromDate($year)->endOfYear()->toDateString());

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

    public function PeriodIncomeStatement(Request $request)
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
                    $periodBalances[] = $balance;
                }
                $account->period_balances = $periodBalances;
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


    public function getIncomeForMonths(Request $request)
    {
        if (!auth()->user()->can('Product_Sell_Report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        // Initialize date filters
        $date_range = $this->dateFilterService->calculateDateRange($request);
        $start_date = $date_range['start_date'];
        $end_date = $date_range['end_date'];

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
            ->where('t.status', 'final')
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('t.transaction_date', [$start_date, $end_date]);
            });

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
            ->orderBy('t.transaction_date', 'desc');

        // Paginate the results
        $perPage = 10; // Number of items per page
        $raw_data = $query->paginate($perPage);

        $total_cross_sale = 0;
        $total_net_price = 0;
        $total_khr = 0;

        // Format data
        $formatted_data = $raw_data->map(function ($row) use (&$total_cross_sale, &$total_khr, &$total_net_price) {
            $description = $row->category_name ?? 'Uncategorized';

            $customer_name = !empty($row->supplier_business_name) ?
                $row->supplier_business_name . ' - ' . $row->customer :
                $row->customer;

            $subtotal = is_numeric($row->subtotal) ? (float)$row->subtotal : 0;
            $khr_amount = is_numeric($row->khr_amount) ? (float)$row->khr_amount : 0;
            $unit_price = is_numeric($row->unit_price) ? (float)$row->unit_price : 0;

            $total_cross_sale += $subtotal;
            $total_khr += $khr_amount;
            $total_net_price += $unit_price;

            return [
                'date' => $row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y') : 'N/A',
                'voucher' => $row->transaction_id,
                'unit_price' => number_format((float)$row->unit_price, 2, '.', ''),
                'item_tax' => number_format(abs((float)$row->item_tax), 2, '.', ''),
                'subtotal' => number_format((float)$row->subtotal, 2, '.', ''),
                'customer' => $customer_name,
                'invoice_no' => $row->invoice_no ?? 'N/A',
                'description' => $description,
                'exchange_rate_khr' => number_format((float)$row->exchange_rate_khr, 0, '.', ''),
                'exchange_rate_date' => $row->exchange_rate_date ?
                    \Carbon\Carbon::parse($row->exchange_rate_date)->format('m/d/Y') :
                    'N/A',
                'khr_amount' => $row->khr_amount ? number_format((float)$row->khr_amount, 0, '.', ',') : 'N/A'
            ];
        });

        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.income_for_month', compact(
            'formatted_data',
            'business',
            'total_cross_sale',
            'total_khr',
            'total_net_price',
            'start_date',
            'end_date',
            'raw_data' // Pass the paginated data to the view
        ));
    }

    public function getExspenseForMonth(Request $request)
    {
        // Authorization check
        if (!auth()->user()->can('all_expense.access') && !auth()->user()->can('view_own_expense')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get date range from the request
        $date_range = $this->dateFilterService->calculateDateRange($request);
        $start_date = $date_range['start_date'];
        $end_date = $date_range['end_date'];

        // Query to fetch expenses
        $expenses = Transaction::leftJoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
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
            ->whereIn('transactions.type', ['expense'])
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
            })
            ->select(
                'transactions.id',
                'transactions.document',
                'transactions.transaction_date', // Fixed to use 'transactions' consistently
                'transactions.ref_no',
                'ec.name as category',
                'esc.name as sub_category',
                'transactions.payment_status',
                'transactions.additional_notes',
                'transactions.final_total',
                'transactions.is_recurring',
                'transactions.recur_interval',
                'transactions.recur_interval_type',
                'transactions.recur_repetitions',
                'transactions.subscription_repeat_on',
                'transactions.audit_status',
                'bl.name as location_name',
                DB::raw("CONCAT(COALESCE(U.surname, ''),' ',COALESCE(U.first_name, ''),' ',COALESCE(U.last_name,'')) as expense_for"),
                DB::raw("CONCAT(tr.name ,' (', tr.amount ,' )') as tax"),
                DB::raw('SUM(TP.amount) as amount_paid'),
                DB::raw("CONCAT(COALESCE(usr.surname, ''),' ',COALESCE(usr.first_name, ''),' ',COALESCE(usr.last_name,'')) as added_by"),
                'transactions.recur_parent_id',
                'c.name as contact_name',
                'c.tax_number as tax_number',
                'transactions.type',
                'er.KHR_3 as exchange_rate_khr'
            )
            ->with(['recurring_parent'])
            ->groupBy('transactions.id')
            ->get();
        $total_final_total = $expenses->sum('final_total');
        $total_amount_paid = $expenses->sum('amount_paid');
        $total_due = $total_final_total - $total_amount_paid;

        // Get available years for filtering
        $available_years = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->selectRaw('DISTINCT YEAR(transaction_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Additional data for filters
        $categories = ExpenseCategory::where('business_id', $business_id)
            ->whereNull('parent_id')
            ->pluck('name', 'id');

        $users = User::forDropdown($business_id, false, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contacts = Contact::contactDropdown($business_id, false, false);

        $sub_categories = ExpenseCategory::where('business_id', $business_id)
            ->whereNotNull('parent_id')
            ->pluck('name', 'id')
            ->toArray();

        $audit_statuses = $this->transactionUtil->audit_statuses();

        // Pass data to the view
        return view('minireportb1::MiniReportB1.StandardReport.BusinessOverview.expense_for_months')
            ->with(compact(
                'expenses',
                'total_final_total',
                'total_amount_paid',
                'total_due',
                'categories',
                'business_locations',
                'users',
                'audit_statuses',
                'contacts',
                'sub_categories',
                'available_years',
                'start_date',
                'end_date'
            ));
    }
}
