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
use App\Contact;

class CustomerReportsController extends Controller
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

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_schedule = auth()->user()->can('crm.access_all_schedule');
        $can_access_own_schedule = auth()->user()->can('crm.access_own_schedule');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_schedule || $can_access_own_schedule)) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $schedules = Schedule::leftjoin('contacts', 'crm_schedules.contact_id', '=', 'contacts.id')
                ->leftjoin('users as U', 'crm_schedules.created_by', '=', 'U.id')
                ->leftjoin('categories as C', 'crm_schedules.followup_category_id', '=', 'C.id')
                ->with(['users'])
                ->where('crm_schedules.business_id', $business_id)
                ->select(
                    'crm_schedules.*',
                    'contacts.name as contact',
                    'contacts.supplier_business_name as biz_name',
                    'U.surname',
                    'U.first_name',
                    'U.last_name',
                    'crm_schedules.status as status',
                    'crm_schedules.created_at as added_on',
                    'contacts.type as contact_type',
                    'contacts.id as contact_id',
                    'C.name as followup_category'
                );

            if (request()->input('is_recursive') == 1) {
                $schedules->where('crm_schedules.is_recursive', 1);
            } else {
                $schedules->where('crm_schedules.is_recursive', 0);
            }

            if (!empty(request()->input('contact_id'))) {
                $schedules->where('crm_schedules.contact_id', request()->input('contact_id'));
            }

            if (!empty(request()->input('assgined_to'))) {
                $user_id = request()->input('assgined_to');
                $schedules->whereHas('users', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            }

            if (!empty(request()->input('status'))) {
                if (request()->input('status') == 'none') {
                    $schedules->whereNull('crm_schedules.status');
                } else {
                    $schedules->where('crm_schedules.status', request()->input('status'));
                }
            }

            if (!empty(request()->input('schedule_type'))) {
                $schedules->where('crm_schedules.schedule_type', request()->input('schedule_type'));
            }

            if (!empty(request()->input('followup_category_id'))) {
                $schedules->where('crm_schedules.followup_category_id', request()->input('followup_category_id'));
            }

            if (!empty(request()->input('start_date_time')) && !empty(request()->input('end_date_time'))) {
                $start_date = request()->input('start_date_time');
                $end_date = request()->input('end_date_time');
                $schedules->whereBetween(DB::raw('date(start_datetime)'), [$start_date, $end_date]);
            }

            if (!empty(request()->input('follow_up_by'))) {
                $schedules->where('crm_schedules.follow_up_by', request()->input('follow_up_by'));
            }

            if (!auth()->user()->can('superadmin') && !$can_access_all_schedule) {
                $user_id = auth()->user()->id;
                $schedules->whereHas('users', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            }

            return Datatables::of($schedules)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                                <button class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info tw-w-max dropdown-toggle" type="button"  data-toggle="dropdown" aria-expanded="false">
                                    ' . __('messages.action') . '
                                    <span class="caret"></span>
                                    <span class="sr-only">'
                        . __('messages.action') . '
                                    </span>
                                </button>
                                  <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    // <li>
                    //      <a href="' . action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'show'], ['follow_up' => $row->id]) . '" class="cursor-pointer view_schedule">
                    //          <i class="fa fa-eye"></i>
                    //          '.__("messages.view").'
                    //      </a>
                    //  </li>';
                    if ($row->is_recursive != 1) {
                        $html .= '<li>
                                        <a data-schedule_id="' . $row->id . '"class="cursor-pointer view_schedule_log">
                                            <i class="fa fa-eye"></i>
                                            ' . __('crm::lang.view_follow_up') . '
                                        </a>
                                    </li>';

                        $html .= '<li>
                                        <a data-href="' . action([\Modules\Crm\Http\Controllers\ScheduleLogController::class, 'create'], ['schedule_id' => $row->id]) . '"class="cursor-pointer schedule_log_add">
                                            <i class="fa fa-edit"></i>
                                            ' . __('crm::lang.add_schedule_log') . '
                                        </a>
                                    </li>';

                        $html .= '<li>
                                        <a data-href="' . action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'edit'], ['follow_up' => $row->id]) . '"class="cursor-pointer schedule_edit">
                                            <i class="fa fa-edit"></i>
                                            ' . __('messages.edit') . '
                                        </a>
                                    </li>';
                    }

                    $html .= '<li>
                                        <a data-href="' . action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'destroy'], ['follow_up' => $row->id]) . '" class="cursor-pointer schedule_delete">
                                            <i class="fas fa-trash"></i>
                                            ' . __('messages.delete') . '
                                        </a>
                                    </li>';

                    $html .= '</ul>
                            </div>';

                    return $html;
                })
                ->editColumn('start_datetime', ' @if(!empty($start_datetime))
                    {{@format_datetime($start_datetime)}}<br>
                    <i>(<span class="time-from-now">{{$start_datetime}}</span>)</i> @endif
                ')
                ->editColumn('end_datetime', '
                    @if(!empty($end_datetime)){{@format_datetime($end_datetime)}} @endif
                ')
                ->editColumn('contact', '
                    @if(!empty($biz_name)) {{$biz_name}},<br>@endif {{$contact}}
                    <br>
                    @if($contact_type == "lead")
                        <a href="{{action(\'\Modules\Crm\Http\Controllers\LeadController@show\', [\'lead\' => $contact_id])}}" target="_blank">
                            <i class="fas fa-external-link-square-alt text-info"></i>
                        </a>
                    @else
                    @if(!empty($contact_id))
                    <a href="{{action(\'App\Http\Controllers\ContactController@show\', [$contact_id])}}" target="_blank">
                            <i class="fas fa-external-link-square-alt text-info"></i>
                        </a>
                    @endif
                    @endif
                ')
                ->addColumn('added_by', function ($row) {
                    return "{$row->surname} {$row->first_name} {$row->last_name}";
                })
                ->addColumn('additional_info', function ($row) {
                    $html = '';
                    $infos = $row->followup_additional_info;
                    if (!empty($infos)) {
                        foreach ($infos as $key => $value) {
                            $html .= $key . ' : ' . $value . '<br>';
                        }
                    }

                    return $html;
                })
                ->editColumn('added_on', '
                    {{@format_datetime($added_on)}}
                ')
                ->editColumn('schedule_type', function ($row) {
                    $html = '';
                    if (!empty($row->schedule_type)) {
                        $html = '<div class="schedule_type" data-orig-value="' . __('crm::lang.' . $row->schedule_type) . '" data-status-name="' . __('crm::lang.' . $row->schedule_type) . '">
                                    ' . __('crm::lang.' . $row->schedule_type) .
                            '</div>';
                    }

                    return $html;
                })
                ->editColumn('users', function ($row) {
                    $html = '&nbsp;';
                    if ($row->users->count() > 0) {
                        foreach ($row->users as $user) {
                            if (isset($user->media->display_url)) {
                                $html .= '<img class="user_avatar" src="' . $user->media->display_url . '" data-toggle="tooltip" title="' . $user->user_full_name . '">';
                            } else {
                                $html .= '<img class="user_avatar" src="https://ui-avatars.com/api/?name=' . $user->first_name . '" data-toggle="tooltip" title="' . $user->user_full_name . '">';
                            }
                        }
                    }

                    return $html;
                })
                ->editColumn('status', function ($row) {
                    $html = '';
                    if (!empty($row->status)) {
                        $html = '<span class="text-center label status ' . $this->status_bg[$row->status] . '" data-orig-value="' . __('crm::lang.' . $row->status) . '" data-status-name="' . __('crm::lang.' . $row->status) . '"><small>
                                    ' . __('crm::lang.' . $row->status) .
                            '</small></span>';
                    }

                    return $html;
                })
                ->editColumn('follow_up_by', function ($row) {
                    $follow_up_by = '';

                    if ($row->follow_up_by == 'payment_status') {
                        $follow_up_by = __('sale.payment_status') . ' - ' . __('lang_v1.' . $row->follow_up_by_value);
                    } elseif ($row->follow_up_by == 'orders') {
                        $follow_up_by = __('restaurant.orders') . ' - ' . __('crm::lang.has_no_transactions');
                    }

                    return $follow_up_by;
                })
                ->removeColumn('id')
                ->rawColumns([
                    'action', 'start_datetime', 'end_datetime', 'users', 'contact', 'added_on',
                    'additional_info', 'schedule_type', 'status', 'description',
                ])
                ->make(true);
        }

        $leads = CrmContact::leadsDropdown($business_id, false);
        $contacts = Contact::customersDropdown($business_id, false)->toArray();

        foreach ($contacts as $key => $value) {
            $contacts[$key] = $value . ' (' . __('contact.customer') . ')';
        }
        foreach ($leads as $key => $value) {
            $contacts[$key] = $value . ' (' . __('crm::lang.lead') . ')';
        }

        $assigned_to = User::forDropdown($business_id, false);
        $statuses = Schedule::statusDropdown(true);
        $follow_up_types = Schedule::followUpTypeDropdown();

        // Set default user from get parameter
        $default_user = request()->input('assigned_to', null);

        // Set default status from get parameter
        $default_status = request()->input('status', null);

        // Set default date from get parameter
        $default_start_date = request()->input('start_date', null);
        $default_end_date = request()->input('end_date', null);

        $default_followup_category_id = request()->input('followup_category_id', null);

        $followup_category = Category::forDropdown($business_id, 'followup_category');

        return view('crm::schedule.index')
            ->with(compact(
                'contacts',
                'assigned_to',
                'statuses',
                'follow_up_types',
                'default_start_date',
                'default_end_date',
                'default_status',
                'default_user',
                'followup_category',
                'default_followup_category_id'
            ));
    }

    public function franchiseMonthlyReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        // Default to "This Month" if no filter is applied
        $dateFilter = $request->input('date_filter', 'this_month');
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Adjust date range based on filter
        switch ($dateFilter) {
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'last_2_months':
                $startDate = Carbon::now()->subMonths(2)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_3_months':
                $startDate = Carbon::now()->subMonths(3)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_6_months':
                $startDate = Carbon::now()->subMonths(6)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_quarter':
                $startDate = Carbon::now()->startOfQuarter();
                $endDate = Carbon::now()->endOfQuarter();
                break;
            case 'last_quarter':
                $startDate = Carbon::now()->subQuarter()->startOfQuarter();
                $endDate = Carbon::now()->subQuarter()->endOfQuarter();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'last_year':
                $startDate = Carbon::now()->subYear()->startOfYear();
                $endDate = Carbon::now()->subYear()->endOfYear();
                break;
            case 'custom_month_range':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : $startDate;
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : $endDate;
                break;
            case '':
                $startDate = null;
                $endDate = null;
                break;
        }

        $start_date = $startDate ? $startDate->toDateString() : null;
        $end_date = $endDate ? $endDate->toDateString() : null;

        // Dynamic values for the header with full date format
        if ($dateFilter === '') {
            $month = 'All';
            $firstContact = Contact::where('business_id', $business_id)
                ->orderBy('created_at', 'asc')
                ->first();
            $lastContact = Contact::where('business_id', $business_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $start_day = $firstContact ? Carbon::parse($firstContact->created_at)->format('d/m/Y') : 'N/A';
            $end_day = $lastContact ? Carbon::parse($lastContact->created_at)->format('d/m/Y') : 'N/A';
        } else {
            $month = $startDate ? $startDate->format('m') : 'All';
            $start_day = $startDate ? $startDate->format('d/m/Y') : 'N/A';
            $end_day = $endDate ? $endDate->format('d/m/Y') : 'N/A';
        }

        $customer_groups = CustomerGroup::where('business_id', $business_id)
            ->pluck('name', 'id');

        $assigned_users = User::select('id', 'surname', 'first_name', 'last_name')
            ->where('business_id', $business_id)
            ->get();

        // Base query for contacts
        $contactsQuery = Contact::select([
            'contacts.id',
            'contacts.supplier_business_name',
            'contacts.name',
            'contacts.customer_group_id',
            'contacts.mobile',
            'contacts.address_line_1',
            'contacts.address_line_2',
            'contacts.city',
            'contacts.created_by',
            'contacts.register_date',
            'contacts.created_at',
            'contacts.expired_date'
        ])
        ->where('contacts.business_id', $business_id)
        ->when($startDate && $endDate, fn($query) => $query->whereBetween('contacts.created_at', [$startDate->toDateString(), $endDate->toDateString()]))
        ->when($request->type, fn($query, $type) => $query->where('contacts.customer_group_id', $type))
        ->when(!empty($request->input('assigned_to')), function ($query) use ($request) {
            $query->join('user_contact_access AS uc', 'contacts.id', '=', 'uc.contact_id')
                    ->join('users', 'users.id', '=', 'uc.user_id')
                    ->where('users.id', $request->input('assigned_to'))
                    ->select('contacts.*');
        })
        ->when($request->search_keyword, fn($query, $keyword) => $query->where(function ($q) use ($keyword) {
            $q->where('contacts.name', 'like', "%$keyword%")
                ->orWhere('contacts.supplier_business_name', 'like', "%$keyword%")
                ->orWhere('contacts.mobile', 'like', "%$keyword%");
        }))
        ->orderBy('contacts.created_at', 'desc');

        $contacts = $contactsQuery->distinct()->get();

        $payment_types = [];
        $payment_types_query = TransactionPayment::join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', $business_id)
            ->pluck('transaction_payments.method')
            ->unique()
            ->toArray();

        foreach ($payment_types_query as $method) {
            $payment_types[$method] = ucfirst($method);
        }

        $locations = BusinessLocation::where('business_id', $business_id)
            ->pluck('name')
            ->implode(', ');

        $contacts = $contacts->map(function ($contact) use ($startDate, $endDate, $business_id, $payment_types, $customer_groups) {
            $assignedUsers = Contact::join('user_contact_access AS uc', 'contacts.id', '=', 'uc.contact_id')
                ->join('users', 'users.id', '=', 'uc.user_id')
                ->where('contacts.id', $contact->id)
                ->select('users.surname', 'users.first_name', 'users.last_name')
                ->get();

            $contact->assigned_to = $assignedUsers->map(function ($user) {
                return trim("{$user->surname} {$user->first_name} {$user->last_name}");
            })->implode(', ');

            $contact->customer_group = $customer_groups[$contact->customer_group_id] ?? '';

            $contact->total_paid = TransactionPayment::join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
                ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->where('transactions.contact_id', $contact->id)
                ->where('transactions.business_id', $business_id)
                ->when($startDate && $endDate, fn($query) => $query->whereBetween('transaction_payments.created_at', [$startDate->toDateString(), $endDate->toDateString()]))
                ->sum('transaction_payments.amount');

            $payment_methods = TransactionPayment::join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
                ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->where('transactions.contact_id', $contact->id)
                ->where('transactions.business_id', $business_id)
                ->when($startDate && $endDate, fn($query) => $query->whereBetween('transaction_payments.created_at', [$startDate->toDateString(), $endDate->toDateString()]))
                ->pluck('transaction_payments.method')
                ->unique()
                ->toArray();

            $count = count($payment_methods);
            if ($count == 1) {
                $contact->payment_method = $payment_types[$payment_methods[0]] ?? $payment_methods[0];
            } elseif ($count > 1) {
                $contact->payment_method = 'Multiple';
            } else {
                $contact->payment_method = '';
            }

            if (!empty($contact->mobile)) {
                $phoneNumbers = preg_split('/[,;\s]+/', $contact->mobile, -1, PREG_SPLIT_NO_EMPTY);
                $contact->mobile = implode(' , ', array_slice($phoneNumbers, 0, 2));
            }

            return $contact;
        });

        $business_name = $business->name;

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'contacts' => $contacts,
                'month' => $month,
                'start_day' => $start_day,
                'end_day' => $end_day,
                'locations' => $locations,
                'business_name' => $business_name,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
        }

        return view('minireportb1::MiniReportB1.StandardReport.Monthly.franchise_monthly_report')
            ->with(compact('contacts', 'business', 'business_name', 'month', 'start_day', 'end_day', 'locations', 'start_date', 'end_date', 'assigned_users'));
    }

    public function shareBenefitReport(Request $request) {
        return view('minireportb1::MiniReportB1.StandardReport.Monthly.share_benefit_report');
            // ->with(compact('month'));
    }
}