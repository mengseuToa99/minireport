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
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Utils\AccountingUtil;
use Modules\Essentials\Entities\PayrollGroup;
use PDF;
use App\Category;
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
use Illuminate\Http\JsonResponse;
use Modules\Essentials\Entities\EssentialsAttendance;
use Spatie\Permission\Models\Permission;
use App\ExchangeRate; // Add this line at the top with other use statements
use Illuminate\Support\Facades\Schema;

class HumanResourceController extends Controller
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

    private function calculateSalaryTaxKhr($taxable_salary_khr)
    {
        $tax_khr = 0;
        $rate_percent = 0;

        if ($taxable_salary_khr <= 1500000) {
            $tax_khr = 0;
            $rate_percent = 0;
        } elseif ($taxable_salary_khr <= 2000000) {
            $tax_khr = $taxable_salary_khr * 0.05 - 75000;
            $rate_percent = 5;
        } elseif ($taxable_salary_khr <= 8500000) {
            $tax_khr = $taxable_salary_khr * 0.1 - 175000;
            $rate_percent = 10;
        } elseif ($taxable_salary_khr <= 12500000) {
            $tax_khr = $taxable_salary_khr * 0.15 - 600000;
            $rate_percent = 15;
        } else { // Over 12,500,000
            $tax_khr = $taxable_salary_khr * 0.2 - 1225000;
            $rate_percent = 20;
        }

        // Round down to nearest 100 Riels
        // $tax_khr = floor($tax_khr / 100) * 100;

        return ['tax_khr' => $tax_khr, 'rate_percent' => $rate_percent];
    }




    // Function to calculate Cambodian Salary Tax in KHR
    public function monthlyNssfTaxReport(Request $request, DateFilterService $dateFilterService)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        // Get date range
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Format dates for display
        $formatted_month_year = Carbon::parse($start_date)->format('F Y');
        
        // Get exchange rate - use the 15th of the month for the start_date
        $report_month = date('m', strtotime($start_date));
        $report_year = date('Y', strtotime($start_date));
        $fifteenth_date = "$report_year-$report_month-15";

        // Get the exchange rate from the 15th of the month
        $exchangeRate = ExchangeRate::where('business_id', $business_id)
            ->whereDate('date_1', $fifteenth_date)
            ->first();

        if (!$exchangeRate) {
            // If no rate on 15th, get the closest rate on or before 15th within the same month
            $exchangeRate = ExchangeRate::where('business_id', $business_id)
                ->where('date_1', '<=', $fifteenth_date)
                ->whereYear('date_1', $report_year)
                ->whereMonth('date_1', $report_month)
                ->orderBy('date_1', 'desc')
                ->first();

            // If still no rate found in the current month before 15th, try finding ANY rate in that month
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->whereYear('date_1', $report_year)
                    ->whereMonth('date_1', $report_month)
                    ->orderBy('date_1', 'desc') // Get the latest in the month
                    ->first();
            }

            // Fallback: Get the absolute latest rate if still nothing found
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->orderBy('date_1', 'desc')
                    ->first();
            }
        }

        // Use the exchange rate from the database or default value
        $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;
        $exchange_rate_date_used = $exchangeRate ? $exchangeRate->date_1 : $fifteenth_date;
        
        // For debugging
        // dump($exchangeRate, $exchange_rate_date_used, $exchange_rate);
        
        // Get locations for filter
        $locations = BusinessLocation::forDropdown($business_id);
        
        // Convert locations to a regular array to avoid nesting issues
        $locationsArray = [];
        foreach ($locations as $id => $name) {
            $locationsArray[$id] = $name;
        }
        
        // For filters-only request, return early with just the filter options
        if ($request->input('get_filters_only') === true || $request->input('get_filters_only') === 'true') {
            return response()->json([
                'locations' => $locationsArray
            ]);
        }
        
        // Base SQL query with location filter
        $baseQuery = "
            SELECT
                u.id AS id,
                COALESCE(u.id_proof_number, '') AS tax_id,
                COALESCE(CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')), u.name_in_khmer) AS employee_name,
                COALESCE(cat.name, 'Others') AS position,
                CASE WHEN u.marital_status = 'married' THEN 'មាន' ELSE 'គ្មាន' END AS marital_status,
                COALESCE(u.child_count, 0) AS child_count,
                COALESCE(t.final_total, 0) AS gross_salary_usd,
                u.location_id,
                bl.name AS location_name
            FROM users u
            LEFT JOIN categories cat ON u.essentials_designation_id = cat.id
            LEFT JOIN business_locations bl ON u.location_id = bl.id
            LEFT JOIN transactions t ON t.expense_for = u.id 
                AND t.transaction_date BETWEEN ? AND ?
                AND t.payment_status IN ('paid', 'recieved', 'due')
            LEFT JOIN essentials_payroll_group_transactions pgt ON pgt.transaction_id = t.id
            LEFT JOIN essentials_payroll_groups pg ON pg.id = pgt.payroll_group_id
            WHERE u.status = 'active' AND u.business_id = ? AND t.final_total IS NOT NULL
        ";
        
        $params = [$start_date->format('Y-m-d 00:00:00'), $end_date->format('Y-m-d 23:59:59'), $business_id];
        
        // Apply location filter if provided
        $locationIdUsed = null;
        if ($request->filled('location_filter') && $request->location_filter != '') {
            // Cast to integer since database likely stores it as int
            $location_id = (int) $request->location_filter;
            $locationIdUsed = $location_id;
            
            $baseQuery .= " AND u.location_id = ?";
            $params[] = $location_id;
        }
        
        // Complete the query with GROUP BY
        $baseQuery .= " GROUP BY u.id, u.id_proof_number, u.surname, u.first_name, u.last_name, u.name_in_khmer, 
                    cat.name, u.marital_status, u.child_count, u.location_id, bl.name";
        
        // Execute the query
        $employees = DB::select($baseQuery, $params);
        
        // Check if no salary data was found
        $hasSalaryData = collect($employees)->sum('gross_salary_usd') > 0;
        
        // If no data found with salary, try searching for active employees directly
        if (!$hasSalaryData && $locationIdUsed) {
            $activeEmployeesAtLocation = User::where('users.business_id', $business_id)
                ->where('users.location_id', $locationIdUsed)
                ->where('users.status', 'active')
                ->get();
        }
        
        // Process data for display
        $formatted_data = [];
        $total_spouse = 0;
        $total_children = 0;
        $total_salary_usd = 0;
        $total_gross_salary_usd = 0;
        $total_gross_salary_khr = 0;
        $total_taxable_salary_usd = 0;
        $total_taxable_salary = 0;
        $total_salary_tax = 0;
        $total_pension_employee = 0;
        $total_net_salary = 0;
        $total_nssf_contribution = 0;
        $total_health_contribution = 0;
        $total_pension_employer = 0;
        $total_total_contributions = 0;
        
        foreach ($employees as $index => $employee) {
            // Basic calculations
            $salary_usd = $employee->gross_salary_usd;
            $gross_salary_khr = $salary_usd * $exchange_rate;
            
            // Calculate dependents
            $spouse_count = $employee->marital_status == 'មាន' ? 1 : 0;
            $child_count = $employee->child_count;
            
            // Calculate tax deductions
            $dependent_deduction_khr = ($spouse_count + $child_count) * 150000;
            $taxable_salary_khr = max(0, $gross_salary_khr - $dependent_deduction_khr);
            
            // Calculate pension contribution (2% capped between 400,000 and 1,200,000)
            $pension_base = max(400000, min(1200000, $gross_salary_khr));
            $pension_employee_amount = round($pension_base * 0.02, 0);
            
            // Calculate Net Salary
            $net_salary = $gross_salary_khr - $pension_employee_amount;
            
            // Calculate salary tax
            $tax_calculation = $this->calculateSalaryTaxKhr($taxable_salary_khr);
            $tax_amount_khr = round($tax_calculation['tax_khr'], 0);
            $tax_rate_percent = $tax_calculation['rate_percent'];
            
            // Convert tax to USD (for display purposes)
            $tax_amount_usd = $exchange_rate ? round($tax_amount_khr / $exchange_rate, 2) : 0;
            
            // Calculate NSSF and health contributions
            $nssf_base = max(400000, min(1200000, $gross_salary_khr));
            $nssf_contribution = round($nssf_base * 0.008, 0); // 0.8%
            $health_contribution = round($nssf_base * 0.026, 0); // 2.6%
            $pension_employer_amount = round($nssf_base * 0.02, 0); // 2% employer contribution
            
            // Calculate total contributions
            $total_contributions = $nssf_contribution + $health_contribution + $pension_employee_amount + $pension_employer_amount;
            
            // Calculate gross salary in USD (the amount after tax)
            // For a $1,000 salary, it should be $958.44 when tax is $41.56
            $gross_salary_usd = round($salary_usd - $tax_amount_usd, 2);
            
            // Add to totals
            $total_spouse += $spouse_count;
            $total_children += $child_count;
            $total_salary_usd += $salary_usd;
            $total_gross_salary_usd += $gross_salary_usd;
            $total_gross_salary_khr += $gross_salary_khr;
            $total_taxable_salary_usd += $tax_amount_usd;
            $total_taxable_salary += $taxable_salary_khr;
            $total_salary_tax += $tax_amount_khr;
            $total_pension_employee += $pension_employee_amount;
            $total_net_salary += $net_salary;
            $total_nssf_contribution += $nssf_contribution;
            $total_health_contribution += $health_contribution;
            $total_pension_employer += $pension_employer_amount;
            $total_total_contributions += $total_contributions;
            
            // Format for display 
            $formatted_data[] = [
                'id' => $index + 1,
                'tax_id' => $employee->tax_id,
                'employee_name' => $employee->employee_name,
                'position' => $employee->position,
                'salary_usd' => number_format($salary_usd, 2) . ' $',
                'gross_salary_khr' => number_format($gross_salary_usd, 2) . ' $',
                'taxable_salary_usd' => number_format($tax_amount_usd, 2) . ' $',
                'gross_salary_khr_display' => number_format($gross_salary_khr, 0) . ' ៛',
                'pension_employee' => number_format($pension_employee_amount, 0) . ' ៛',
                'net_salary' => number_format($net_salary, 0) . ' ៛',
                'spouse' => $spouse_count,
                'children' => $child_count,
                'taxable_salary' => number_format($taxable_salary_khr, 0) . ' ៛',
                'tax_rate' => $tax_rate_percent . '%',
                'salary_tax' => number_format($tax_amount_khr, 0) . ' ៛',
                'salary_khr' => number_format($gross_salary_khr, 0) . ' ៛',
                'nssf_contribution' => number_format($nssf_contribution, 0) . ' ៛',
                'health_contribution' => number_format($health_contribution, 0) . ' ៛',
                'pension_employee_display' => number_format($pension_employee_amount, 0) . ' ៛',
                'pension_employer' => number_format($pension_employer_amount, 0) . ' ៛',
                'total_contributions' => number_format($total_contributions, 0) . ' ៛'
            ];
        }
        
        // Get business details
        $business = Business::find($business_id);
        
        if ($request->ajax()) {
            return response()->json([
                'data' => $formatted_data,
                'total' => count($formatted_data),
                'totals' => [
                    'salary_usd' => number_format($total_salary_usd, 2) . ' $',
                    'gross_salary_khr' => number_format($total_gross_salary_usd, 2) . ' $',
                    'taxable_salary_usd' => number_format($total_taxable_salary_usd, 2) . ' $',
                    'spouse' => $total_spouse,
                    'children' => $total_children,
                    'taxable_salary' => number_format($total_taxable_salary, 0) . ' ៛',
                    'salary_tax' => number_format($total_salary_tax, 0) . ' ៛',
                    'pension_employee' => number_format($total_pension_employee, 0) . ' ៛',
                    'net_salary' => number_format($total_net_salary, 0) . ' ៛',
                    'salary_khr' => number_format($total_gross_salary_khr, 0) . ' ៛',
                    'nssf_contribution' => number_format($total_nssf_contribution, 0) . ' ៛',
                    'health_contribution' => number_format($total_health_contribution, 0) . ' ៛',
                    'pension_employer' => number_format($total_pension_employer, 0) . ' ៛',
                    'total_contributions' => number_format($total_total_contributions, 0) . ' ៛'
                ],
                'business_name' => $business->name ?? '',
                'report_month' => $formatted_month_year,
                'has_salary_data' => $hasSalaryData,
                'exchange_rate' => $exchange_rate
            ]);
        }
        
        // Get all users for filter dropdown if needed
        $users = User::where('users.business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->orderBy('full_name')
            ->get();
        
        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.monthly_nssf_tax_report')
            ->with(compact('business', 'formatted_month_year', 'hasSalaryData', 'start_date', 'end_date', 'users', 'locations'));
    }

    public function viewPayrollGroups()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        // Get location_id from request if present
        $location_id = request()->input('location_id');

        $query = PayrollGroup::where('business_id', $business_id)
            ->with(['payrollGroupTransactions', 'payrollGroupTransactions.transaction_for', 'businessLocation', 'business']);

        // Add location filter if location_id is provided
        if (!empty($location_id)) {
            $query->whereHas('businessLocation', function ($q) use ($location_id) {
                $q->where('id', $location_id);
            });
        }

        $payroll_groups = $query->get();

        $result = [];

        foreach ($payroll_groups as $payroll_group) {
            $group_data = [
                'payroll_group_id' => $payroll_group->id,
                'payroll_group_name' => $payroll_group->name,
                'payrolls' => [],
                'month_name' => null,
                'year' => null,
                'business_location' => $payroll_group->businessLocation->name ?? null,
                'business' => $payroll_group->business->name ?? null
            ];

            foreach ($payroll_group->payrollGroupTransactions as $transaction) {
                // Set month and year from first transaction if not set
                if (empty($group_data['month_name']) && empty($group_data['year'])) {
                    $transaction_date = \Carbon::parse($transaction->transaction_date);
                    $group_data['month_name'] = $transaction_date->format('F');
                    $group_data['year'] = $transaction_date->format('Y');
                }

                // Prepare payroll data
                $payroll_data = [
                    'transaction_id' => $transaction->id,
                    'final_total' => $transaction->final_total,
                    'payment_status' => $transaction->payment_status,
                    'employee' => $transaction->transaction_for->user_full_name ?? null,
                    'bank_details' => json_decode($transaction->transaction_for->bank_details ?? '{}', true)
                ];

                $group_data['payrolls'][$transaction->expense_for] = $payroll_data;
            }

            $result[] = $group_data;
        }

        if (request()->ajax()) {
            return response()->json($result);
        }

        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.shiftschedule_report')
            ->with(compact('result'));
    }


    public function ShiftSchedule(Request $request)
    {
        // Retrieve business ID from session securely
        $business_id = $request->session()->get('user.business_id');
        if (!$business_id) {
            return response()->json(['error' => 'Business ID not found in session.'], 400);
        }

        // Base query to fetch user shift data
        $usersQuery = User::where('users.business_id', $business_id)
            ->join('essentials_user_shifts as eus', 'eus.user_id', '=', 'users.id')
            ->join('essentials_shifts as es', 'es.id', '=', 'eus.essentials_shift_id')
            ->select([
                'users.id',
                DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as user"),
                'es.start_time',
                'es.end_time'
            ]);

        // Apply user filter if provided
        if ($request->filled('user_id_filter')) {
            $usersQuery->where('users.id', $request->user_id_filter);
        }

        // Apply shift time filter if provided
        if ($request->filled('shift_filter') && $request->shift_filter !== 'all') {
            $shiftMap = [
                '08:00-17:00' => ['08:00:00', '17:00:00'],
                '08:30-17:30' => ['08:30:00', '17:30:00'],
            ];

            if (array_key_exists($request->shift_filter, $shiftMap)) {
                [$startTime, $endTime] = $shiftMap[$request->shift_filter];
                $usersQuery->where('es.start_time', $startTime)
                    ->where('es.end_time', $endTime);
            }
        }

        // Fetch shift data
        $shiftData = $usersQuery->get();

        // Handle AJAX requests
        if ($request->ajax()) {
            $formattedData = $shiftData->map(function ($row) {
                return [
                    'user' => $row->user,
                    'shift' => date('H:i', strtotime($row->start_time)) . '-' . date('H:i', strtotime($row->end_time))
                ];
            });

            return response()->json([
                'shift_data' => $formattedData,
                'total' => $formattedData->count()
            ]);
        }

        // Fetch all users for the dropdown (if not an AJAX request)
        $users = User::where('business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->orderBy('full_name')
            ->get();

        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.shiftschedule_report')
            ->with(compact('shiftData', 'users'));
    }

    public function monthlyAttendance(Request $request, DateFilterService $dateFilterService)
    {
        $business_id = $request->session()->get('user.business_id');

        $users = User::where('business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->orderBy('full_name')
            ->get();

        $allEmployees = User::forDropdown($business_id, false);
        $employeeCollection = collect($allEmployees);

        $employees = $employeeCollection->filter(function ($employeeName, $employeeId) {
            $user = User::find($employeeId);
            return $user && $user->status == 'active' && $user->allow_login == 1;
        })->toArray();

        $dateRange = $dateFilterService->calculateDateRange($request);
        $startDate = $dateRange['start_date']->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $daysInMonth = $startDate->daysInMonth;

        $usernameFilter = $request->input('username_filter');
        $filteredEmployees = !empty($usernameFilter)
            ? collect($employees)->filter(fn($name, $id) => $id == $usernameFilter)
            : collect($employees);

        $attendanceQuery = EssentialsAttendance::where('business_id', $business_id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('clock_in_time', [$startDate, $endDate])
                    ->orWhereBetween('clock_out_time', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('clock_in_time', '<=', $startDate)
                            ->where('clock_out_time', '>=', $endDate);
                    });
            });

        if (!empty($usernameFilter)) {
            $attendanceQuery->where('user_id', $usernameFilter);
        }

        $attendanceRecords = $attendanceQuery->get();

        // Calculate total hours per user per day
        $attendanceSummary = [];

        foreach ($attendanceRecords as $record) {
            $userId = $record->user_id;

            // Parse clock_in_time and clock_out_time as Carbon instances
            $clockIn = \Carbon\Carbon::parse($record->clock_in_time);
            $clockOut = \Carbon\Carbon::parse($record->clock_out_time);

            // Extract the day of the month from the clock_in_time
            $day = (int) $clockIn->format('j');

            // Calculate total minutes worked
            $totalMinutes = $clockOut->diffInMinutes($clockIn);
            $totalHours = $totalMinutes / 60; // Convert to decimal hours

            // Initialize the day's total hours if not already set
            if (!isset($attendanceSummary[$userId][$day])) {
                $attendanceSummary[$userId][$day] = 0;
            }

            // Add the hours worked for the day
            $attendanceSummary[$userId][$day] += $totalHours;
        }

        // Build attendance data with icons
        $attendanceData = [];
        foreach ($filteredEmployees as $employeeId => $employeeName) {
            $employeeAttendance = ['id' => $employeeId, 'username' => $employeeName];

            // Initialize all days, marking Sundays as holidays
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDay = $startDate->copy()->addDays($day - 1); // Get the actual date for this day
                if ($currentDay->isSunday()) {
                    $employeeAttendance[$day] = '🌴'; // Mark Sundays as holidays
                } else {
                    $employeeAttendance[$day] = '❌'; // Default to absent for non-Sundays
                }
            }

            // Apply attendance summary (overrides holiday marker if employee worked)
            if (isset($attendanceSummary[$employeeId])) {
                foreach ($attendanceSummary[$employeeId] as $day => $hours) {
                    if ($day <= $daysInMonth) { // Ensure no overflow
                        $employeeAttendance[$day] = $hours >= 8 ? '✅' : '⏳';
                    }
                }
            }

            $attendanceData[] = $employeeAttendance;
        }

        $formattedData = collect($attendanceData)->map(function ($employee) use ($daysInMonth) {
            $formatted = ['id' => $employee['id'], 'username' => $employee['username']];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $formatted[$day] = $employee[$day];
            }
            return $formatted;
        });

        if ($request->ajax()) {
            return response()->json([
                'attendance_data' => $formattedData,
                'total' => $formattedData->count(),
                'daysInMonth' => $daysInMonth
            ]);
        } else {
            return view(
                'minireportb1::MiniReportB1.StandardReport.HumanResource.monthly_attendance_report',
                compact('users', 'attendanceData', 'daysInMonth')
            );
        }
    }

    public function lateCheckIn(Request $request, DateFilterService $dateFilterService)
    {
        $business_id = $request->session()->get('user.business_id');
        // Authorization checks remain the same

        // Get date range
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query with shift assignment
        $attendance = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
            ->leftJoin('essentials_user_shifts as eus', function ($join) use ($start_date, $end_date) {
                $join->on('eus.user_id', '=', 'u.id')
                    ->where('eus.start_date', '<=', $end_date)
                    ->where('eus.end_date', '>=', $start_date);
            })
            ->leftJoin('essentials_shifts as es', 'es.id', '=', 'eus.essentials_shift_id')
            ->select([
                'essentials_attendances.id',
                'essentials_attendances.user_id',
                'essentials_attendances.clock_in_location',
                'clock_in_time',
                DB::raw('DATE(clock_in_time) as date'),
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                'es.name as shift_name', // Include shift name
                'es.start_time',
                'es.end_time',
                DB::raw("CASE 
                    WHEN TIME(clock_in_time) > COALESCE(es.start_time, '08:00:00') 
                    THEN TIMESTAMPDIFF(
                        MINUTE, 
                        CONCAT(DATE(clock_in_time), ' ', COALESCE(es.start_time, '08:00:00')), 
                        clock_in_time
                    )
                    ELSE 0 
                 END as late_minutes")
            ])
            ->whereBetween('clock_in_time', [$start_date, $end_date])
            ->whereRaw("TIME(clock_in_time) > COALESCE(es.start_time, '08:00:00')");

        // Apply username filter
        if ($request->filled('username_filter')) {
            $attendance->where('essentials_attendances.user_id', $request->username_filter);
        }

        // Fetch data
        $attendance_data = $attendance->get();

        if ($request->ajax()) {
            $formatted_data = $attendance_data->map(function ($row) {
                $late_time = $row->late_minutes < 60
                    ? "{$row->late_minutes} minutes"
                    : floor($row->late_minutes / 60) . "h " . ($row->late_minutes % 60) . "m";

                // Use shift name if available, otherwise default to shift time
                $shift = $row->shift_name
                    ? $row->shift_name . ' (' . date('H:i', strtotime($row->start_time)) . ' - ' . date('H:i', strtotime($row->end_time)) . ')'
                    : 'Default (08:00 - 17:00)';

                return [
                    'date' => $row->date,
                    'user' => $row->user,
                    'shift' => $shift, // Include shift name and time
                    'clock_in_time' => date('H:i:s', strtotime($row->clock_in_time)),
                    'late_time' => $late_time
                ];
            });

            return response()->json([
                'attendance_data' => $formatted_data,
                'total' => $formatted_data->count()
            ]);
        }

        // Get users for dropdown
        $users = User::where('business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->orderBy('full_name')
            ->get();

        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.late_check_in_report')
            ->with(compact('attendance_data', 'start_date', 'end_date', 'users'));
    }

    public function earlyCheckOut(Request $request, DateFilterService $dateFilterService)
    {
        $business_id = $request->session()->get('user.business_id');
        // Authorization checks should be added here according to your application's requirements

        // Get date range
        $dateRange = $dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Base query with shift assignment
        $attendance = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
            ->leftJoin('essentials_user_shifts as eus', function ($join) use ($start_date, $end_date) {
                $join->on('eus.user_id', '=', 'u.id')
                    ->where('eus.start_date', '<=', $end_date)
                    ->where('eus.end_date', '>=', $start_date);
            })
            ->leftJoin('essentials_shifts as es', 'es.id', '=', 'eus.essentials_shift_id')
            ->select([
                'essentials_attendances.id',
                'essentials_attendances.user_id',
                'essentials_attendances.clock_out_location',
                'clock_out_time',
                DB::raw('DATE(clock_out_time) as date'),
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                'es.name as shift_name',
                'es.start_time',
                'es.end_time',
                DB::raw("CASE 
                    WHEN TIME(clock_out_time) < COALESCE(es.end_time, '17:00:00') 
                    THEN TIMESTAMPDIFF(
                        MINUTE, 
                        clock_out_time,
                        CONCAT(DATE(clock_out_time), ' ', COALESCE(es.end_time, '17:00:00'))
                    )
                    ELSE 0 
                 END as early_minutes")
            ])
            ->whereBetween('clock_out_time', [$start_date, $end_date])
            ->whereNotNull('clock_out_time') // Ensure there's a checkout time
            ->whereRaw("TIME(clock_out_time) < COALESCE(es.end_time, '17:00:00')");

        // Apply username filter
        if ($request->filled('username_filter')) {
            $attendance->where('essentials_attendances.user_id', $request->username_filter);
        }

        // Fetch data
        $attendance_data = $attendance->get();

        if ($request->ajax()) {
            $formatted_data = $attendance_data->map(function ($row) {
                $early_time = $row->early_minutes < 60
                    ? "{$row->early_minutes} minutes"
                    : floor($row->early_minutes / 60) . "h " . ($row->early_minutes % 60) . "m";

                // Format shift information
                $shift = $row->shift_name
                    ? $row->shift_name . ' (' . date('H:i', strtotime($row->start_time)) . ' - ' . date('H:i', strtotime($row->end_time)) . ')'
                    : 'Default (08:00 - 17:00)';

                return [
                    'date' => $row->date,
                    'user' => $row->user,
                    'shift' => $shift,
                    'clock_out_time' => date('H:i:s', strtotime($row->clock_out_time)),
                    'early_time' => $early_time
                ];
            });

            return response()->json([
                'attendance_data' => $formatted_data,
                'total' => $formatted_data->count()
            ]);
        }

        // Get users for dropdown (same as late check-in)
        $users = User::where('business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->orderBy('full_name')
            ->get();

        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.early_check_out_report')
            ->with(compact('attendance_data', 'start_date', 'end_date', 'users'));
    }

    /**
     * Monthly Tax Report
     * Monthly report of salary tax and benefits tax
     */
    public function monthlyTaxReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        // Get date range
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Format dates for display
        $formatted_month_year = Carbon::parse($start_date)->format('F Y');
        
        // Get exchange rate - use the 15th of the month for the start_date
        $report_month = date('m', strtotime($start_date));
        $report_year = date('Y', strtotime($start_date));
        $fifteenth_date = "$report_year-$report_month-15";

        // Get the exchange rate from the 15th of the month
        $exchangeRate = ExchangeRate::where('business_id', $business_id)
            ->whereDate('date_1', $fifteenth_date)
            ->first();

        if (!$exchangeRate) {
            // If no rate on 15th, get the closest rate on or before 15th within the same month
            $exchangeRate = ExchangeRate::where('business_id', $business_id)
                ->where('date_1', '<=', $fifteenth_date)
                ->whereYear('date_1', $report_year)
                ->whereMonth('date_1', $report_month)
                ->orderBy('date_1', 'desc')
                ->first();

            // If still no rate found in the current month before 15th, try finding ANY rate in that month
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->whereYear('date_1', $report_year)
                    ->whereMonth('date_1', $report_month)
                    ->orderBy('date_1', 'desc') // Get the latest in the month
                    ->first();
            }

            // Fallback: Get the absolute latest rate if still nothing found
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->orderBy('date_1', 'desc')
                    ->first();
            }
        }

        $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;
        $exchange_rate_date_used = $exchangeRate ? $exchangeRate->date_1 : $fifteenth_date;
        
        // For debugging
        // dump($exchangeRate, $exchange_rate_date_used, $exchange_rate);
        
        // Get locations for filter
        $locations = BusinessLocation::forDropdown($business_id);
        
        // Convert locations to a regular array to avoid nesting issues
        $locationsArray = [];
        foreach ($locations as $id => $name) {
            $locationsArray[$id] = $name;
        }
        
        // For filters-only request, return early with just the filter options
        if ($request->input('get_filters_only') === true || $request->input('get_filters_only') === 'true') {
            return response()->json([
                'locations' => $locationsArray
            ]);
        }
        
        // Base SQL query with location filter
        $baseQuery = "
            SELECT
                u.id AS id,
                '' AS tax_id,
                CONCAT( COALESCE(u.id_proof_number, '')) AS id_proof,
              COALESCE(
    TRIM(
        CONCAT_WS(' ',
            CASE WHEN u.surname REGEXP '^[0-9]' THEN '' ELSE u.surname END,
            CASE WHEN u.first_name REGEXP '^[0-9]' THEN '' ELSE u.first_name END,
            CASE WHEN u.last_name REGEXP '^[0-9]' THEN '' ELSE u.last_name END
                        )
                    ),
                    u.name_in_khmer
                ) AS employee_name,
                COALESCE( 'ខ្មែរ') AS nationality,
                COALESCE( 'និវាសនជន') AS employee_type,
               COALESCE(
                    REGEXP_REPLACE(cat.name, '^[0-9]+\.', ''),
                    'Others'
                    ) AS position,
                CASE WHEN u.marital_status = 'married' THEN 'មាន' ELSE 'គ្មាន' END AS marital_status,
                COALESCE(u.child_count, 0) AS child_count,
                COALESCE(t.final_total, 0) AS gross_salary_usd,
                u.location_id,
                bl.name AS location_name
            FROM users u
            LEFT JOIN categories cat ON u.essentials_designation_id = cat.id
            LEFT JOIN business_locations bl ON u.location_id = bl.id
            LEFT JOIN transactions t ON t.expense_for = u.id 
                AND t.transaction_date BETWEEN ? AND ?
                AND t.payment_status IN ('paid', 'recieved', 'due')
            LEFT JOIN essentials_payroll_group_transactions pgt ON pgt.transaction_id = t.id
            LEFT JOIN essentials_payroll_groups pg ON pg.id = pgt.payroll_group_id
            WHERE u.status = 'active' AND u.business_id = ? AND t.final_total IS NOT NULL
        ";
        
        $params = [$start_date->format('Y-m-d 00:00:00'), $end_date->format('Y-m-d 23:59:59'), $business_id];
        
        // Apply location filter if provided
        $locationIdUsed = null;
        if ($request->filled('location_filter') && $request->location_filter != '') {
            // Cast to integer since database likely stores it as int
            $location_id = (int) $request->location_filter;
            $locationIdUsed = $location_id;
            
            $baseQuery .= " AND u.location_id = ?";
            $params[] = $location_id;
   
        }
        
        // Complete the query with GROUP BY
        $baseQuery .= " GROUP BY u.id, u.id_proof_number, u.surname, u.first_name, u.last_name, u.name_in_khmer, 
                    u.custom_field_1, u.custom_field_2, cat.name, u.marital_status, u.child_count, u.location_id, bl.name";

        
        // Execute the query
        $employees = DB::select($baseQuery, $params);
        

        
        // Check if no salary data was found
        $hasSalaryData = collect($employees)->sum('gross_salary_usd') > 0;
        
        // If no data found with salary, try searching for active employees directly
        if (!$hasSalaryData && $locationIdUsed) {
            $activeEmployeesAtLocation = User::where('users.business_id', $business_id)
                ->where('users.location_id', $locationIdUsed)
                ->where('users.status', 'active')
                ->get();
        }
        
        // Get benefits (allowances) for each employee from the essentials_allowances_and_deductions table
        $benefitsQuery = "
            SELECT 
                u.id AS employee_id,
                SUM(CASE WHEN ead.type = 'allowance' THEN ead.amount ELSE 0 END) AS benefits_amount
            FROM users u
            JOIN business b ON u.business_id = b.id
            JOIN essentials_user_allowance_and_deductions eua ON eua.user_id = u.id
            JOIN essentials_allowances_and_deductions ead ON ead.id = eua.allowance_deduction_id
            WHERE b.id = ?
                AND ead.applicable_date BETWEEN ? AND ?
                AND ead.type = 'allowance'
            GROUP BY u.id
        ";
        
        $benefitsParams = [$business_id, $start_date->format('Y-m-d 00:00:00'), $end_date->format('Y-m-d 23:59:59')];
        
        // Apply location filter if provided
        if ($locationIdUsed) {
            $benefitsQuery .= " AND u.location_id = ?";
            $benefitsParams[] = $locationIdUsed;
        }
        
        // Execute the benefits query
        $benefits = DB::select($benefitsQuery, $benefitsParams);
        
        // Convert to a lookup array for easier access
        $benefitsLookup = [];
        foreach ($benefits as $benefit) {
            $benefitsLookup[$benefit->employee_id] = $benefit->benefits_amount;
        }
        
        // Process data for display
        $formatted_data = [];
        $total_spouse = 0;
        $total_children = 0;
        $total_salary = 0;
        $total_benefits = 0;
        $total_salary_tax = 0;
        $total_benefits_tax = 0;
        $total_non_taxable_salary = 0;
        
        foreach ($employees as $index => $employee) {
            // Get basic salary
            $gross_salary_khr = $employee->gross_salary_usd * $exchange_rate;
            
            // Get benefits amount in USD (if any)
            $benefits_usd = isset($benefitsLookup[$employee->id]) ? $benefitsLookup[$employee->id] : 0;
            $benefits_khr = $benefits_usd * $exchange_rate;
            
            // Calculate dependents
            $spouse_count = $employee->marital_status == 'មាន' ? 1 : 0;
            $child_count = $employee->child_count;
            
            // Calculate tax deductions
            $dependent_deduction_khr = ($spouse_count + $child_count) * 150000;
            
            // Calculate taxable salary (after dependent deductions)
            $taxable_salary_khr = max(0, $gross_salary_khr - $dependent_deduction_khr);
            
            // Calculate tax on salary using the tax calculation method
            $salary_tax_calculation = $this->calculateSalaryTaxKhr($taxable_salary_khr);
            $salary_tax_khr = $salary_tax_calculation['tax_khr'];
            $tax_rate_percent = $salary_tax_calculation['rate_percent'];
            
            // Calculate tax on benefits at the same rate as salary tax
            $benefits_tax_khr = $benefits_khr * ($tax_rate_percent / 100);
            
            // Add to totals
            $total_spouse += $spouse_count;
            $total_children += $child_count;
            $total_salary += $gross_salary_khr;
            $total_benefits += $benefits_khr;
            $total_salary_tax += $salary_tax_khr;
            $total_benefits_tax += $benefits_tax_khr;
            
            // Non-taxable salary is the dependent deduction amount
            // $non_taxable_salary_khr = $dependent_deduction_khr;
            $non_taxable_salary_khr = 0;
            $total_non_taxable_salary += $non_taxable_salary_khr;
            
            $formatted_data[] = [
                'id' => $index + 1,
                'tax_id' => $employee->tax_id ?? '', 
                'id_proof' => $employee->id_proof,
                'employee_name' => $employee->employee_name,
                'nationality' => $employee->nationality,
                'employee_type' => $employee->employee_type,
                'position' => $employee->position,
                'spouse' => $employee->marital_status,
                'children' => $child_count,
                'gross_salary' => number_format($gross_salary_khr, 0),
                'benefits' => number_format($benefits_khr, 0),
                'salary_tax' => number_format($salary_tax_khr, 0),
                'benefits_tax' => number_format($benefits_tax_khr, 0),
                'non_taxable_salary' => number_format($non_taxable_salary_khr, 0),
                'tax_status' => 'មិនទាន់ប្រកាសពន្ធ',
                'location_name' => $employee->location_name ?? 'N/A',
                'location_id' => $employee->location_id
            ];
        }
        
        // Get business details
        $business = Business::find($business_id);
        
        if ($request->ajax()) {
            return response()->json([
                'data' => $formatted_data,
                'total' => count($formatted_data),
                'totals' => [
                    'spouse' => $total_spouse,
                    'children' => $total_children,
                    'gross_salary' => number_format($total_salary, 0) . ' ៛',
                    'benefits' => number_format($total_benefits, 0) . ' ៛',
                    'salary_tax' => number_format($total_salary_tax, 0) . ' ៛',
                    'benefits_tax' => number_format($total_benefits_tax, 0) . ' ៛',
                    'non_taxable_salary' => number_format($total_non_taxable_salary, 0) . ' ៛'
                ],
                'business_name' => $business->name ?? '',
                'report_month' => $formatted_month_year,
                'has_salary_data' => $hasSalaryData,
                'debug_info' => [
                    'location_id' => $locationIdUsed,
                    'date_range' => [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')],
                    'exchange_rate' => $exchange_rate,
                    'exchange_rate_date' => $exchange_rate_date_used,
                    'employee_count' => count($employees),
                    'benefits_count' => count($benefits),
                    'raw_sql' => $baseQuery,
                    'sql_params' => $params,
                    'has_salary_data' => $hasSalaryData
                ]
            ]);
        }
        
        // Get all users for filter dropdown if needed
        $users = User::where('users.business_id', $business_id)
            ->select('id', DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->orderBy('full_name')
            ->get();
        
        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.monthly_tax_report')
            ->with(compact('business', 'formatted_month_year', 'hasSalaryData', 'start_date', 'end_date', 'users', 'locations'));
    }

    /**
     * Individual Salary Slip Report
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function getSalarySlip(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        // Get report date
        $report_date = $request->input('report_date', Carbon::now()->format('Y-m-d'));
        $carbon_date = Carbon::parse($report_date);
        $month = $carbon_date->format('F');
        $year = $carbon_date->format('Y');
        
        // Format as "March 2024"
        $formatted_date = $month . ' ' . $year;
        
        // Get employees
        $employees = User::where('users.business_id', $business_id)
            ->where('users.status', 'active')
            ->leftJoin('categories', 'users.essentials_designation_id', '=', 'categories.id')
            ->select(
                'users.id', 
                DB::raw("COALESCE(TRIM(CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, ''))), users.name_in_khmer) as full_name"),
                'categories.name as position',
                'users.marital_status',
                'users.child_count'
            )
            ->get();
        
        $selected_employee = $request->input('employee_id');
        
        // Get exchange rate for this month
        $report_month = $carbon_date->format('m');
        $report_year = $carbon_date->format('Y');
        $fifteenth_date = "$report_year-$report_month-15";

        // Get the exchange rate from the 15th of the month
        $exchangeRate = ExchangeRate::where('business_id', $business_id)
            ->whereDate('date_1', $fifteenth_date)
            ->first();

        // If no rate on 15th, get the closest rate
        if (!$exchangeRate) {
            $exchangeRate = ExchangeRate::where('business_id', $business_id)
                ->where('date_1', '<=', $fifteenth_date)
                ->whereYear('date_1', $report_year)
                ->whereMonth('date_1', $report_month)
                ->orderBy('date_1', 'desc')
                ->first();

            // If still nothing, get ANY rate in that month
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->whereYear('date_1', $report_year)
                    ->whereMonth('date_1', $report_month)
                    ->orderBy('date_1', 'desc')
                    ->first();
            }

            // Fallback: Get the absolute latest rate if still nothing found
            if (!$exchangeRate) {
                $exchangeRate = ExchangeRate::where('business_id', $business_id)
                    ->orderBy('date_1', 'desc')
                    ->first();
            }
        }

        $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;
        
        // Get salary data if employee is selected
        $salary_amount = 0;
        $tax_amount = 0;
        $net_salary = 0;
        
        if ($selected_employee) {
            // Get the employee's transaction data for the selected month
            $start_date = Carbon::parse($report_date)->startOfMonth();
            $end_date = Carbon::parse($report_date)->endOfMonth();
            
            // First try to find a direct payroll transaction
            $transaction = DB::table('transactions')
                ->where('expense_for', $selected_employee)
                ->where('business_id', $business_id)
                ->where('type', 'payroll') // Ensure we're looking at payroll transactions
                ->whereBetween('transaction_date', [$start_date->format('Y-m-d 00:00:00'), $end_date->format('Y-m-d 23:59:59')])
                ->whereIn('payment_status', ['paid', 'due', 'recieved'])
                ->select('final_total', 'transaction_date', 'id')
                ->orderBy('transaction_date', 'desc')
                ->first();
            
            // Debug info
            $debug_info = [
                'method' => 'primary',
                'transaction_found' => $transaction ? true : false,
                'start_date' => $start_date->format('Y-m-d'),
                'end_date' => $end_date->format('Y-m-d'),
            ];
            
            if ($transaction) {
                $salary_amount = $transaction->final_total;
                $debug_info['transaction_id'] = $transaction->id;
                $debug_info['transaction_date'] = $transaction->transaction_date;
            } else {
                // Fallback 1: Try to find any expense transaction for this employee in this month
                $fallback_transaction = DB::table('transactions')
                    ->where('expense_for', $selected_employee)
                    ->where('business_id', $business_id)
                    ->whereBetween('transaction_date', [$start_date->format('Y-m-d 00:00:00'), $end_date->format('Y-m-d 23:59:59')])
                    ->whereIn('payment_status', ['paid', 'due', 'recieved'])
                    ->select('final_total', 'transaction_date', 'id', 'type')
                    ->orderBy('transaction_date', 'desc')
                    ->first();
                
                if ($fallback_transaction) {
                    $salary_amount = $fallback_transaction->final_total;
                    $debug_info['method'] = 'fallback_transaction';
                    $debug_info['transaction_id'] = $fallback_transaction->id;
                    $debug_info['transaction_date'] = $fallback_transaction->transaction_date;
                    $debug_info['transaction_type'] = $fallback_transaction->type;
                } else {
                    // Fallback 2: Try to find the most recent payroll for this employee from previous months
                    $previous_payroll = DB::table('transactions')
                        ->where('expense_for', $selected_employee)
                        ->where('business_id', $business_id)
                        ->where('type', 'payroll')
                        ->whereIn('payment_status', ['paid', 'due', 'recieved'])
                        ->select('final_total', 'transaction_date', 'id')
                        ->orderBy('transaction_date', 'desc')
                        ->first();
                    
                    if ($previous_payroll) {
                        $salary_amount = $previous_payroll->final_total;
                        $debug_info['method'] = 'previous_payroll';
                        $debug_info['transaction_id'] = $previous_payroll->id;
                        $debug_info['transaction_date'] = $previous_payroll->transaction_date;
                    } else {
                        // Try to find salary from allowances
                        $allowances = DB::table('essentials_allowances_and_deductions')
                            ->join('essentials_user_allowance_and_deductions', 'essentials_allowances_and_deductions.id', '=', 'essentials_user_allowance_and_deductions.allowance_deduction_id')
                            ->where('essentials_user_allowance_and_deductions.user_id', $selected_employee)
                            ->whereBetween('essentials_allowances_and_deductions.applicable_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])
                            ->where('essentials_allowances_and_deductions.type', 'allowance')
                            ->sum('essentials_allowances_and_deductions.amount');
                        
                        if ($allowances > 0) {
                            $salary_amount = $allowances;
                            $debug_info['method'] = 'allowances';
                            $debug_info['allowances_total'] = $allowances;
                        } else {
                            $debug_info['method'] = 'no_data_found';
                        }
                    }
                }
            }
            
            // Get selected employee details
            $employee = $employees->firstWhere('id', $selected_employee);
            $spouse_count = ($employee && $employee->marital_status == 'married') ? 1 : 0;
            $child_count = $employee ? $employee->child_count : 0;
            
            // Calculate tax
            $gross_salary_khr = $salary_amount * $exchange_rate;
            $dependent_deduction_khr = ($spouse_count + $child_count) * 150000;
            $taxable_salary_khr = max(0, $gross_salary_khr - $dependent_deduction_khr);
            
            // Calculate salary tax
            $salary_tax_calculation = $this->calculateSalaryTaxKhr($taxable_salary_khr);
            $salary_tax_khr = $salary_tax_calculation['tax_khr'];
            
            // Convert back to USD
            $tax_amount = $salary_tax_khr / $exchange_rate;
            
            $net_salary = $salary_amount - $tax_amount;
        }
        
        // For AJAX requests, return data as JSON
        if ($request->ajax()) {
            $employee = $employees->firstWhere('id', $selected_employee);
            
            return response()->json([
                'employee' => $employee,
                'salary_amount' => $salary_amount,
                'tax_amount' => $tax_amount,
                'net_salary' => $net_salary,
                'formatted_date' => $formatted_date,
                'exchange_rate' => $exchange_rate,
                'debug_info' => isset($debug_info) ? $debug_info : null
            ]);
        }
        
        // Get the business information
        $business = \App\Business::findOrFail($business_id);
        
        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.payroll_slip')
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
     * Payroll Allowance and Deduction Report
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function payrollAllowanceDeductionReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $is_admin || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        // Get date range
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];


        // Get users/employees for the filter
        $users = User::where('business_id', $business_id)
                     ->whereNull('deleted_at')
                     ->select('id', 'first_name', 'last_name', 'username')
                     ->get();

        // Get payment methods
        $payment_methods = $this->transactionUtil->payment_types();
        
        // Filter by user if specified
        $user_filter = $request->input('username_filter', '');

        // Check if the filter is a numeric ID or a username string
        $is_numeric_filter = is_numeric($user_filter);
        
        // We'll always use the direct SQL query as it's more performant
        $sql = "SELECT 
            u.id as user_id,
            u.username,
            CONCAT(u.first_name, ' ', u.last_name) AS name,
            DATE_FORMAT(t.transaction_date, '%Y-%m') AS payroll_month,
            t.transaction_date,
            tp.paid_on AS payment_date,
            t.final_total AS base_salary,
            0 AS allowances,
            '' AS allowance_descriptions,
            0 AS deductions,
            '' AS deduction_descriptions,
            t.final_total AS net_salary,
            tp.method AS payment_method,
            t.payment_status
        FROM users u
        JOIN business b ON u.business_id = b.id
        JOIN transactions t ON t.expense_for = u.id AND t.type = 'payroll'
        LEFT JOIN transaction_payments tp ON tp.transaction_id = t.id
        WHERE b.id = ? AND u.status = 'active'
            AND (
                (tp.paid_on IS NOT NULL AND tp.paid_on BETWEEN ? AND ?)
                OR (tp.paid_on IS NULL AND t.transaction_date BETWEEN ? AND ?)
            )
            AND u.status = 'active' " ;

        $params = [
            // Parameter for business ID
            $business_id,
            // Parameters for payment date range
            $start_date->format('Y-m-d 00:00:00'), 
            $end_date->format('Y-m-d 23:59:59'),
            // Parameters for transaction date range (fallback)
            $start_date->format('Y-m-d 00:00:00'), 
            $end_date->format('Y-m-d 23:59:59')
        ];

        // Add user filter if provided
        if (!empty($user_filter)) {
            if ($is_numeric_filter) {
                $sql .= " AND u.id = ?";
            } else {
                $sql .= " AND u.username = ?";
            }
            $params[] = $user_filter;
        }
        
        $sql .= " ORDER BY DATE_FORMAT(t.transaction_date, '%Y-%m') DESC";
        
        // Execute the query
        $payroll_data = DB::select($sql, $params);
        
        // Convert the result to a collection
        $payroll_data = collect($payroll_data);
        
        // Get allowances and deductions separately and organize by user ID and month
        $allowances_deductions_sql = "
            SELECT 
                u.id as user_id,
                u.username,
                DATE_FORMAT(ead.applicable_date, '%Y-%m') AS payroll_month,
                ead.applicable_date,
                SUM(CASE WHEN ead.type = 'allowance' THEN ead.amount ELSE 0 END) AS allowances_amount,
                GROUP_CONCAT(CASE 
                    WHEN ead.type = 'allowance' 
                    THEN CONCAT(ead.description, ' (', ead.amount, ')') 
                    ELSE NULL 
                END SEPARATOR ', ') AS allowance_descriptions,
                SUM(CASE WHEN ead.type = 'deduction' AND ead.amount > 0 THEN ead.amount ELSE 0 END) AS deductions_amount,
                GROUP_CONCAT(CASE 
                    WHEN ead.type = 'deduction' AND ead.amount > 0 
                    THEN CONCAT(ead.description, ' (', ead.amount, ')') 
                    ELSE NULL 
                END SEPARATOR ', ') AS deduction_descriptions
            FROM users u
            JOIN business b ON u.business_id = b.id
            JOIN essentials_user_allowance_and_deductions eua ON eua.user_id = u.id
            JOIN essentials_allowances_and_deductions ead ON ead.id = eua.allowance_deduction_id
            WHERE b.id = ?
                AND ead.applicable_date BETWEEN ? AND ?
                AND u.status = 'active'";
        
        $ad_params = [
            $business_id,
            $start_date->format('Y-m-d 00:00:00'), 
            $end_date->format('Y-m-d 23:59:59')
        ];
        
        // Add user filter to additional query if provided
        if (!empty($user_filter)) {
            if ($is_numeric_filter) {
                $allowances_deductions_sql .= " AND u.id = ?";
            } else {
                $allowances_deductions_sql .= " AND u.username = ?";
            }
            $ad_params[] = $user_filter;
        }
        
        $allowances_deductions_sql .= " GROUP BY u.id, u.username, DATE_FORMAT(ead.applicable_date, '%Y-%m')";
        
        $allowances_deductions = DB::select($allowances_deductions_sql, $ad_params);
        
        // Create lookup arrays for allowances and deductions by user and month
        $allowances_lookup = [];
        $deductions_lookup = [];
        $allowance_descriptions_lookup = [];
        $deduction_descriptions_lookup = [];
        
        foreach ($allowances_deductions as $ad) {
            $key = $ad->user_id . '-' . $ad->payroll_month;
            $allowances_lookup[$key] = $ad->allowances_amount;
            $deductions_lookup[$key] = $ad->deductions_amount;
            $allowance_descriptions_lookup[$key] = $ad->allowance_descriptions;
            $deduction_descriptions_lookup[$key] = $ad->deduction_descriptions;
        }
        
        // Add special flag for rows that only have allowances/deductions but no payroll
        $months_with_only_allowances_deductions = [];
        foreach ($allowances_deductions as $ad) {
            $key = $ad->user_id . '-' . $ad->payroll_month;
            $months_with_only_allowances_deductions[$key] = true;
        }
        
        // Add standalone allowance/deduction entries (with no payroll)
        foreach ($allowances_deductions as $ad) {
            $key = $ad->user_id . '-' . $ad->payroll_month;
            $has_payroll_entry = $payroll_data->first(function($item) use ($ad) {
                return $item->user_id == $ad->user_id && $item->payroll_month == $ad->payroll_month;
            });
            
            // Only add if there's no payroll entry for this user/month
            if (!$has_payroll_entry) {
                $additional_entry = (object)[
                    'user_id' => $ad->user_id,
                    'username' => $ad->username,
                    'name' => '', // Will be populated when we get user names
                    'payroll_month' => $ad->payroll_month,
                    'transaction_date' => $ad->applicable_date,
                    'payment_date' => null,
                    'base_salary' => 0,
                    'allowances' => $ad->allowances_amount,
                    'allowance_descriptions' => $ad->allowance_descriptions,
                    'deductions' => $ad->deductions_amount,
                    'deduction_descriptions' => $ad->deduction_descriptions,
                    'net_salary' => $ad->allowances_amount - $ad->deductions_amount,
                    'payment_method' => null,
                    'payment_status' => 'Additional'
                ];
                
                $payroll_data->push($additional_entry);
            }
        }
        
        // Get all user names for final data preparation
        $usernames = User::whereIn('id', $payroll_data->pluck('user_id')->unique())
            ->select('id', DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
            ->pluck('full_name', 'id')
            ->toArray();
        
        // Now update existing payroll entries with allowances and deductions
        $payroll_data = $payroll_data->map(function($item) use (
            $allowances_lookup, 
            $deductions_lookup, 
            $allowance_descriptions_lookup,
            $deduction_descriptions_lookup,
            $usernames
        ) {
            $key = $item->user_id . '-' . $item->payroll_month;
            
            // Add allowances and deductions for the specific month only
            if (isset($allowances_lookup[$key])) {
                $item->allowances = $allowances_lookup[$key];
            }
            
            if (isset($deductions_lookup[$key])) {
                $item->deductions = $deductions_lookup[$key];
            }
            
            if (isset($allowance_descriptions_lookup[$key])) {
                $item->allowance_descriptions = $allowance_descriptions_lookup[$key];
            }
            
            if (isset($deduction_descriptions_lookup[$key])) {
                $item->deduction_descriptions = $deduction_descriptions_lookup[$key];
            }
            
            // Recalculate net salary
            $item->net_salary = $item->base_salary + $item->allowances - $item->deductions;
            
            // Add full name if it's missing
            if (empty($item->name) && isset($usernames[$item->user_id])) {
                $item->name = $usernames[$item->user_id];
            }
            
            return $item;
        });
        
        // Apply show_allowances and show_deductions filters if needed
        $show_allowances = $request->input('show_allowances', '1') == '1';
        $show_deductions = $request->input('show_deductions', '1') == '1';
        
        if (!$show_allowances) {
            // Set allowances to 0 if they should be hidden
            $payroll_data = $payroll_data->map(function($item) {
                $item->allowances = 0;
                $item->allowance_descriptions = '';
                $item->net_salary = $item->base_salary - $item->deductions;
                return $item;
            });
        }
        
        if (!$show_deductions) {
            // Set deductions to 0 if they should be hidden
            $payroll_data = $payroll_data->map(function($item) {
                $item->deductions = 0;
                $item->deduction_descriptions = '';
                $item->net_salary = $item->base_salary + $item->allowances;
                return $item;
            });
        }
        
        // Ensure we handle the case where empty data is returned
        if ($payroll_data->isEmpty()) {
            // Create empty data with months from date range
            $month_range = [];
            $current = Carbon::parse($start_date->format('Y-m-d'))->startOfMonth();
            $end = Carbon::parse($end_date->format('Y-m-d'))->endOfMonth();
            
            while ($current->lte($end)) {
                $month_range[] = $current->format('Y-m');
                $current->addMonth();
            }
            
            // Get active users to create empty entries for
            $active_users_query = User::where('business_id', $business_id)
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->select(['id as user_id', 'username', DB::raw("CONCAT(first_name, ' ', last_name) as name")]);
            
            // Apply user filter consistently
            if (!empty($user_filter)) {
                if ($is_numeric_filter) {
                    $active_users_query->where('id', $user_filter);
                } else {
                    $active_users_query->where('username', $user_filter);
                }
            }
            
            $active_users = $active_users_query->get();
                
            foreach ($active_users as $user) {
                foreach ($month_range as $month) {
                    $payroll_data->push((object)[
                        'user_id' => $user->user_id,
                        'username' => $user->username,
                        'name' => $user->name,
                        'payroll_month' => $month,
                        'transaction_date' => Carbon::createFromFormat('Y-m', $month)->endOfMonth()->format('Y-m-d'),
                        'payment_date' => null,
                        'base_salary' => 0,
                        'allowances' => 0,
                        'deductions' => 0,
                        'net_salary' => 0,
                        'payment_method' => null,
                        'payment_status' => 'No Payroll',
                        'allowance_descriptions' => '',
                        'deduction_descriptions' => ''
                    ]);
                }
            }
        }
        
        // Sort the combined data by payment date or transaction date
        $payroll_data = $payroll_data->sortBy(function($item) {
            return [$item->username, -strtotime($item->transaction_date)]; // Sort by username ASC, date DESC
        })->values();
        
        // Calculate totals
        $totals = [
            'base_salary' => $payroll_data->sum('base_salary'),
            'allowances' => $payroll_data->sum('allowances'),
            'deductions' => $payroll_data->sum('deductions'),
            'net_salary' => $payroll_data->sum('net_salary')
        ];
        
        // Get all active users for the dropdown
        $users_query = User::where('business_id', $business_id)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->select('id', 'first_name', 'last_name', 'username')
            ->orderBy(DB::raw("CONCAT(first_name, ' ', last_name)"));
        
        $users = $users_query->get();
        
        // Check if it's an AJAX request for partial data
        if ($request->ajax() || $request->input('load_data') == 1) {
            $load_data = true;
            return view('minireportb1::MiniReportB1.StandardReport.HumanResource.payroll_data', 
                    compact('payroll_data', 'start_date', 'end_date', 'payment_methods', 'totals', 'load_data', 'user_filter'));
        }

        $load_data = false;
        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.payroll_allowance_deduction_report', 
                compact('payroll_data', 'start_date', 'end_date', 'users', 'payment_methods', 'totals', 'load_data', 'user_filter'));
    }

    /**
     * Bank Reconciliation Report
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function bankReconciliationReport(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        // Get date range
        $dateRange = $this->dateFilterService->calculateDateRange($request);
        $start_date = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        // Format dates for display
        $formatted_date = $end_date->format('d F Y');

        // Get accounts for filter dropdown
        $accounts = Account::where('business_id', $business_id)
            ->select('id', 'name', 'account_number', 'account_details')
            ->orderBy('name')
            ->get();

        // Get selected account
        $account_id = $request->input('account_id');

        // Base query to get bank account balances
        $query = DB::table('accounts AS a')
            ->leftJoin('account_transactions AS tr', 'a.id', '=', 'tr.account_id')
            ->where('a.business_id', $business_id)
            ->groupBy('a.id', 'a.account_number', 'a.name', 'a.account_details')
            ->select(
                'a.account_number AS bank_account_no',
                'a.name AS account_name',
                'a.account_details AS bank_name',
                DB::raw('IFNULL(SUM(CASE WHEN tr.type = "credit" THEN tr.amount WHEN tr.type = "debit" THEN -tr.amount ELSE 0 END), 0) AS balance_per_book')
            )
            ->havingRaw('IFNULL(SUM(CASE WHEN tr.type = "credit" THEN tr.amount WHEN tr.type = "debit" THEN -tr.amount ELSE 0 END), 0) > 0');

        // Apply account filter if selected
        if (!empty($account_id)) {
            $query->where('a.id', $account_id);
        }

        // Get exchange rate
        $exchangeRate = ExchangeRate::where('business_id', $business_id)
            ->whereDate('date_1', '<=', $end_date)
            ->orderBy('date_1', 'desc')
            ->first();

        $exchange_rate = $exchangeRate ? $exchangeRate->KHR_3 : 4100;

        // Get the bank account data
        $bank_accounts = $query->get();

        // Format data for display
        $formatted_data = [];
        foreach ($bank_accounts as $account) {
            $formatted_data[] = [
                'bank_account_no' => $account->bank_account_no,
                'account_name' => $account->account_name,
                'bank_name' => $account->bank_name,
                'balance_per_book' => $account->balance_per_book,
                'balance_per_book_khr' => $account->balance_per_book * $exchange_rate,
                'balance_per_statement' => $account->balance_per_book * $exchange_rate, // Default same as book balance
                'adjusted_bank_balance' => $account->balance_per_book * $exchange_rate, // Default same as statement balance
                'adjusted_book_balance' => $account->balance_per_book, // Default same as book balance
                'difference' => 0 // Default no difference
            ];
        }

        // For AJAX requests, return data as JSON
        if ($request->ajax()) {
            return response()->json([
                'data' => $formatted_data,
                'total' => count($formatted_data),
                'formatted_date' => $formatted_date,
                'exchange_rate' => $exchange_rate
            ]);
        }

        // Get business details
        $business = Business::find($business_id);

        return view('minireportb1::MiniReportB1.StandardReport.HumanResource.bank_reconciliation')
            ->with(compact('accounts', 'formatted_data', 'business', 'formatted_date', 'exchange_rate', 'start_date', 'end_date'));
    }
}
