<?php

namespace Modules\MiniReportB1\Http\Services;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DateFilterService
{
    function calculateDateRange(Request $request)
    {
        $date_filter = $request->input('date_filter', '');
        $start_date = null;
        $end_date = null;
        
        // Special case for payroll allowance deduction report
        $route_name = $request->route() ? $request->route()->getName() : '';
        if ($route_name === 'minireportb1.standardReport.humanResource.payroll_allowance_deduction_report' && 
            empty($date_filter) && 
            !$request->filled('start_date') && 
            !$request->filled('end_date')) {
            
            // Default to January-April 2025 for this specific report
            $start_date = Carbon::parse('2025-01-01');
            $end_date = Carbon::parse('2025-04-30');
            
    
            
            return [
                'start_date' => $start_date,
                'end_date' => $end_date
            ];
        }

        if ($date_filter == 'today') {
            $start_date = Carbon::today();
            $end_date = Carbon::today();
        } else if ($date_filter == 'this_month') {
            $start_date = Carbon::now()->startOfMonth();
            $end_date = Carbon::now()->endOfMonth();
        } else if ($date_filter == 'last_month') {
            $start_date = Carbon::now()->subMonth()->startOfMonth();
            $end_date = Carbon::now()->subMonth()->endOfMonth();
        } else if ($date_filter == 'last_3_months') {
            $start_date = Carbon::now()->subMonths(3)->startOfMonth();
            $end_date = Carbon::now()->endOfMonth();
        } else if ($date_filter == 'last_6_months') {
            $start_date = Carbon::now()->subMonths(6)->startOfMonth();
            $end_date = Carbon::now()->endOfMonth();
        } else if ($date_filter == 'this_quarter') {
            $start_date = Carbon::now()->startOfQuarter();
            $end_date = Carbon::now()->endOfQuarter();
        } else if ($date_filter == 'last_quarter') {
            $start_date = Carbon::now()->subQuarter()->startOfQuarter();
            $end_date = Carbon::now()->subQuarter()->endOfQuarter();
        } else if ($date_filter == 'this_year') {
            $start_date = Carbon::now()->startOfYear();
            $end_date = Carbon::now()->endOfYear();
        } else if ($date_filter == 'last_year') {
            $start_date = Carbon::now()->subYear()->startOfYear();
            $end_date = Carbon::now()->subYear()->endOfYear();
        } else if ($date_filter == 'custom_month_range') {
            // For custom range, use provided dates or fallback to current month
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
                $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            } else {
                // Default to current month if no dates provided for custom range
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
            }
        } else {
            // Default to current month if no valid filter provided
            $start_date = Carbon::now()->startOfMonth();
            $end_date = Carbon::now()->endOfMonth();
        }



        return [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }
    
    /**
     * Apply date filter to a query based on date_filter parameter
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $date_filter
     * @param string $date_column
     * @return \Illuminate\Database\Query\Builder
     */
    public function applyDateFilter($query, $date_filter, $date_column)
    {
        if (!$date_filter) {
            return $query;
        }
        
        switch ($date_filter) {
            case 'today':
                $query->whereDate($date_column, Carbon::today());
                break;
            case 'this_month':
                $query->whereMonth($date_column, Carbon::now()->month)
                    ->whereYear($date_column, Carbon::now()->year);
                break;
            case 'last_month':
                $last_month = Carbon::now()->subMonth();
                $query->whereMonth($date_column, $last_month->month)
                    ->whereYear($date_column, $last_month->year);
                break;
            case 'last_3_months':
                $three_months_ago = Carbon::now()->subMonths(3)->startOfMonth();
                $query->where($date_column, '>=', $three_months_ago)
                    ->where($date_column, '<=', Carbon::now()->endOfMonth());
                break;
            case 'last_6_months':
                $six_months_ago = Carbon::now()->subMonths(6)->startOfMonth();
                $query->where($date_column, '>=', $six_months_ago)
                    ->where($date_column, '<=', Carbon::now()->endOfMonth());
                break;
            case 'this_quarter':
                $start = Carbon::now()->startOfQuarter();
                $end = Carbon::now()->endOfQuarter();
                $query->where($date_column, '>=', $start)
                    ->where($date_column, '<=', $end);
                break;
            case 'last_quarter':
                $start = Carbon::now()->subQuarter()->startOfQuarter();
                $end = Carbon::now()->subQuarter()->endOfQuarter();
                $query->where($date_column, '>=', $start)
                    ->where($date_column, '<=', $end);
                break;
            case 'this_year':
                $query->whereYear($date_column, Carbon::now()->year);
                break;
            case 'last_year':
                $query->whereYear($date_column, Carbon::now()->subYear()->year);
                break;
            case 'custom_month_range':
                $request = request();
                if ($request->has('start_date')) {
                    $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
                    $query->where($date_column, '>=', $start_date);
                }
                
                if ($request->has('end_date')) {
                    $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
                    $query->where($date_column, '<=', $end_date);
                }
                break;
        }
        
        return $query;
    }
}