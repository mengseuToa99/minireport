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

@include('minireportb1::MiniReportB1.components.back_to_dashboard_button')
   

    <div style="margin: 16px" class="no-print">
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
            Monthly Expense Report
        </h2>
    </div>

<div class="reusable-table-container">
    <table class="reusable-table" id="expense-table">
        <thead>
            <tr>
                <th class="col-xs">#</th>
                <th class="col-sm">Date</th>
                <th class="col-md">Contact Name</th>
                <th class="col-md">Expense Note</th>
                <th class="col-xs">Type</th>
                <th class="col-sm">Ref No</th>
                <th class="col-sm">Total Amount ($)</th>
                <th class="col-sm">Amount Paid ($)</th>
                <th class="col-xs">VAT Input 10%</th>
                <th class="col-xs">Rate</th>
                <th class="col-xs">VAT Input (KHR)</th>
                <th class="col-xs">Total Amount (KHR)</th>
                <th class="col-sm">Amount Paid (KHR)</th>
                <th class="col-xs">WHT 10% (KHR)</th>
                <th class="col-xs">VAT Tin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr class="table-row"> <!-- Fixed class attribute -->
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $expense->contact_name }}</td>
                    <td>{{ $expense->additional_notes }}</td>
                    <td>PV</td>
                    <td>{{ $expense->ref_no }}</td>
                    <td class="number">{{ number_format($expense->final_total, 2) }}</td>
                    <td class="number"></td>
                    <td class="number"></td>
                    <td class="number">{{ $expense->exchange_rate_khr ?? 0 }}</td>
                    <td class="number">
                        @if (isset($expense->exchange_rate_khr))
                            {{ number_format($expense->final_total * 0.1 * $expense->exchange_rate_khr, 0) }}
                        @endif
                    </td>
                    <td class="number">
                        @if (isset($expense->exchange_rate_khr))
                            {{ number_format($expense->final_total * $expense->exchange_rate_khr, 0) }}
                        @endif
                    </td>
                    <td class="number">
                        {{ number_format($expense->final_total * $expense->exchange_rate_khr, 0) }}
                    </td>
                    <td class="number">
                        @if (isset($expense->exchange_rate_khr))
                            {{ number_format($expense->final_total * $expense->exchange_rate_khr * 0.1, 0) }}
                        @endif
                    </td>
                    <td>{{ $expense->tax_number }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center">No expense records available.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            @php
                $total_final_total = $expenses->sum('final_total');
                $total_final_total_khr = $expenses->sum(function ($expense) {
                    return isset($expense->exchange_rate_khr)
                        ? $expense->final_total * $expense->exchange_rate_khr
                        : 0;
                });
                $total_wht_khr = $expenses->sum(function ($expense) {
                    return isset($expense->exchange_rate_khr)
                        ? $expense->final_total * $expense->exchange_rate_khr * 0.1
                        : 0;
                });
            @endphp
            <tr>
                <td colspan="6" class="text-right"><strong>Total:</strong></td>
                <td class="number"><strong>{{ number_format($total_final_total, 2) }}</strong></td>
                <td class="number"></td>
                <td class="number"></td>
                <td></td>
                <td></td>
                <td class="number"></td>
                <td class="number"><strong>{{ number_format($total_final_total_khr, 0) }}</strong></td>
                <td class="number"></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

    <script>
        const tablename = "#expense-table";
        const reportname = "Monthly Expense Report";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

@endsection
