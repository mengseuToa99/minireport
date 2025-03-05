<?php

namespace Modules\MiniReportB1\Http\Controllers\StandardReport;


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
use App\Transaction;
use App\User;

class OperationalReportsController extends Controller
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


    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil, DateFilterService $dateFilterService)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->dateFilterService = $dateFilterService;
    }


    
    public function activityLog()
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_types = [
            'contact' => __('report.contact'),
            'user' => __('report.user'),
            'sell' => __('sale.sale'),
            'purchase' => __('lang_v1.purchase'),
            'sales_order' => __('lang_v1.sales_order'),
            'purchase_order' => __('lang_v1.purchase_order'),
            'sell_return' => __('lang_v1.sell_return'),
            'purchase_return' => __('lang_v1.purchase_return'),
            'sell_transfer' => __('lang_v1.stock_transfer'),
            'stock_adjustment' => __('stock_adjustment.stock_adjustment'),
            'expense' => __('lang_v1.expense'),
        ];

        if (request()->ajax()) {
            $activities = Activity::with(['subject'])
                ->leftjoin('users as u', 'u.id', '=', 'activity_log.causer_id')
                ->where('activity_log.business_id', $business_id)
                ->select(
                    'activity_log.*',
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as created_by")
                );

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $activities->whereDate('activity_log.created_at', '>=', $start)
                    ->whereDate('activity_log.created_at', '<=', $end);
            }

            if (! empty(request()->user_id)) {
                $activities->where('causer_id', request()->user_id);
            }

            $subject_type = request()->subject_type;
            if (! empty($subject_type)) {
                if ($subject_type == 'contact') {
                    $activities->where('subject_type', \App\Contact::class);
                } elseif ($subject_type == 'user') {
                    $activities->where('subject_type', \App\User::class);
                } elseif (in_array($subject_type, [
                    'sell',
                    'purchase',
                    'sales_order',
                    'purchase_order',
                    'sell_return',
                    'purchase_return',
                    'sell_transfer',
                    'expense',
                    'purchase_order',
                ])) {
                    $activities->where('subject_type', \App\Transaction::class);
                    $activities->whereHasMorph('subject', Transaction::class, function ($q) use ($subject_type) {
                        $q->where('type', $subject_type);
                    });
                }
            }

            $sell_statuses = Transaction::sell_statuses();
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $purchase_statuses = $this->transactionUtil->orderStatuses();
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $statuses = array_merge($sell_statuses, $sales_order_statuses, $purchase_statuses);

            return Datatables::of($activities)
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->addColumn('subject_type', function ($row) use ($transaction_types) {
                    $subject_type = '';
                    if ($row->subject_type == \App\Contact::class) {
                        $subject_type = __('contact.contact');
                    } elseif ($row->subject_type == \App\User::class) {
                        $subject_type = __('report.user');
                    } elseif ($row->subject_type == \App\Transaction::class && ! empty($row->subject->type)) {
                        $subject_type = isset($transaction_types[$row->subject->type]) ? $transaction_types[$row->subject->type] : '';
                    } elseif (($row->subject_type == \App\TransactionPayment::class)) {
                        $subject_type = __('lang_v1.payment');
                    }

                    return $subject_type;
                })
                ->addColumn('note', function ($row) use ($statuses, $shipping_statuses) {
                    $html = '';
                    if (! empty($row->subject->ref_no)) {
                        $html .= __('purchase.ref_no') . ': ' . $row->subject->ref_no . '<br>';
                    }
                    if (! empty($row->subject->invoice_no)) {
                        $html .= __('sale.invoice_no') . ': ' . $row->subject->invoice_no . '<br>';
                    }
                    if ($row->subject_type == \App\Transaction::class && ! empty($row->subject) && in_array($row->subject->type, ['sell', 'purchase'])) {
                        $html .= view('sale_pos.partials.activity_row', ['activity' => $row, 'statuses' => $statuses, 'shipping_statuses' => $shipping_statuses])->render();
                    } else {
                        $update_note = $row->getExtraProperty('update_note');
                        if (! empty($update_note) && ! is_array($update_note)) {
                            $html .= $update_note;
                        }
                    }

                    if ($row->description == 'contact_deleted') {
                        $html .= $row->getExtraProperty('supplier_business_name') ?? '';
                        $html .= '<br>';
                    }

                    if (! empty($row->getExtraProperty('name'))) {
                        $html .= __('user.name') . ': ' . $row->getExtraProperty('name') . '<br>';
                    }

                    if (! empty($row->getExtraProperty('id'))) {
                        $html .= 'id: ' . $row->getExtraProperty('id') . '<br>';
                    }
                    if (! empty($row->getExtraProperty('invoice_no'))) {
                        $html .= __('sale.invoice_no') . ': ' . $row->getExtraProperty('invoice_no');
                    }

                    if (! empty($row->getExtraProperty('ref_no'))) {
                        $html .= __('purchase.ref_no') . ': ' . $row->getExtraProperty('ref_no');
                    }

                    return $html;
                })
                ->filterColumn('created_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->editColumn('description', function ($row) {
                    return __('lang_v1.' . $row->description);
                })
                ->rawColumns(['note'])
                ->make(true);
        }

        $users = User::allUsersDropdown($business_id, false);

        return view('minireportb1::MiniReportB1.card-menu.report.activity_log')->with(compact('users', 'transaction_types'));
    }

    public function getRegisterReport(Request $request)
    {
        if (! auth()->user()->can('register_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {

            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $user_id = request()->input('user_id');

            $permitted_locations = auth()->user()->permitted_locations();

            $registers = $this->transactionUtil->registerReport($business_id, $permitted_locations, $start_date, $end_date, $user_id);

            return Datatables::of($registers)
                ->editColumn('total_card_payment', function ($row) {
                    return '<span data-orig-value="' . $row->total_card_payment . '" >' . $this->transactionUtil->num_f($row->total_card_payment, true) . ' (' . $row->total_card_slips . ')</span>';
                })
                ->editColumn('total_cheque_payment', function ($row) {
                    return '<span data-orig-value="' . $row->total_cheque_payment . '" >' . $this->transactionUtil->num_f($row->total_cheque_payment, true) . ' (' . $row->total_cheques . ')</span>';
                })
                ->editColumn('total_cash_payment', function ($row) {
                    return '<span data-orig-value="' . $row->total_cash_payment . '" >' . $this->transactionUtil->num_f($row->total_cash_payment, true) . '</span>';
                })
                ->editColumn('total_bank_transfer_payment', function ($row) {
                    return '<span data-orig-value="' . $row->total_bank_transfer_payment . '" >' . $this->transactionUtil->num_f($row->total_bank_transfer_payment, true) . '</span>';
                })
                ->editColumn('total_other_payment', function ($row) {
                    return '<span data-orig-value="' . $row->total_other_payment . '" >' . $this->transactionUtil->num_f($row->total_other_payment, true) . '</span>';
                })
                ->editColumn('total_advance_payment', function ($row) {
                    return '<span data-orig-value="' . $row->total_advance_payment . '" >' . $this->transactionUtil->num_f($row->total_advance_payment, true) . '</span>';
                })
                ->editColumn('total_custom_pay_1', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_1 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_1, true) . '</span>';
                })
                ->editColumn('total_custom_pay_2', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_2 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_2, true) . '</span>';
                })
                ->editColumn('total_custom_pay_3', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_3 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_3, true) . '</span>';
                })
                ->editColumn('total_custom_pay_4', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_4 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_4, true) . '</span>';
                })
                ->editColumn('total_custom_pay_5', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_5 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_5, true) . '</span>';
                })
                ->editColumn('total_custom_pay_6', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_6 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_6, true) . '</span>';
                })
                ->editColumn('total_custom_pay_7', function ($row) {
                    return '<span data-orig-value="' . $row->total_custom_pay_7 . '" >' . $this->transactionUtil->num_f($row->total_custom_pay_7, true) . '</span>';
                })
                ->editColumn('closed_at', function ($row) {
                    if ($row->status == 'close') {
                        return $this->productUtil->format_date($row->closed_at, true);
                    } else {
                        return '';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return $this->productUtil->format_date($row->created_at, true);
                })
                ->addColumn('total', function ($row) {
                    $total = $row->total_card_payment + $row->total_cheque_payment + $row->total_cash_payment + $row->total_bank_transfer_payment + $row->total_other_payment + $row->total_advance_payment + $row->total_custom_pay_1 + $row->total_custom_pay_2 + $row->total_custom_pay_3 + $row->total_custom_pay_4 + $row->total_custom_pay_5 + $row->total_custom_pay_6 + $row->total_custom_pay_7;

                    return '<span data-orig-value="' . $total . '" >' . $this->transactionUtil->num_f($total, true) . '</span>';
                })
                ->addColumn('action', '<button type="button" data-href="{{action(\'App\Http\Controllers\CashRegisterController@show\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max btn-modal" 
                    data-container=".view_register"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</button> @if($status != "close" && auth()->user()->can("close_cash_register"))<button type="button" data-href="{{action(\'App\Http\Controllers\CashRegisterController@getCloseRegister\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error tw-w-max btn-modal" 
                        data-container=".view_register"><i class="fas fa-window-close"></i> @lang("messages.close")</button> @endif')
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, ''), '<br>', COALESCE(u.email, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['action', 'user_name', 'total_card_payment', 'total_cheque_payment', 'total_cash_payment', 'total_bank_transfer_payment', 'total_other_payment', 'total_advance_payment', 'total_custom_pay_1', 'total_custom_pay_2', 'total_custom_pay_3', 'total_custom_pay_4', 'total_custom_pay_5', 'total_custom_pay_6', 'total_custom_pay_7', 'total'])
                ->make(true);
        }

        $users = User::forDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return view('minireportb1::MiniReportB1.StandardReport.OperationalReports.register_report')
            ->with(compact('users', 'payment_types'));
    }

    public function getExpenseReport(Request $request)
    {
        if (! auth()->user()->can('expense_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $filters = $request->only(['category', 'location_id']);

        $date_range = $request->input('date_range');

        if (! empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
            $filters['end_date'] = \Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($expenses as $expense) {
            $values[] = (float) $expense->total_expense;
            $labels[] = ! empty($expense->category) ? $expense->category : __('report.others');
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->title(__('report.expense_report'))
            ->dataset(__('report.total_expense'), 'column', $values);

        $categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.StandardReport.OperationalReports.expense_report')
            ->with(compact('chart', 'categories', 'business_locations', 'expenses'));
    }


    // get profit and loss
    public function getProfitLoss(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');

            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty(request()->input('location_id')) ? request()->input('location_id') : null;
            $start_date = ! empty(request()->input('start_date')) ? request()->input('start_date') : $fy['start'];
            $end_date = ! empty(request()->input('end_date')) ? request()->input('end_date') : $fy['end'];

            $user_id = request()->input('user_id') ?? null;

            $permitted_locations = auth()->user()->permitted_locations();
            $data = $this->transactionUtil->getProfitLossDetails($business_id, $location_id, $start_date, $end_date, $user_id, $permitted_locations);

            return view('report.partials.profit_loss_details', compact('data'))->render();
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('minireportb1::MiniReportB1.StandardReport.OperationalReports.profit_loss', compact('business_locations'));
    }



    public function getTaxReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $contact_id = $request->get('contact_id');

            $input_tax_details = $this->transactionUtil->getInputTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $output_tax_details = $this->transactionUtil->getOutputTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $expense_tax_details = $this->transactionUtil->getExpenseTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $module_output_taxes = $this->moduleUtil->getModuleData('getModuleOutputTax', ['start_date' => $start_date, 'end_date' => $end_date]);

            $total_module_output_tax = 0;
            foreach ($module_output_taxes as $key => $module_output_tax) {
                $total_module_output_tax += $module_output_tax;
            }

            $total_output_tax = $output_tax_details['total_tax'] + $total_module_output_tax;

            $tax_diff = $total_output_tax - $input_tax_details['total_tax'] - $expense_tax_details['total_tax'];

            return [
                'tax_diff' => $tax_diff,
            ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $taxes = TaxRate::forBusiness($business_id);

        $tax_report_tabs = $this->moduleUtil->getModuleData('getTaxReportViewTabs');

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('minireportb1::MiniReportB1.StandardReport.OperationalReports.tax_report')
            ->with(compact('business_locations', 'taxes', 'tax_report_tabs', 'contact_dropdown'));
    }

}
