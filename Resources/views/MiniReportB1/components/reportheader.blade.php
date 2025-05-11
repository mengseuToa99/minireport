@php
    // Get business info directly from the database for reliability
    $business_id = session('user.business_id');
    $business = \App\Business::find($business_id);
    $directLogoPath = $business && $business->logo ? '/uploads/business_logos/' . $business->logo : '';
    
    // Get date range information from filter
    $date_filter = request('date_filter');
    $start_date_value = request('start_date');
    $end_date_value = request('end_date');
    
    // Format date range text based on selected filter
    $date_range_text = '';
    
    if ($date_filter === 'custom_month_range' && $start_date_value && $end_date_value) {
        // Format custom date range
        $start_formatted = \Carbon\Carbon::parse($start_date_value)->format('d/m/Y');
        $end_formatted = \Carbon\Carbon::parse($end_date_value)->format('d/m/Y');
        $date_range_text = $start_formatted . ' - ' . $end_formatted;
    } elseif ($date_filter === 'today') {
        $date_range_text = trans('minireportb1::minireportb1.today') . ': ' . \Carbon\Carbon::today()->format('d/m/Y');
    } elseif ($date_filter === 'this_month') {
        $start = \Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.this_month') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'last_month') {
        $start = \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->subMonth()->endOfMonth()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.last_month') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'last_3_months') {
        $start = \Carbon\Carbon::now()->subMonths(3)->startOfMonth()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.last_3_months') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'last_6_months') {
        $start = \Carbon\Carbon::now()->subMonths(6)->startOfMonth()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.last_6_months') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'this_quarter') {
        $start = \Carbon\Carbon::now()->startOfQuarter()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->endOfQuarter()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.this_quarter') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'last_quarter') {
        $start = \Carbon\Carbon::now()->subQuarter()->startOfQuarter()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->subQuarter()->endOfQuarter()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.last_quarter') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'this_year') {
        $start = \Carbon\Carbon::now()->startOfYear()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->endOfYear()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.this_year') . ': ' . $start . ' - ' . $end;
    } elseif ($date_filter === 'last_year') {
        $start = \Carbon\Carbon::now()->subYear()->startOfYear()->format('d/m/Y');
        $end = \Carbon\Carbon::now()->subYear()->endOfYear()->format('d/m/Y');
        $date_range_text = trans('minireportb1::minireportb1.last_year') . ': ' . $start . ' - ' . $end;
    } else {
        // Default to all dates
        $date_range_text = trans('minireportb1::minireportb1.all_dates');
    }
@endphp

<div class="report-header">
    <div class="header-left">
        @if($business && $business->logo)
        <img src="{{ asset($directLogoPath) }}" 
             class="business-logo" 
             alt="{{ $business->name ?? 'Business' }} Logo"
             style="height: 50px; width: 50px; object-fit: contain;">
        @endif
        <div class="business-name">{{ $business->name ?? 'Business Name' }}</div>
    </div>
    <div class="header-right">
        <div class="report-name">{{ $report_name ?? 'Report' }}</div>
        <div class="date-range" id="report-date-range">{{ $date_range_text }}</div>
        <div class="date-range date-printed">{{ __('Printed on') }}: {{ date('Y-m-d H:i:s') }}</div>
    </div>
</div>

<style>
    .report-header {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
    }
    .header-left {
        display: flex;
        align-items: center;
        flex: 1;
    }
    .header-right {
        flex: 1;
        text-align: right;
    }
    .business-logo {
        max-height: 50px;
        max-width: 50px;
        margin-right: 15px;
    }
    .business-name {
        font-size: 20px;
        font-weight: 600;
    }
    .report-name {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .date-range {
        font-size: 14px;
        margin-top: 5px;
    }
    .date-printed {
        font-style: italic;
        color: #666;
    }
    
    @media print {
        .report-header {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
             