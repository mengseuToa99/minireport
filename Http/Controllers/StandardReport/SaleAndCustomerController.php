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
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\Schedule;
use App\Utils\ContactUtil;
use Illuminate\Support\Facades\Validator;



class SaleAndCustomerController extends Controller
{


    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;

    protected $dateFilterService;

    protected $contactUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */




    public function __construct(DateFilterService $dateFilterService, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil,   ContactUtil $contactUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->dateFilterService = $dateFilterService;
        $this->contactUtil = $contactUtil;
    }

    public function customerLoanReport(Request $request, DateFilterService $dateFilterService)
    {
        // Get date range from DateFilterService
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];
    
        $business_id = $request->session()->get('user.business_id');
    
        // Query with date filter applied
        $query = Contact::join('transactions', 'contacts.id', '=', 'transactions.contact_id')
            ->where('contacts.business_id', $business_id)
            ->where('transactions.pay_term_number', '>', 0)
            ->whereBetween('transactions.transaction_date', [$start_date, $end_date]) // Date filter
            ->select(
                'contacts.id',
                'contacts.name',
                'contacts.email',
                'contacts.mobile',
                'contacts.city',
                'contacts.state',
                'contacts.country',
                'contacts.zip_code',
                'contacts.created_by',       // Assuming this is a user ID
                'contacts.customer_group_id', // Assuming this links to a group
                'transactions.business_id',
                'transactions.pay_term_number',
                'transactions.final_total'
            );
    
        // Execute the query
        $customers = $query->get();
    
