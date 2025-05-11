{{-- Example of how to use the updated reportheader component --}}
{{-- 
    IMPORTANT UPDATE: The reportheader component has been updated to automatically retrieve 
    business information directly from the session, so you no longer need to pass 
    'business_name' or 'business_logo' parameters. 
    
    Just pass the 'report_name' parameter and optionally date range information.
--}}

@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Include the reportheader component with just report_name --}}
        @include('minireportb1::MiniReportB1.components.reportheader', [
            'report_name' => 'Example Report'
        ])
        
        {{-- If you need to specify date range parameters --}}
        @include('minireportb1::MiniReportB1.components.reportheader', [
            'report_name' => 'Example Report with Date Range',
            'date_range' => 'Jan 2023 - Dec 2023'
        ])
        
        {{-- Or if you have start and end dates separately --}}
        @include('minireportb1::MiniReportB1.components.reportheader', [
            'report_name' => 'Example Report with Start/End Dates',
            'start_date' => '2023-01-01',
            'end_date' => '2023-12-31'
        ])
        
        {{-- The rest of your report content... --}}
    </div>
@endsection 