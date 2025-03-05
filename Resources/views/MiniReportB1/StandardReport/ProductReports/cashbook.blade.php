@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

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
            {{ $business_name }}
        </h2>
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            CASHBOOK
        </h2>
    </div>


    <div class="reusable-table-container" id="cashbook">
        <table class="reusable-table" id="expense-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-sm">Date</th>
                    <th class="col-md">Voucher No</th>
                    <th class="col-md">Contact Name</th>
                    <th class="col-md">Expense Note</th>
                    <th class="col-md">Cash In</th>
                    <th class="col-md">Cash Out</th>
                    <th class="col-md">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($combined_data as $row)
                    <tr class="table-row"> <!-- Add the class here -->
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['voucher'] }}</td>
                        <td>{{ $row['contact_name'] }}</td>
                        <td>{{ $row['description'] }}</td>
                        <td>{{ $row['cash_in'] == 0 ? '' : $row['cash_in'] }}</td>
                        <td>{{ $row['cash_out'] == 0 ? '' : $row['cash_out'] }}</td>
                        <td>{{ $row['balance'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        const tablename = "#cashbook"; // Assuming this is the table you want to print
        const reportname = "Cashbook"; // Default report name
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

@endsection