        // AJAX response
        if ($request->ajax()) {
            $formatted_data = $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email ?? 'N/A',
                    'mobile' => $customer->mobile ?? 'N/A',
                    'city' => $customer->city ?? 'N/A',
                    'state' => $customer->state ?? 'N/A',
                    'country' => $customer->country ?? 'N/A',
                    'zip_code' => $customer->zip_code ?? 'N/A',
                    'created_by' => $customer->created_by_fullname ?? 'N/A', // Ensure this accessor exists
                    'customer_group' => $customer->customer_group_name ?? 'N/A', // Ensure this accessor exists
                    'pay_term_number' => $customer->pay_term_number ?? 0,
                    'final_total' => $customer->final_total ?? 0 // Default to 0 if null
                ];
            });
    
            return response()->json([
                'data' => $formatted_data,
                'total' => $customers->count(),
                'total_loan' => $customers->sum('final_total')
            ]);
        }
    
        // Return view for non-AJAX requests
        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.customer_loan');
    }



    public function customerPurchaseReport(Request $request, DateFilterService $dateFilterService)
    {
        if (!auth()->user()->can('account_receivable.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range from DateFilterService
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query using contactUtil
        $query = $this->contactUtil->getContactQuery($business_id, 'customer');

        // Filter for unpaid accounts
        $query->havingRaw('total_invoice >  0');

        // Apply date filter
        if ($request->filled('date_filter')) {
            if ($request->date_filter === 'custom_month_range') {
                $query->whereDate('contacts.created_at', '>=', $start_date)
                    ->whereDate('contacts.created_at', '<=', $end_date);
            } else {
                $this->applyDateFilter($query, $request->date_filter, 'contacts.created_at');
            }
        }

        // Created by filter
        if ($request->filled('created_by_filter')) {
            $query->where('contacts.created_by', $request->input('created_by_filter'));
        }

        // Assigned to filter
        if ($request->filled('assigned_to_filter')) {
            $query->where('contacts.assigned_to', $request->input('assigned_to_filter'));
        }

        // Location filter (if needed)
        if ($request->filled('location_id')) {
            $query->where('contacts.location_id', $request->input('location_id'));
        }

        // Get results
        $contacts = $query->orderBy('contacts.created_at', 'desc')->get();

        // AJAX response
        if ($request->ajax()) {
            $formatted_data = $contacts->map(function ($contact) {
                return [
                    'name' => $contact->name ?? 'N/A',
                    'email' => $contact->email ?? 'N/A',
                    'mobile' => $contact->mobile ?? 'N/A',
                    'total_purchase' => $contact->total_purchase ?? 0,
                    'purchase_paid' => $contact->purchase_paid ?? 0,
                    'balance' => ($contact->total_purchase ?? 0) - ($contact->purchase_paid ?? 0),
                    'address_line_1' => $contact->address_line_1 ?? 'N/A',
                    'address_line_2' => $contact->address_line_2 ?? 'N/A',
                    'city' => $contact->city ?? 'N/A',
                    'state' => $contact->state ?? 'N/A',
                    'country' => $contact->country ?? 'N/A',
                    'zip_code' => $contact->zip_code ?? 'N/A',
                    'created_by' => $contact->created_by->name ?? 'N/A',
                    'assigned_to' => $contact->assigned_to_user->full_name ?? 'N/A',
                    'created_at' => $contact->created_at ? $contact->created_at->format('Y-m-d') : 'N/A',
                ];
            });

            return response()->json([
                'data' => $formatted_data,
                'total' => $contacts->count()
            ]);
        }

        // Normal view response
        return view(
            'minireportb1::MiniReportB1.StandardReport.Salesandcustomers.customer_purchase',
            [
                'users' => User::where('business_id', $business_id)->get(),
                'business_locations' => BusinessLocation::forDropdown($business_id),
                'filterOptions' => [
                    'users' => User::where('business_id', $business_id)->get(),
                    'locations' => BusinessLocation::forDropdown($business_id)
                ]
            ]
        );
    }



    public function accountsReceivableUnpaid(Request $request, DateFilterService $dateFilterService)
    {
        if (!auth()->user()->can('account_receivable.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range from DateFilterService
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query using contactUtil
        $query = $this->contactUtil->getContactQuery($business_id, 'customer');

        // Filter for unpaid accounts
        $query->havingRaw('(total_invoice - invoice_received) > 0');

        // Apply date filter
        if ($request->filled('date_filter')) {
            if ($request->date_filter === 'custom_month_range') {
                $query->whereDate('contacts.created_at', '>=', $start_date)
                    ->whereDate('contacts.created_at', '<=', $end_date);
            } else {
                $this->calculateDateRange($query, $request->date_filter, 'contacts.created_at');
            }
        }

        // Created by filter
        if ($request->filled('created_by_filter')) {
            $query->where('contacts.created_by', $request->input('created_by_filter'));
        }

        // Assigned to filter
        if ($request->filled('assigned_to_filter')) {
            $query->where('contacts.assigned_to', $request->input('assigned_to_filter'));
        }

        // Location filter (if needed)
        if ($request->filled('location_id')) {
            $query->where('contacts.location_id', $request->input('location_id'));
        }

        // Get results
        $contacts = $query->orderBy('contacts.created_at', 'desc')->get();

        // AJAX response
        if ($request->ajax()) {
            $formatted_data = $contacts->map(function ($contact) {
                return [
                    'name' => $contact->name ?? 'N/A',
                    'email' => $contact->email ?? 'N/A',
                    'mobile' => $contact->mobile ?? 'N/A',
                    'total_purchase' => $contact->total_purchase ?? 0,
                    'purchase_paid' => $contact->purchase_paid ?? 0,
                    'balance' => ($contact->total_purchase ?? 0) - ($contact->purchase_paid ?? 0),
                    'address_line_1' => $contact->address_line_1 ?? 'N/A',
                    'address_line_2' => $contact->address_line_2 ?? 'N/A',
                    'city' => $contact->city ?? 'N/A',
                    'state' => $contact->state ?? 'N/A',
                    'country' => $contact->country ?? 'N/A',
                    'zip_code' => $contact->zip_code ?? 'N/A',
                    'created_by' => $contact->created_by->name ?? 'N/A',
                    'assigned_to' => $contact->assigned_to_user->full_name ?? 'N/A',
                    'created_at' => $contact->created_at ? $contact->created_at->format('Y-m-d') : 'N/A',
                ];
            });

            return response()->json([
                'data' => $formatted_data,
                'total' => $contacts->count()
            ]);
        }

        // Normal view response
        return view(
            'minireportb1::MiniReportB1.StandardReport.Salesandcustomers.account_receivable_unpaid',
            [
                'users' => User::where('business_id', $business_id)->get(),
                'business_locations' => BusinessLocation::forDropdown($business_id),
                'filterOptions' => [
                    'users' => User::where('business_id', $business_id)->get(),
                    'locations' => BusinessLocation::forDropdown($business_id)
                ]
            ]
        );
    }



    public function customerwithoutmap(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Base query
        $query = Contact::join('users', 'contacts.created_by', '=', 'users.id')
            ->leftJoin('user_contact_access', 'contacts.id', '=', 'user_contact_access.contact_id')
            ->leftJoin('users as assigned_user', 'user_contact_access.user_id', '=', 'assigned_user.id')
            ->leftJoin('customer_groups', 'contacts.customer_group_id', '=', 'customer_groups.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.position', null)
            ->select([
                'contacts.*',
                'users.first_name as creator_first_name',
                'users.last_name as creator_last_name',
                'assigned_user.first_name as assignee_first_name',
                'assigned_user.last_name as assignee_last_name',
                'customer_groups.name as customer_group_name'
            ]);

        // Apply Created By filter
        if ($request->filled('created_by_filter')) {
            $query->where('contacts.created_by', $request->input('created_by_filter'));
        }

        // Apply Assigned To filter
        if ($request->filled('assigned_to_filter')) {
            $query->where('user_contact_access.user_id', $request->input('assigned_to_filter'));
        }

        // Apply Customer Group filter
        if ($request->filled('customer_group_filter')) {
            $query->where('contacts.customer_group_id', $request->input('customer_group_filter'));
        }

        // Get results
        $customers = $query->orderBy('contacts.created_at', 'desc')->get();

        // Prepare data with full names
        $customers->transform(function ($customer) {
            $customer->created_by_fullname = trim($customer->creator_first_name . ' ' . $customer->creator_last_name);
            $customer->assigned_to_fullname = trim($customer->assignee_first_name . ' ' . $customer->assignee_last_name);
            return $customer;
        });

        // Get filter options
        $filterOptions = [
            'users' => User::where('business_id', $business_id)->get(),
            'customer_groups' => CustomerGroup::where('business_id', $business_id)->get()
        ];

        // AJAX response
        if ($request->ajax()) {
            $formatted_data = $customers->map(function ($customer) {
                return [
                    'name' => $customer->name,
                    'email' => $customer->email ?? 'N/A',
                    'mobile' => $customer->mobile ?? 'N/A',
                    'address_line_1' => $customer->address_line_1 ?? 'N/A',
                    'address_line_2' => $customer->address_line_2 ?? 'N/A',
                    'city' => $customer->city ?? 'N/A',
                    'state' => $customer->state ?? 'N/A',
                    'country' => $customer->country ?? 'N/A',
                    'zip_code' => $customer->zip_code ?? 'N/A',
                    'created_by' => $customer->created_by_fullname ?: 'N/A',
                    'assigned_to' => $customer->assigned_to_fullname ?: 'N/A',
                    'customer_group' => $customer->customer_group_name ?? 'N/A'
                ];
            });

            return response()->json([
                'data' => $formatted_data,
                'total' => $customers->count(),

            ]);
        }

        // Normal view response
        return view(
            'minireportb1::MiniReportB1.StandardReport.Salesandcustomers.customer_no_map',
            array_merge(['customers' => $customers], $filterOptions)
        );
    }


    public function branchDataReport(Request $request, DateFilterService $dateFilterService)
    {
        $business_id = $request->session()->get('user.business_id');

        // Check permissions
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        // Initialize date filters using DateFilterService
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query
        $schedules = Schedule::leftJoin('contacts', 'crm_schedules.contact_id', '=', 'contacts.id')
            ->where('crm_schedules.business_id', $business_id)
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                return $query->whereBetween(DB::raw('DATE(crm_schedules.start_datetime)'), [$start_date, $end_date]);
            })
            ->select(
                'contacts.name as contact',
                'contacts.mobile as phone_number',
                DB::raw("CONCAT(
                    COALESCE(contacts.address_line_1, ''), ' ', 
                    COALESCE(contacts.address_line_2, ''), ' ', 
                    COALESCE(contacts.city, ''), ' ', 
                    COALESCE(contacts.state, ''), ' ', 
                    COALESCE(contacts.country, ''), ' ', 
                    COALESCE(contacts.zip_code, '')
                ) as address"),
                'crm_schedules.description as description'
            );

        // Apply filters
        if ($request->filled('username_filter')) {
            $schedules->where('contacts.name', 'like', '%' . $request->input('username_filter') . '%');
        }

        if (!auth()->user()->can('superadmin') && !$can_access_all_schedule) {
            $user_id = auth()->user()->id;
            $schedules->whereHas('users', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        // Fetch data
        $no_map = $schedules->get();

        if ($request->ajax()) {
            $formatted_data = $no_map->map(function ($item) {
                return [
                    'contact' => $item->contact ?? 'N/A',
                    'phone_number' => $item->phone_number ?? 'N/A',
                    'address' => trim($item->address) ?? 'N/A',
                    'description' => $item->description ?? 'N/A'
                ];
            });

            return response()->json([
                'success' => true,
                'no_map' => $formatted_data,
                'total' => $formatted_data->count(),
                'message' => $formatted_data->count() > 0 ? 'Data retrieved successfully' : 'No data available for the selected period'
            ]);
        }

        // Get users for dropdown (assuming username_filter uses contact names)
        $users = Contact::where('business_id', $business_id)
            ->select('id', 'name as full_name')
            ->orderBy('name')
            ->get();

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.no_map_report', [
            'no_map' => $no_map,
            'business_name' => Business::where('id', $business_id)->value('name'), // Assuming Business model exists
            'start_date' => $start_date,
            'end_date' => $end_date,
            'users' => $users
        ]);
    }


    public function sellPaymentReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
        if ($request->ajax()) {
            $customer_id = $request->get('supplier_id', null);
            $contact_filter1 = ! empty($customer_id) ? "AND t.contact_id=$customer_id" : '';
            $contact_filter2 = ! empty($customer_id) ? "AND transactions.contact_id=$customer_id" : '';

            $location_id = $request->get('location_id', null);
            $parent_payment_query_part = empty($location_id) ? 'AND transaction_payments.parent_id IS NULL' : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['sell', 'opening_balance']);
            })
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('sell', 'opening_balance') $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('sell', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT CONCAT(COALESCE(CONCAT(c.supplier_business_name, '<br>'), ''), c.name) FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as customer"),
                    'transaction_payments.amount',
                    'transaction_payments.is_return',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.transaction_no',
                    't.invoice_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_payments.id as DT_RowId',
                    'CG.name as customer_group'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->get('customer_group_id'))) {
                $query->where('CG.id', $request->get('customer_group_id'));
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            if (! empty($request->has('commission_agent'))) {
                $query->where('t.commission_agent', $request->get('commission_agent'));
            }

            if (! empty($request->get('payment_types'))) {
                $query->where('transaction_payments.method', $request->get('payment_types'));
            }

            return Datatables::of($query)
                ->editColumn('invoice_no', function ($row) {
                    if (! empty($row->transaction_id)) {
                        return '<a data-href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }
                    if ($row->is_return == 1) {
                        $method .= '<br><small>(' . __('lang_v1.change_return') . ')</small>';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    $amount = $row->is_return == 1 ? -1 * $row->amount : $row->amount;

                    return '<span class="paid-amount" data-orig-value="' . $amount . '" 
                    >' . $this->transactionUtil->num_f($amount, true) . '</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['invoice_no', 'amount', 'method', 'action', 'customer'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);
        $customer_groups = CustomerGroup::forDropdown($business_id, false, true);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers..sell_payment_report')
            ->with(compact('business_locations', 'customers', 'payment_types', 'customer_groups'));
    }



    public function getPurchaseSell(Request $request, DateFilterService $dateFilterService)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start_date, $end_date, $location_id);

            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            $transaction_types = [
                'purchase_return',
                'sell_return',
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id
            );

            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];
            $total_sell_return_inc_tax = $transaction_totals['total_sell_return_inc_tax'];

            $difference = [
                'total' => $sell_details['total_sell_inc_tax'] - $total_sell_return_inc_tax - ($purchase_details['total_purchase_inc_tax'] - $total_purchase_return_inc_tax),
                'due' => $sell_details['invoice_due'] - $purchase_details['purchase_due'],
            ];

            return [
                'purchase' => $purchase_details,
                'sell' => $sell_details,
                'total_purchase_return' => $total_purchase_return_inc_tax,
                'total_sell_return' => $total_sell_return_inc_tax,
                'difference' => $difference,
            ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.purchase_sell')
            ->with(compact('business_locations'));
    }



    public function purchasePaymentReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $supplier_id = $request->get('supplier_id', null);
            $contact_filter1 = ! empty($supplier_id) ? "AND t.contact_id=$supplier_id" : '';
            $contact_filter2 = ! empty($supplier_id) ? "AND transactions.contact_id=$supplier_id" : '';

            $location_id = $request->get('location_id', null);

            $parent_payment_query_part = empty($location_id) ? 'AND transaction_payments.parent_id IS NULL' : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['purchase', 'opening_balance']);
            })
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('purchase', 'opening_balance')  $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('purchase', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })

                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT CONCAT(COALESCE(c.supplier_business_name, ''), '<br>', c.name) FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as supplier"),
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    't.ref_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_no',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            return Datatables::of($query)
                ->editColumn('ref_no', function ($row) {
                    if (! empty($row->ref_no)) {
                        return '<a data-href="' . action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->ref_no . '</a>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="paid-amount" data-orig-value="' . $row->amount . '">' .
                        $this->transactionUtil->num_f($row->amount, true) . '</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['ref_no', 'amount', 'method', 'action', 'supplier'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.purchase_payment_report')
            ->with(compact('business_locations', 'suppliers'));
    }


    public function getCustomerGroup(Request $request)
    {
        if (! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = Transaction::leftjoin('customer_groups AS CG', 'transactions.customer_group_id', '=', 'CG.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->groupBy('transactions.customer_group_id')
                ->select(DB::raw('SUM(final_total) as total_sell'), 'CG.name');

            $group_id = $request->get('customer_group_id', null);
            if (! empty($group_id)) {
                $query->where('transactions.customer_group_id', $group_id);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_sell . '</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.customer_group')
            ->with(compact('customer_group', 'business_locations'));
    }


    public function getSalesRepresentativeReport(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $users = User::allUsersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.sales_representative')
            ->with(compact('users', 'business_locations', 'pos_settings'));
    }

    public function vatSalesReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range from DateFilterService
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query for transactions matching the provided SQL query structure
        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.is_quotation', 0)
            ->whereNull('transactions.deleted_at')
            ->whereBetween('transactions.transaction_date', [$start_date, $end_date])
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->select([
                'transactions.transaction_date as date',
                'transactions.invoice_no',
                'transactions.ref_no as cn_number',
                'contacts.name as contact_name',
                DB::raw("CASE WHEN contacts.tax_number IS NULL OR contacts.tax_number = '' THEN 'បុគ្គលមិនជាប់អាករ' ELSE 'បុគ្គលជាប់អាករ' END as contact_type"),
                DB::raw("COALESCE(contacts.tax_number, 'GC000001') as tax_number"),
                'transactions.final_total',
                'transactions.total_before_tax',
                'transactions.tax_amount',
                DB::raw("CASE WHEN transactions.is_export = 1 THEN transactions.total_before_tax ELSE 0 END as exempt_amount"),
                DB::raw("CASE WHEN transactions.is_export = 1 THEN 0 ELSE transactions.total_before_tax END as domestic_sale"),
                DB::raw("CASE WHEN transactions.is_export = 1 THEN transactions.total_before_tax ELSE 0 END as export_vat"),
                'transactions.additional_expense_value_1 as domestic_sale_tax',
                'transactions.additional_expense_value_2 as withholding_tax',
                'transactions.additional_expense_value_3 as public_lighting_tax',
                'transactions.additional_expense_value_4 as special_goods_tax',
                DB::raw("0 as special_services_tax"),
                DB::raw("0 as accommodation_tax"),
                DB::raw("0 as income_tax_rate"),
                DB::raw("'1%' as notes"),
                DB::raw("CASE WHEN YEAR(transactions.transaction_date) = YEAR(CURRENT_DATE()) THEN 'មិនទាន់ប្រកាសពន្ធ' ELSE 'បានប្រកាសពន្ធ' END as status"),
                DB::raw("'ចំណូលពីការលក់ប្រចាំថ្ងៃ' as description")
            ])
            ->orderBy('transactions.transaction_date', 'desc')
            ->orderBy('transactions.invoice_no', 'desc');

        // Apply location filter if provided
        if ($request->filled('location_id')) {
            $query->where('transactions.location_id', $request->input('location_id'));
        }

        // Apply contact filter if provided
        if ($request->filled('customer_id')) {
            $query->where('transactions.contact_id', $request->input('customer_id'));
        }

        // Get total record count for pagination
        $total_records = $query->count();

        // Get records with pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to query
        $transactions = $query->skip($offset)->take($limit)->get();

        // Get totals from unfiltered query for accurate sums
        $totals = $query->selectRaw('
            SUM(transactions.final_total) as total_final, 
            SUM(transactions.total_before_tax) as total_before_tax,
            SUM(transactions.tax_amount) as total_tax,
            SUM(CASE WHEN transactions.is_export = 1 THEN transactions.total_before_tax ELSE 0 END) as total_exempt
        ')->first();

        // Format data for AJAX response
        if ($request->ajax()) {
            $formatted_data = [];

            foreach ($transactions as $transaction) {
                $formatted_data[] = [
                    'date' => $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') : '',
                    'invoice_no' => $transaction->invoice_no ?? '',
                    'cn_number' => $transaction->cn_number ?? '',
                    'contact_type' => $transaction->contact_type ?? '',
                    'tax_number' => $transaction->tax_number ?? '',
                    'contact_name' => $transaction->contact_name ?? '',
                    'final_total' => $transaction->final_total ?? '',
                    'total_before_tax' => $transaction->total_before_tax ?? '',
                    'exempt_amount' => $transaction->exempt_amount ?? '',
                    'domestic_sale' => $transaction->domestic_sale ?? '',
                    'export_vat' => $transaction->export_vat ?? '',
                    'domestic_sale_tax' => $transaction->domestic_sale_tax ?? '',
                    'withholding_tax' => $transaction->withholding_tax ?? '',
                    'public_lighting_tax' => $transaction->public_lighting_tax ?? '',
                    'special_goods_tax' => $transaction->special_goods_tax ?? '',
                    'special_services_tax' => $transaction->special_services_tax ?? '',
                    'accommodation_tax' => $transaction->accommodation_tax ?? '',
                    'income_tax_rate' => $transaction->income_tax_rate ?? '',
                    'notes' => $transaction->notes ?? '',
                    'description' => $transaction->description ?? '',
                    'status' => $transaction->status ?? ''
                ];
            }

            return response()->json([
                'data' => $formatted_data,
                'total' => $total_records,
                'total_pages' => ceil($total_records / $limit),
                'current_page' => (int)$page,
                'total_final' => $totals->total_final ?? 0,
                'total_before_tax' => $totals->total_before_tax ?? 0,
                'total_tax' => $totals->total_tax ?? 0,
                'total_exempt' => $totals->total_exempt ?? 0
            ]);
        }

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        
        // Get customers for dropdown
        $customers = Contact::customersDropdown($business_id, false);

        // Get business details
        $business = Business::find($business_id);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.vat_sales_report')
            ->with(compact('business_locations', 'customers', 'business'));
    }

    public function monthlyPurchaseLedger(Request $request)
    {
        // Check authorization
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        // Get business ID from session
        $business_id = request()->session()->get('user.business_id');

        // Initialize debug variables
        $debugSql = '';
        $debugBindings = [];
        $using_date_filter = false;

        // Configure pagination
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // Build query
        $query = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->select(
                'transactions.transaction_date as purchase_date',
                'transactions.invoice_no as invoice_number',
                DB::raw('CASE 
                    WHEN contacts.tax_number IS NULL OR contacts.tax_number = "" THEN "បុគ្គលមិនជាប់អាករ"
                    WHEN contacts.is_export = 1 THEN "ក្រុមហ៊ុនក្រៅប្រទេស"
                    ELSE "បុគ្គលជាប់អាករ"
                END AS supplier_type'),
                DB::raw('COALESCE(contacts.tax_number, contacts.mobile) AS tax_id'),
                DB::raw('COALESCE(contacts.supplier_business_name, contacts.name) AS supplier_name'),
                'transactions.final_total as total_amount',
                'transactions.total_before_tax as amount_without_vat',
                DB::raw('(transactions.final_total - COALESCE(transactions.tax_amount, 0)) as non_taxable_amount'),
                DB::raw('0 AS zero_rate_vat'),
                'transactions.tax_amount as domestic_vat',
                DB::raw('0 AS import_vat_10'),
                DB::raw('0 AS non_creditable_vat'),
                DB::raw('0 AS domestic_vat_state'),
                DB::raw('0 AS import_vat_10_state'),
                'transactions.additional_notes as description',
                DB::raw('"មិនទាន់ប្រកាសពន្ធ" AS tax_declaration_status')
            );

        // Handle date filtering
        // $using_date_filter = false; // This line is moved to the variable initialization section

        // Override request parameters to force December 2024 if no specific date is provided
        if ($request->has('date_filter') && !empty($request->input('date_filter')) && $request->input('date_filter') !== 'custom_month_range') {
            $using_date_filter = true;
            $this->dateFilterService->applyDateFilter($query, $request->input('date_filter'), 'transactions.transaction_date');
        } else if ($request->has('start_date') && $request->has('end_date') && !empty($request->input('start_date')) && !empty($request->input('end_date'))) {
            $using_date_filter = true;
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
        } else {
            // Default to December 2024 (hardcoded for data availability)
            $query->whereMonth('transactions.transaction_date', '=', 12);
            $query->whereYear('transactions.transaction_date', '=', 2024);
        }

        // Apply location filter
        if ($request->has('location_id') && !empty($request->input('location_id'))) {
            $query->where('transactions.location_id', $request->input('location_id'));
        }

        // Apply supplier filter
        if ($request->has('supplier_id') && !empty($request->input('supplier_id'))) {
            $query->where('transactions.contact_id', $request->input('supplier_id'));
        }

        // For debugging - Get SQL query string
        $debugSql = $query->toSql();
        $debugBindings = $query->getBindings();

        // Clone query for count before pagination
        $count_query = clone $query;
        $total_records = $count_query->count();

        // Get paginated data
        $transactions = $query->orderBy('transactions.transaction_date', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Get totals from separate query for accurate sums
        $totals_query = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase');
            
        // Apply the same filters to totals query
        if ($using_date_filter) {
            if ($request->has('date_filter') && !empty($request->input('date_filter')) && $request->input('date_filter') !== 'custom_month_range') {
                $this->dateFilterService->applyDateFilter($totals_query, $request->input('date_filter'), 'transactions.transaction_date');
            } else if ($request->has('start_date') && $request->has('end_date') && !empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
                $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
                $totals_query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
            }
        } else {
            // Default to December 2024 (hardcoded for data availability)
            $totals_query->whereMonth('transactions.transaction_date', '=', 12);
            $totals_query->whereYear('transactions.transaction_date', '=', 2024);
        }

        if ($request->has('location_id') && !empty($request->input('location_id'))) {
            $totals_query->where('transactions.location_id', $request->input('location_id'));
        }

        if ($request->has('supplier_id') && !empty($request->input('supplier_id'))) {
            $totals_query->where('transactions.contact_id', $request->input('supplier_id'));
        }

        $totals = $totals_query->selectRaw('
            SUM(transactions.final_total) as total_amount, 
            SUM(transactions.total_before_tax) as total_amount_without_vat,
            SUM(transactions.final_total - COALESCE(transactions.tax_amount, 0)) as total_non_taxable,
            SUM(transactions.tax_amount) as total_domestic_vat
        ')->first();

        // Format data for AJAX response
        if ($request->ajax()) {
            $formatted_data = [];

            foreach ($transactions as $transaction) {
                $formatted_data[] = [
                    'purchase_date' => Carbon::parse($transaction->purchase_date)->format('d-m-Y'),
                    'invoice_number' => $transaction->invoice_number,
                    'supplier_type' => $transaction->supplier_type,
                    'tax_id' => $transaction->tax_id,
                    'supplier_name' => $transaction->supplier_name,
                    'total_amount' => $transaction->total_amount,
                    'amount_without_vat' => $transaction->amount_without_vat,
                    'non_taxable_amount' => $transaction->non_taxable_amount,
                    'zero_rate_vat' => $transaction->zero_rate_vat,
                    'domestic_vat' => $transaction->domestic_vat,
                    'import_vat_10' => $transaction->import_vat_10,
                    'non_creditable_vat' => $transaction->non_creditable_vat,
                    'domestic_vat_state' => $transaction->domestic_vat_state,
                    'import_vat_10_state' => $transaction->import_vat_10_state,
                    'description' => $transaction->description,
                    'tax_declaration_status' => $transaction->tax_declaration_status
                ];
            }

            $response = [
                'data' => $formatted_data,
                'total' => $total_records,
                'total_pages' => ceil($total_records / $limit),
                'current_page' => (int)$page,
                'total_amount' => $totals->total_amount ?? 0,
                'total_amount_without_vat' => $totals->total_amount_without_vat ?? 0,
                'total_non_taxable' => $totals->total_non_taxable ?? 0,
                'total_domestic_vat' => $totals->total_domestic_vat ?? 0,
                'debug_info' => [
                    'sql' => $debugSql,
                    'bindings' => $debugBindings,
                    'business_id' => $business_id,
                    'using_date_filter' => $using_date_filter,
                    'original_request' => [
                        'date_filter' => $request->input('date_filter'),
                        'start_date' => $request->input('start_date'),
                        'end_date' => $request->input('end_date')
                    ]
                ]
            ];
            
            
            return response()->json($response);
        }

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        
        // Get suppliers for dropdown
        $suppliers = Contact::suppliersDropdown($business_id, false);

        // Get business details
        $business = Business::find($business_id);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.monthly_purchase_ledger')
            ->with(compact('business_locations', 'suppliers', 'business'));
    }

    // Add the withholding tax report method
    public function withholdingTaxReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // dd($business_id);
        // Get date range from DateFilterService
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query using the provided SQL structure
        $query = DB::table('transactions as t')
            ->leftJoin('expense_categories as ec', 't.expense_category_id', '=', 'ec.id')
            ->leftJoin('exchangerate_main as er', function ($join) {
                $join->on(DB::raw('DATE(t.transaction_date)'), '=', 'er.date_1')
                    ->orWhere(function ($query) {
                        $query->whereNull('er.date_1')
                            ->orderBy('er.date_1', 'desc')
                            ->limit(1);
                    });
            })
            ->where('t.business_id', $business_id)
            ->where(function($q) {
                $q->where('ec.name', 'LIKE', '%ចំណាយជួលអាគារ%');
            })
            ->where('t.status', 'final')
            ->where('t.type', 'expense')
            ->select([
                DB::raw('DATE(t.transaction_date) AS date'),
                't.invoice_no',
                DB::raw("COALESCE(ec.name, 'ចំណាយជួលផ្ទះ') AS contact_name"),
                DB::raw("'' AS contact_type"),
                DB::raw("'' AS tax_number"),
                DB::raw("'រូបវន្ដបុគ្គល' AS tax_residence_type"),
                't.final_total AS payable_amount',
                DB::raw('ROUND(t.final_total * 0.10, 2) AS tax_amount'), // 10% withholding tax, rounded to 2 decimal places
                DB::raw("'' AS certificate_number"),
                DB::raw("'មិនទាន់ប្រកាសពន្ធ' AS tax_status"),
                DB::raw("'១០%' AS tax_rate"),
                DB::raw("'ការបង់ថ្លៃឈ្នួលចលនទ្រព្យ និង អចលនទ្រព្យ(រូបវន្ដបុគ្គល)' AS description"),
                DB::raw("COALESCE(er.KHR_3, 0) AS exchange_rate")
            ])
            ->orderBy('t.transaction_date', 'desc');

        // Apply date filter if provided
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('t.transaction_date', [$start_date, $end_date]);
        }

        // Apply location filter if provided
        if ($request->filled('location_id')) {
            $query->where('t.location_id', $request->input('location_id'));
        }

        // Get total record count for pagination
        $total_records = $query->count();

        // Get records with pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to query
        $transactions = $query->skip($offset)->take($limit)->get();

        // Calculate totals
        $totals = (object)[
            'total_payable_amount' => $transactions->sum('payable_amount'),
            'total_tax_amount' => $transactions->sum('tax_amount')
        ];

        // Format data for AJAX response
        if ($request->ajax()) {
            $formatted_data = [];

            foreach ($transactions as $transaction) {
                $formatted_data[] = [
                    'date' => $transaction->date ? Carbon::parse($transaction->date)->format('d-m-Y') : date('d-m-Y'),
                    'invoice_no' => $transaction->invoice_no ?? '',
                    'tax_residence_type' => $transaction->tax_residence_type ?? 'ពន្ធកាត់ទុកលើថ្លៃឈ្នួល (រូបវន្ដបុគ្គល) ១០%',
                    'contact_type' => $transaction->contact_type ?? 'អ្នកផ្គត់ផ្គង់ ទូទៅ',
                    'tax_number' => $transaction->tax_number ?? '',
                    'contact_name' => $transaction->contact_name ?? '',
                    'transaction_type' => $transaction->tax_residence_type ?? 'ពន្ធកាត់ទុកលើថ្លៃឈ្នួល (រូបវន្ដបុគ្គល) ១០%',
                    'tax_rate' => $transaction->tax_rate ?? '១០%',
                    'payable_amount' => $transaction->payable_amount ?? 0,
                    'tax_amount' => $transaction->tax_amount ?? 0,
                    'certificate_number' => $transaction->certificate_number ?? '',
                    'tax_status' => $transaction->tax_status ?? 'មិនទាន់ប្រកាសពន្ធ',
                    'description' => $transaction->description ?? 'ការបង់ថ្លៃឈ្នួលចលនទ្រព្យ និង អចលនទ្រព្យ(រូបវន្ដបុគ្គល)',
                    'exchange_rate' => $transaction->exchange_rate ?? $exchange_rate
                ];
            }

            return response()->json([
                'data' => $formatted_data,
                'total' => $total_records,
                'total_pages' => ceil($total_records / $limit),
                'current_page' => (int)$page,
                'total_payable_amount' => $totals->total_payable_amount ?? 0,
                'total_tax_amount' => $totals->total_tax_amount ?? 0,
                'exchange_rate' => DB::table('exchangerate_main')
                    ->orderBy('date_1', 'desc')
                    ->value('KHR_3') ?? 4100 // Default to 4100 if no exchange rate found
            ]);
        }

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        
        // Get suppliers for dropdown
        $suppliers = Contact::suppliersDropdown($business_id, false);

        // Get tax types for dropdown
        $tax_types = [
            'resident' => __('minireportb1::minireportb1.resident'),
            'non_resident' => __('minireportb1::minireportb1.non_resident')
        ];

        // Get business details
        $business = Business::find($business_id);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.withholding_tax_report')
            ->with(compact('business_locations', 'suppliers', 'tax_types', 'business'));
    }

    /**
     * Display the rental invoice based on withholding tax data
     */
    public function rentalInvoice(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get current month and year
        $current_date = Carbon::now();
        $month = $current_date->month;
        $year = $current_date->year;
        
        // Khmer month names
        $khmer_months = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 
                         'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        $khmer_month = $khmer_months[$month - 1];

        // Get the most recent exchange rate from the database
        $exchangeRate = \App\ExchangeRate::where('business_id', $business_id)
            ->orderBy('date_1', 'desc')
            ->first();

        // Use the exchange rate from the database or default to 4025
        $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4025;
        
        // Get data from the specific account
        $query = DB::table('accounts as a')
            ->leftJoin('account_transactions as tr', 'a.id', '=', 'tr.account_id')
            ->leftJoin('exchange_rates as er', function ($join) {
                $join->on(DB::raw('CURRENT_DATE()'), '>=', 'er.date_1')
                    ->orderBy('er.date_1', 'desc')
                    ->limit(1);
            })
            ->where('a.business_id', $business_id)
            ->where('a.id', 550) // Targeting the specific account
            ->select([
                'a.id',
                'a.account_number as លេខគណនី',
                'a.name as ឈ្មោះ',
                DB::raw("COALESCE(SUM(CASE WHEN tr.type = 'credit' THEN tr.amount WHEN tr.type = 'debit' THEN -tr.amount ELSE 0 END), 600.0000) as សមតុល្យ"),
                DB::raw("COALESCE(er.KHR_3, 4025) as exchange_rate")
            ])
            ->groupBy('a.id', 'a.account_number', 'a.name', 'er.KHR_3');
            
        $accountData = $query->first();
        
        // Default amount is 600 if no account data found
        $amount = $accountData ? $accountData->សមតុល្យ : 600.00;
        $exchange_rate = $accountData ? $accountData->exchange_rate : 4025;

        // For AJAX requests (not needed in this updated version, but kept for backward compatibility)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => [
                    [
                        'date' => $current_date->format('Y-m-d'),
                        'invoice_no' => $accountData ? $accountData->លេខគណនី : '',
                        'contact_name' => $accountData ? $accountData->ឈ្មោះ : 'វិទ្យាស្ថានបច្ចេកវិទ្យាកម្ពុជា',
                        'tax_number' => '004 លីម ស្រីពេជ្រ',
                        'payable_amount' => $amount,
                        'description' => "ចំណាយលើការផ្សព្វផ្សាយ",
                        'exchange_rate' => $exchange_rate
                    ]
                ],
                'total' => 1,
                'total_pages' => 1,
                'current_page' => 1
            ]);
        }

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.rental_invoice')
            ->with(compact('exchange_rate', 'amount', 'khmer_month'));
    }

    public function purchasesExpensesReport(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range from DateFilterService
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query for due purchases and expenses
        $query = Transaction::leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->join('business_locations as bs', 'transactions.location_id', '=', 'bs.id')
            ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
            ->leftJoin('transactions as pr', 'transactions.id', '=', 'pr.return_parent_id')
            ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.payment_status', 'due');
        
        // Apply type filter
        if ($request->filled('type') && in_array($request->input('type'), ['purchase', 'expense'])) {
            $query->where('transactions.type', $request->input('type'));
        } else {
            $query->whereIn('transactions.type', ['purchase', 'expense']);
        }
        
        $query->select([
            'transactions.id',
            'transactions.transaction_date as date',
            'transactions.ref_no as tran_no',
            'bs.name as location',
            DB::raw('COALESCE(c.supplier_business_name, c.name) as contact_or_supplier_name'),
            'transactions.payment_status',
            'transactions.final_total',
            'transactions.type'
        ])
        ->groupBy(
            'transactions.id',
            'transactions.transaction_date',
            'transactions.ref_no',
            'bs.name',
            'c.supplier_business_name',
            'c.name',
            'transactions.payment_status',
            'transactions.final_total',
            'transactions.type'
        )
        ->orderBy('transactions.transaction_date', 'desc');

        // Apply date filter
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
        }

        // Apply location filter
        if ($request->filled('location_id')) {
            $query->where('transactions.location_id', $request->input('location_id'));
        }

        // Apply supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('transactions.contact_id', $request->input('supplier_id'));
        }
        
        // Get total record count for pagination
        $total_records = $query->count();

        // Get records with pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to query
        $transactions = $query->skip($offset)->take($limit)->get();

        // Get totals with the same filters
        $totalsQuery = clone $query;
        $totals = $totalsQuery->selectRaw('SUM(transactions.final_total) as total_amount')->first();

        // Format data for AJAX response
        if ($request->ajax()) {
            $formatted_data = [];

            foreach ($transactions as $transaction) {
                $formatted_data[] = [
                    'id' => $transaction->id,
                    'date' => $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') : '',
                    'tran_no' => $transaction->tran_no ?? '',
                    'location' => $transaction->location ?? '',
                    'contact_or_supplier_name' => $transaction->contact_or_supplier_name ?? '',
                    'payment_status' => $transaction->payment_status ?? '',
                    'final_total' => $transaction->final_total ?? 0,
                    'type' => ucfirst($transaction->type) ?? ''
                ];
            }

            return response()->json([
                'data' => $formatted_data,
                'total' => $total_records,
                'total_pages' => ceil($total_records / $limit),
                'current_page' => (int)$page,
                'total_amount' => $totals->total_amount ?? 0
            ]);
        }

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        
        // Get suppliers for dropdown
        $suppliers = Contact::suppliersDropdown($business_id, false);

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.purchases_expenses_report')
            ->with(compact('business_locations', 'suppliers'));
    }

    /**
     * Display the P101 tax form
     */
    public function p101TaxForm(Request $request)
    {
        if (!auth()->user()->can('tax.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::find($business_id);
        
        // Get current month and year
        $current_date = Carbon::now();
        $month = $current_date->format('m');
        $year = $current_date->format('Y');

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('minireportb1::MiniReportB1.gov_tax.p101_tax_form', compact(
            'business',
            'month',
            'year',
            'business_locations'
        ));
    }

    /**
     * Process P101 tax form submission
     */
    public function processP101TaxForm(Request $request)
    {
        if (!auth()->user()->can('tax.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            
            // Validate the request
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'company_name_latin' => 'required|string|max:255',
                'form_number' => 'required|string|max:50',
                'date' => 'required|date',
                'tin' => 'required|string|max:20',
                'department' => 'required|in:large,branch',
                'month' => 'required|numeric|min:1|max:12',
                'year' => 'required|numeric|min:2000|max:2100',
                'tax_items' => 'required|array',
                'tax_items.*.tax_type' => 'required|string',
                'tax_items.*.tax_amount' => 'required|numeric|min:0',
                'tax_items.*.account_number' => 'required|string|max:50',
                'tax_items.*.additional_tax' => 'required|numeric|min:0',
                'tax_items.*.interest' => 'required|numeric|min:0',
                'tax_items.*.additional_account' => 'required|string|max:50',
                'tax_items.*.total_amount' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                    'errors' => $validator->errors()
                ], 400);
            }

            // Process the tax form data
            DB::beginTransaction();

            // Create tax form record
            $tax_form = [
                'business_id' => $business_id,
                'company_name' => $request->input('company_name'),
                'company_name_latin' => $request->input('company_name_latin'),
                'form_number' => $request->input('form_number'),
                'date' => $request->input('date'),
                'tin' => $request->input('tin'),
                'department' => $request->input('department'),
                'month' => $request->input('month'),
                'year' => $request->input('year'),
                'total_amount' => array_sum(array_column($request->input('tax_items'), 'total_amount')),
                'created_by' => auth()->user()->id
            ];

            // Save tax form
            $tax_form_id = DB::table('p101_tax_forms')->insertGetId($tax_form);

            // Save tax items
            $tax_items = [];
            foreach ($request->input('tax_items') as $item) {
                $tax_items[] = [
                    'p101_tax_form_id' => $tax_form_id,
                    'tax_type' => $item['tax_type'],
                    'tax_amount' => $item['tax_amount'],
                    'account_number' => $item['account_number'],
                    'additional_tax' => $item['additional_tax'],
                    'interest' => $item['interest'],
                    'additional_account' => $item['additional_account'],
                    'total_amount' => $item['total_amount'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            DB::table('p101_tax_form_items')->insert($tax_items);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => __('messages.saved_successfully'),
                'tax_form_id' => $tax_form_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ], 500);
        }
    }

    /**
     * Get P101 tax form data
     */
    public function getP101TaxFormData(Request $request)
    {
        if (!auth()->user()->can('tax.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        
        $query = DB::table('p101_tax_forms')
            ->where('business_id', $business_id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('month')) {
            $query->where('month', $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        $tax_forms = $query->get();

        return response()->json([
            'data' => $tax_forms
        ]);
    }

    public function customerReportViaStaff(Request $request)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

       
        // Base query
        $query = DB::table('users')
            ->leftJoin('user_contact_access', 'users.id', '=', 'user_contact_access.user_id')
            ->where('users.business_id', $business_id)
            ->where('users.status', 'active');



        // Apply user filter if specified
        if ($request->filled('user_id')) {
            $query->where('users.id', $request->input('user_id'));
        }

        // Select and group by statement
        $query->select([
            'users.id',
            DB::raw("COALESCE(
                NULLIF(
                    CONCAT(
                        COALESCE(users.surname, ''), 
                        ' ', 
                        COALESCE(users.first_name, ''), 
                        ' ', 
                        COALESCE(users.last_name, '')
                    ), 
                ''
            ), 
            users.username) AS employee_name"),
            DB::raw("COUNT(user_contact_access.contact_id) AS contact_count")
        ])
        ->groupBy('users.id', 'users.surname', 'users.first_name', 'users.last_name', 'users.username');

        // Order by contact count
        $query->orderBy(DB::raw("COUNT(user_contact_access.contact_id)"), 'desc');

        // Get total record count for pagination
        $total_records = $query->count();

        // Get records with pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to query
        $staff = $query->skip($offset)->take($limit)->get();

        // Format data for AJAX response
        if ($request->ajax()) {
            $formatted_data = [];

            foreach ($staff as $employee) {
                $formatted_data[] = [
                    'id' => $employee->id,
                    'employee_name' => $employee->employee_name,
                    'contact_count' => $employee->contact_count
                ];
            }

            return response()->json([
                'data' => $formatted_data,
                'total' => $total_records,
                'total_pages' => ceil($total_records / $limit),
                'current_page' => (int)$page
            ]);
        }

        // Get users for dropdown
        $users = User::where('business_id', $business_id)
                    ->where('status', 'active')
                    ->select(DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"), 'id')
                    ->pluck('name', 'id')
                    ->toArray();

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.customer_report_via_staff')
            ->with(compact('users'));
    }

    public function getewht(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range from DateFilterService
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query for withholding tax data
        $query = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
            ->leftJoin('tax_rates', 'transactions.tax_id', '=', 'tax_rates.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->where('transactions.status', 'final')
            ->whereNotNull('transactions.additional_expense_value_2') // withholding tax field
            ->select([
                'transactions.transaction_date as date',
                'transactions.invoice_no',
                'transactions.additional_expense_value_2 as withholding_tax',
                'transactions.final_total as payable_amount',
                'contacts.name as contact_name',
                'contacts.tax_number',
                'contacts.type as contact_type',
                'tax_rates.name as tax_type',
                'tax_rates.amount as tax_rate',
                'transactions.additional_expense_value_1 as certificate_number',
                DB::raw("CASE 
                    WHEN YEAR(transactions.transaction_date) = YEAR(CURRENT_DATE()) 
                    THEN 'មិនទាន់ប្រកាសពន្ធ' 
                    ELSE 'បានប្រកាសពន្ធ' 
                END as tax_status")
            ]);

        // Apply date filter
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
        }

        // Apply location filter
        if ($request->filled('location_id')) {
            $query->where('transactions.location_id', $request->input('location_id'));
        }

        // Apply supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('transactions.contact_id', $request->input('supplier_id'));
        }

        // Apply tax type filter
        if ($request->filled('tax_type')) {
            $query->where('tax_rates.name', 'like', '%' . $request->input('tax_type') . '%');
        }

        // Get total record count for pagination
        $total_records = $query->count();

        // Get records with pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to query
        $transactions = $query->skip($offset)->take($limit)->get();

        // Get totals
        $totals = $query->selectRaw('
            SUM(transactions.final_total) as total_payable_amount,
            SUM(transactions.additional_expense_value_2) as total_withholding_tax
        ')->first();

        // Format data for response
        $formatted_data = [];
        foreach ($transactions as $transaction) {
            $formatted_data[] = [
                'date' => $transaction->date ? Carbon::parse($transaction->date)->format('d-m-Y') : '',
                'invoice_no' => $transaction->invoice_no ?? '',
                'tax_type' => $transaction->tax_type ?? '',
                'contact_type' => $transaction->contact_type ?? '',
                'tax_number' => $transaction->tax_number ?? '',
                'contact_name' => $transaction->contact_name ?? '',
                'transaction_type' => $transaction->tax_type ?? '',
                'tax_rate' => $transaction->tax_rate ? number_format($transaction->tax_rate, 0) . '%' : '',
                'payable_amount' => $transaction->payable_amount ?? 0,
                'withholding_tax' => $transaction->withholding_tax ?? 0,
                'certificate_number' => $transaction->certificate_number ?? '',
                'tax_status' => $transaction->tax_status ?? ''
            ];
        }

        // Get business locations for dropdown
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        
        // Get suppliers for dropdown
        $suppliers = Contact::suppliersDropdown($business_id, false);

        // Get tax types for dropdown
        $tax_types = [
            'resident' => __('minireportb1::minireportb1.resident'),
            'non_resident' => __('minireportb1::minireportb1.non_resident')
        ];

        if ($request->ajax()) {
            return response()->json([
                'data' => $formatted_data,
                'total' => $total_records,
                'total_pages' => ceil($total_records / $limit),
                'current_page' => (int)$page,
                'total_payable_amount' => $totals->total_payable_amount ?? 0,
                'total_withholding_tax' => $totals->total_withholding_tax ?? 0
            ]);
        }

        return view('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.e_wht')
            ->with(compact('business_locations', 'suppliers', 'tax_types', 'formatted_data', 'total_records', 'totals'));
    }

    public function getWithholdingTaxData(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get date range
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Get latest exchange rate
        $latest_exchange_rate = DB::table('exchangerate_main')
            ->select('KHR_3')
            ->orderBy('date_1', 'desc')
            ->first();
        
        $exchange_rate = $latest_exchange_rate ? $latest_exchange_rate->KHR_3 : 4100;

        // Base query for withholding tax data - using transactions with withholding tax
        $query = DB::table('transactions as t')
            ->leftJoin('contacts as c', 't.contact_id', '=', 'c.id')
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->leftJoin('tax_rates as tr', 't.tax_id', '=', 'tr.id')
            ->leftJoin('expense_categories as ec', 't.expense_category_id', '=', 'ec.id')
            ->leftJoin('exchangerate_main as er', function ($join) {
                $join->on(DB::raw('DATE(t.transaction_date)'), '=', 'er.date_1')
                    ->orWhere(function ($query) {
                        $query->whereNull('er.date_1')
                            ->orderBy('er.date_1', 'desc')
                            ->limit(1);
                    });
            })
            ->where('t.business_id', $business_id)
            ->where('t.status', 'final')
            ->where(function($q) {
                $q->where('t.type', 'sell')
                ->where('ec.name', 'like', '%ឈ្នួល%')
                  ->orWhere('t.type', 'purchase')
                  ->orWhere('t.type', 'expense');
            })
            ->select([
                DB::raw('DATE(t.transaction_date) AS date'),
                't.invoice_no',
                DB::raw('ROUND(t.final_total * 0.10, 4) as withholding_tax'), // Calculate 10% withholding tax
                't.final_total as payable_amount',
                'c.name as contact_name',
                'c.tax_number',
                'c.type as contact_type',
                DB::raw("'ពន្ធកាត់ទុក' as tax_type"),
                DB::raw("'10%' as tax_rate"),
                DB::raw("COALESCE(t.additional_expense_value_1, '0.0000') as certificate_number"),
                DB::raw("CASE 
                    WHEN YEAR(t.transaction_date) = YEAR(CURRENT_DATE()) 
                    THEN 'មិនទាន់ប្រកាសពន្ធ' 
                    ELSE 'បានប្រកាសពន្ធ' 
                END as tax_status"),
                DB::raw("'ពន្ធកាត់ទុក' as transaction_type"),
                'bl.name as location_name',
                DB::raw("COALESCE(er.KHR_3, {$exchange_rate}) as exchange_rate") // Get exchange rate or use default
            ]);

        // Apply date filter
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('t.transaction_date', [$start_date, $end_date]);
        }

        // Apply location filter
        if ($request->filled('location_filter')) {
            $query->where('t.location_id', $request->input('location_filter'));
        }

        // Apply supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('t.contact_id', $request->input('supplier_id'));
        }

        // Apply tax type filter
        if ($request->filled('tax_type')) {
            $tax_type = $request->input('tax_type');
            if ($tax_type === 'resident') {
                $query->where('c.is_export', 0);
            } elseif ($tax_type === 'non_resident') {
                $query->where('c.is_export', 1);
            }
        }

        // Clone the query for totals
        $totals_query = clone $query;

        // Get total record count for pagination
        $total_records = $query->count();

        // Get records with pagination
        $page = (int)$request->input('page', 1);
        $limit = (int)$request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        // Apply pagination to query
        $transactions = $query->skip($offset)->take($limit)->get();

        // Calculate totals
        $totals = $totals_query->selectRaw('
            SUM(t.final_total) as total_payable_amount,
            SUM(ROUND(t.final_total * 0.10, 4)) as total_withholding_tax
        ')->first();

        // Format data for response
        $formatted_data = [];
        foreach ($transactions as $transaction) {
            $formatted_data[] = [
                'date' => $transaction->date ?? '',
                'invoice_no' => $transaction->invoice_no ?? '',
                'tax_type' => $transaction->tax_type ?? '',
                'contact_type' => $transaction->contact_type ?? '',
                'tax_number' => $transaction->tax_number ?? '',
                'contact_name' => $transaction->contact_name ?? '',
                'transaction_type' => $transaction->transaction_type ?? '',
                'tax_rate' => $transaction->tax_rate ?? '',
                'payable_amount' => $transaction->payable_amount ?? 0,
                'withholding_tax' => $transaction->withholding_tax ?? 0,
                'certificate_number' => $transaction->certificate_number ?? '',
                'tax_status' => $transaction->tax_status ?? '',
                'exchange_rate' => $transaction->exchange_rate ?? $exchange_rate
            ];
        }

        // Format response data
        $response = [
            'data' => $formatted_data,
            'total' => $total_records,
            'total_pages' => ceil($total_records / $limit),
            'current_page' => $page,
            'total_payable_amount' => $totals->total_payable_amount ?? 0,
            'total_withholding_tax' => $totals->total_withholding_tax ?? 0,
            'exchange_rate' => $exchange_rate
        ];

        return response()->json($response);
    }
}
