@extends('layouts.app')
@section('title', 'Income for The Month')

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
            ក្រុមហ៊ុនឌឹ ហ្វក់សេស អាត ឯ.ក
        </h2>
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            Monthly Income Report
        </h2>
    </div>


<div class="reusable-table-container">
    <table class="reusable-table" id="income-table">
        <thead>
            <tr>
                <th class="col-sm">Date</th>
                <th class="col-md">Voucher #</th>
                <th class="col-md">Net Price ($)</th>
                <th class="col-xl">VAT 10%</th>
                <th class="col-md">Gross Sale<br>Amount ($)</th>
                <th class="col-md">Customer</th>
                <th class="col-md">Invoice NO</th>
                <th class="col-xl">Product Category</th>
                <th class="col-md">Rate (KHR)</th>
                <th class="col-md">Amount (KHR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($formatted_data as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['voucher'] }}</td>
                    <td class="number">{{ $row['unit_price'] }}</td>
                    <td class="number">{{ $row['item_tax'] }}</td>
                    <td class="number">{{ $row['subtotal'] }}</td>
                    <td>{{ $row['customer'] }}</td>
                    <td>{{ $row['invoice_no'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td class="number">{{ $row['exchange_rate_khr'] }}</td>
                    <td class="number khr-amount">{{ $row['khr_amount'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No data available for the selected period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                <td class="number" style="width: auto; max-width: 100%;">
                    <strong>{{ number_format($total_net_price ?? 0, 0, '.', ',') }}</strong>
                </td>
                <td colspan="1"></td>
                <td class="number" style="width: auto; max-width: 100%;">
                    <strong>{{ number_format($total_cross_sale ?? 0, 0, '.', ',') }}</strong>
                </td>
                <td colspan="4"></td>
                <td class="number">
                    <strong>{{ number_format($total_khr ?? 0, 0, '.', ',') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

    <script>
        const tablename = "#income-table";
        const reportname = "Monthly Expense Report";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

@endsection
