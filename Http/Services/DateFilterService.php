<?php

namespace Modules\MiniReportB1\Http\Services;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DateFilterService
{
    function calculateDateRange(Request $request)
    {
        $date_filter = $request->input('date_filter');
        $start_date = null;
        $end_date = null;

        switch ($date_filter) {
            case 'this_month':
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $start_date = Carbon::now()->subMonth()->startOfMonth();
                $end_date = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'last_3_months':
                $start_date = Carbon::now()->subMonths(3)->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
            case 'last_6_months':
                $start_date = Carbon::now()->subMonths(6)->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
            case 'this_quarter':
                $start_date = Carbon::now()->startOfQuarter();
                $end_date = Carbon::now()->endOfQuarter();
                break;
            case 'last_quarter':
                $start_date = Carbon::now()->subQuarter()->startOfQuarter();
                $end_date = Carbon::now()->subQuarter()->endOfQuarter();
                break;
            case 'this_year':
                $start_date = Carbon::now()->startOfYear();
                $end_date = Carbon::now()->endOfYear();
                break;
            case 'last_year':
                $start_date = Carbon::now()->subYear()->startOfYear();
                $end_date = Carbon::now()->subYear()->endOfYear();
                break;
            case 'custom_month_range':
                $start_date = $request->input('start_date') 
                    ? Carbon::parse($request->input('start_date'))->startOfDay()
                    : null;
                $end_date = $request->input('end_date') 
                    ? Carbon::parse($request->input('end_date'))->endOfDay()
                    : null;
                break;
            default:
                // Default to current month
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
        }

        return [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }
}