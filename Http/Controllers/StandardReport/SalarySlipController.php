<?php

namespace Modules\MiniReportB1\Http\Controllers\StandardReport;

use App\Business;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MiniReportB1\Http\Services\DateFilterService;
use App\ExchangeRate;

class SalarySlipController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;
    protected $moduleUtil;
    protected $dateFilterService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        DateFilterService $dateFilterService
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->dateFilterService = $dateFilterService;
    }

    /**
     * Display salary slip index page
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!auth()->user()->can('salary_slip.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        
        // Get report date (default to current date)
        $report_date = $request->input('report_date', Carbon::now()->format('Y-m-d'));
        $carbon_date = Carbon::parse($report_date);
        $month = $carbon_date->format('F');
        $year = $carbon_date->format('Y');
        
        // Format as "March 2024"
        $formatted_date = $month . ' ' . $year;
        
        // Get exchange rate for the current month (15th day)
        $report_month = $carbon_date->format('m');
        $report_year = $carbon_date->format('Y');
        $fifteenth_date = "$report_year-$report_month-15";
        
        $exchangeRate = ExchangeRate::where('business_id', $business_id)
            ->whereDate('date_1', $fifteenth_date)
            ->first();
            
        if (!$exchangeRate) {
            // If no rate for 15th, get closest previous rate
            $exchangeRate = ExchangeRate::where('business_id', $business_id)
                ->where('date_1', '<=', $fifteenth_date)
                ->orderBy('date_1', 'desc')
                ->first();
                
            // Fallback to any rate
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->orderBy('date_1', 'desc')
                    ->first();
            }
        }
        
        // Default to 4100 if no exchange rate found
        $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;
        
        // Get employees
        $employees = User::where('users.business_id', $business_id)
            ->where('users.status', 'active')
            ->leftJoin('categories', 'users.essentials_designation_id', '=', 'categories.id')
            ->select(
                'users.id', 
                DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name"),
                'categories.name as position'
            )
            ->get()
            ->map(function($employee) {
                $employee->employee_id = $employee->id; // Use user ID as employee ID
                return $employee;
            });
        
        $selected_employee = $request->input('employee_id');
        
        // Sample salary data (can be modified to fetch from actual payroll records)
        $salary_amount = $request->input('salary_amount', 600);
        $tax_amount = $request->input('tax_amount', 0);
        $net_salary = $salary_amount - $tax_amount;
        
        // For AJAX requests, return data as JSON
        if ($request->ajax()) {
            $employee = $employees->firstWhere('id', $selected_employee);
            
            return response()->json([
                'employee' => $employee,
                'salary_amount' => $salary_amount,
                'tax_amount' => $tax_amount,
                'net_salary' => $net_salary,
                'formatted_date' => $formatted_date,
                'exchange_rate' => $exchange_rate
            ]);
        }
        
        // Get the business information
        $business = Business::findOrFail($business_id);
        
        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.salary_slip')
            ->with(compact(
                'employees',
                'selected_employee',
                'salary_amount',
                'tax_amount',
                'net_salary',
                'business',
                'formatted_date',
                'report_date',
                'exchange_rate'
            ));
    }
    
    /**
     * Generate a PDF version of the salary slip
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(Request $request)
    {
        // TODO: Implement PDF generation
        // This would generate a PDF version of the salary slip
        return response()->json(['message' => 'PDF generation not implemented yet']);
    }
} 