@extends('layouts.app')

@section('title', __('accounting::lang.balance_sheet'))

<style>
@media print {
    /* Hide non-print elements */
    .no-print {
        display: none !important;
    }

    /* Expand all dropdown content */
    .dropdown-content {
        display: block !important;
    }

    /* Replace dropdown buttons with static labels */
    button[onclick="toggleDropdown(this)"] {
        visibility: hidden;
        position: relative;
        padding-left: 0 !important;
        margin: 0 !important;
        background-color: transparent !important;
    }

    button[onclick="toggleDropdown(this)"]::after {
        content: attr(data-label);
        visibility: visible;
        position: absolute;
        left: 0;
        top: 0;
        font-weight: bold;
        background-color: #ffffff !important;
    }

    /* Table styling without borders */
    table {
        background-color: #ffffff !important;
        border-collapse: collapse !important;
        width: 100% !important;
        margin-bottom: 10mm;
    }

    thead th, th, td {
        border: none !important; /* Removes all borders */
        padding: 6px !important;
        background-color: #ffffff !important;
    }

    thead tr {
            border: none !important;
        }

    thead th {
        border: none !important;
        background: none !important;
    }

    /* Ensure header and container backgrounds are white */
    .box-header,
    .box {
        background-color: #ffffff !important;
        border: none !important;
    }

    /* Hide dropdown arrow pseudo-elements */
    button::before {
        content: "" !important;
    }

    /* Reduce font size for a compact print layout */
    html, body, table, th, td, .account-row span {
        font-size: 10px !important;
    }

    /* Optional: Set page margins for printing */
    @page {
        margin: 15mm;
    }
}


    /* Custom CSS for better alignment */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .table {
        width: 100%;
        min-width: 800px;
        /* Adjust as needed */
    }

    .table th,
    .table td {
        padding: 8px 12px;
        text-align: left;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .dropdown-content {
        padding-left: 20px;
    }

    .account-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
    }

    .account-row span {
        flex: 1;
        text-align: right;
    }

    .account-row span:first-child {
        text-align: left;
    }
</style>
@section('content')
    <section class="content" style="background-color: #f7f8fa;">
        <div class="filters-container no-print">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="date-picker-container">
                    <div class="date-picker-group">
                        <div class="form-group">
                            <label for="first_date">First Period</label>
                            <input type="month" id="first_date" name="first_date" 
                                   class="form-control" 
                                   value="{{ Carbon\Carbon::parse($first_date)->format('Y-m') }}">
                        </div>
                    </div>
                    <div class="date-picker-group">
                        <div class="form-group">
                            <label for="second_date">Second Period</label>
                            <input type="month" id="second_date" name="second_date" 
                                   class="form-control" 
                                   value="{{ Carbon\Carbon::parse($second_date)->format('Y-m') }}">
                        </div>
                    </div>
                    <button id="updateDates" class="btn btn-primary" style="margin-bottom: 1px;">
                        <i class="fas fa-sync"></i> Update
                    </button>
                    <button id="printButton" class="btn btn-primary" style="margin-bottom: 1px;">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            @endcomponent
        </div>

        <br><br>
        <!-- Report Container -->
        <div class="col-md-12" style="background-color: #ffffff;">
            <div class="box" style="background-color: #ffffff;">
                <div class="box-header with-border text-center">
                    <h4 class="box-title" style="font-size: 24px;">{{ Session::get('business.name') }}</h4>
                    <br><br>
                    <h5 class="box-title"><b>@lang('accounting::lang.balance_sheet')</b></h5>
                    <p>Comparing {{ Carbon\Carbon::parse($first_date)->format('F Y') }} vs {{ Carbon\Carbon::parse($second_date)->format('F Y') }}</p>
                </div>

                <div class="table-container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 12%; text-align: end; font-weight:light; font-size:10px; margin-left: 10px"> Exchange Rate <br><br>&nbsp;</th>
                                @foreach ($comparison_periods as $period)
                                    <th style="text-align: right; font-weight: bold; font-size:10px;">{!! $period['name'] !!}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="balance-sheet-body">
                            @foreach ($account_types as $type => $details)
                                @if (in_array($type, ['expenses', 'income']))
                                    <tr>
                                        <td colspan="{{ count($comparison_periods) + 1 }}">
                                            <!-- Primary Dropdown Button -->
                                            <button onclick="toggleDropdown(this)" data-label="{{ $details['label'] }}"
                                                style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff;">
                                                &#9660; {{ $details['label'] }}
                                            </button>

                                            <!-- Primary Dropdown Content -->
                                            <div class="dropdown-content" style="display: none;">
                                                @foreach ($account_sub_types->where('account_primary_type', $type)->all() as $sub_type)

                                                @php

                                                    $subtype_accounts = $accounts

                                                        ->where('account_sub_type_id', $sub_type->id)

                                                        ->sortBy('name');

                                                    $subtype_accounts_grouped = $subtype_accounts->groupBy(

                                                        'detail_type.id',

                                                    );

                                                @endphp

                                                <div>
                                                        <!-- Sub-type content remains the same, just change monthly_balances to period_balances -->
                                                        <!-- ... Previous sub-type content ... -->
                                                        @foreach ($accounts_in_detail as $account)

                                                        <div class="account-row">

                                                            <span>{{ $account->account_number }}&nbsp;&nbsp;&nbsp;{{ $account->name }}</span>

                                                            @foreach ($account->period_balances as $balance)

                                                                <span>@format_currency($balance)</span>

                                                            @endforeach

                                                        </div>

                                                    @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#updateDates').on('click', function() {
                const firstDate = $('#first_date').val() + '-01';  // Add day to make it a valid date
                const secondDate = $('#second_date').val() + '-01';
                
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('first_date', firstDate);
                urlParams.set('second_date', secondDate);
                window.location.search = urlParams;
            });

            $('#printButton').on('click', function() {
                window.print();
            });
        });

        function toggleDropdown(el) {
            $(el).next('.dropdown-content').slideToggle(300);
        }
    </script>
@endsection