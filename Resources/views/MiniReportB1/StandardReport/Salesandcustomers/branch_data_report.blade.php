@extends('layouts.app')
@section('title', 'Income for The Month')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">

@endsection

@section('content')

    <div class="arrow" id="goBackButton"></div>

    <div style="margin: 16px">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                @include('minireportb1::MiniReportB1.components.filter')
            </div>
        @endcomponent
    </div>

    <div class="report-header" id="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-light tw-text-center normal-view-title" style="font-size: 20px;">
            {{$business_name}}
        </h2>
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            តារាងព័ត៌មានសាខា
        </h2>
    </div>


    <div class="reusable-table-container">
        <table class="reusable-table" id="branch-data-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-md">Contact</th>
                    <th class="col-md">Phone Number</th>
                    <th class="col-xl">Address</th>
                    <th class="col-md">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($formatted_data as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['contact'] }}</td>
                        <td>{{ $row['phone_number'] }}</td>
                        <td>{{ $row['address'] }}</td>
                        <td>{{ $row['description'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No data available for the selected period.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">
                        <strong>Total Records:</strong> {{ count($formatted_data) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#branch-data-table";
        const reportname = "Branch Data Report";
        
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

@endsection


