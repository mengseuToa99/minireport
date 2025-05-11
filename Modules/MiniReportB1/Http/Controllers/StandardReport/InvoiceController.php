<?php

namespace App\Http\Controllers\StandardReport;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Business;
use App\Models\DB;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        // Get the exchange rate closest to the selected date
        $exchangeRate = ExchangeRate::where('business_id', $business_id)
            ->where(function($query) use ($report_date) {
                $query->where('date_1', '<=', $report_date)
                      ->whereNotNull('date_1');
            })
            ->orderBy('date_1', 'desc')
            ->first();
            
        // If no exchange rate found with date_1 <= report_date, try to find any rate
        if (!$exchangeRate) {
            $exchangeRate = ExchangeRate::where('business_id', $business_id)
                ->whereNotNull('date_1')
                ->orderBy(DB::raw("ABS(DATEDIFF(date_1, '$report_date'))")) // Find closest date
                ->first();
        }

        // Always default to 0 for office receipt
        $exchange_rate = 0;
        
        // Only use the exchange rate if it exists
        if ($exchangeRate && !is_null($exchangeRate->KHR_3)) {
            $exchange_rate = $exchangeRate->KHR_3;
        }
        
        // Debug the exchange rate (temporary)
        dd([
            'report_date' => $report_date,
            'exchange_rate_object' => $exchangeRate,
            'final_exchange_rate' => $exchange_rate,
        ]);
        
        // Build the query using the provided SQL structure
    }
} 