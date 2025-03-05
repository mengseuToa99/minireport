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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year_filter">@lang('report.year')</label>
                        <select id="year_filter" name="year" class="form-control">
                            @foreach ($available_years as $yr)
                                <option value="{{ $yr }}" {{ $yr == $year ? 'selected' : '' }}>{{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button id="printButton" class="btn btn-primary" style="margin-top:24px;">
                    <i class="fas fa-print"></i> Print
                </button>
            @endcomponent
        </div>

        </div>
        <br><br>
        <!-- Removed no-print class from the report container -->
        <div class="col-md-12" style="background-color: #ffffff;">
            <div class="box" style="background-color: #ffffff;">
                <div class="box-header with-border text-center">
                    <h4 class="box-title" style="font-size: 24px;">{{ Session::get('business.name') }}</h4>
                    <br><br>
                    <h5 class="box-title"><b>@lang('accounting::lang.balance_sheet')</b></h5>
                    <p>As of {{ @format_date($start_date) }} ~ {{ @format_date($end_date) }}</p>
                </div>

                <div class="table-container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 12%; text-align: end; font-weight:light; font-size:10px; margin-left: 10px"> Exchange Rate <br><br>&nbsp;</th>
                                @foreach ($months as $month)
                                    <th style="text-align: right; font-weight: bold; font-size:10px; ">{!! $month['name'] !!}</th>
                                @endforeach

                            </tr>
                        </thead>
                        <tbody id="balance-sheet-body">
                            @foreach ($account_types as $type => $details)
                                @if (in_array($type, ['expenses', 'income']))
                                    <tr>
                                        <td colspan="{{ count($months) + 1 }}">
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
                                                        <!-- Sub-type Dropdown Button -->
                                                        <button onclick="toggleDropdown(this)"
                                                            data-label="{{ $sub_type->account_type_name }}"
                                                            style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff;">
                                                            &#9660; {{ $sub_type->account_type_name }}
                                                        </button>

                                                        <!-- Sub-type Dropdown Content -->
                                                        <div class="dropdown-content" style="display: none;">
                                                            @foreach ($subtype_accounts_grouped as $detail_type_id => $accounts_in_detail)
                                                                @php
                                                                    $detail_type = $account_detail_types->firstWhere(
                                                                        'id',
                                                                        $detail_type_id,
                                                                    );
                                                                    if (!$detail_type) {
                                                                        continue;
                                                                    }
                                                                @endphp
                                                                <div>
                                                                    <!-- Detail-type Dropdown Button -->
                                                                    <button onclick="toggleDropdown(this)"
                                                                        data-label="{{ $detail_type->account_type_name }}"
                                                                        style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: light; background-color: #ffffff;">
                                                                        &#9660; {{ $detail_type->account_type_name }}
                                                                    </button>

                                                                    <!-- Detail-type Dropdown Content -->
                                                                    <div class="dropdown-content" style="display: none;">
                                                                        @foreach ($accounts_in_detail as $account)
                                                                            <div class="account-row">
                                                                                <span>{{ $account->account_number }}&nbsp;&nbsp;&nbsp;{{ $account->name }}</span>
                                                                                @foreach ($account->monthly_balances as $balance)
                                                                                    {{-- <span>@format_currency($balance)</span> --}}
                                                                                @endforeach
                                                                            </div>
                                                                        @endforeach

                                                                        <!-- Detail-type Total -->
                                                                        <div class="account-row"
                                                                            style="border-top: 1px solid #000; font-weight: light;">
                                                                            <span>Total for
                                                                                {{ $detail_type->account_type_name }}</span>
                                                                            @for ($i = 0; $i < 12; $i++)
                                                                                @php
                                                                                    $monthly_total = $accounts_in_detail->sum(
                                                                                        function ($account) use ($i) {
                                                                                            return $account
                                                                                                ->monthly_balances[
                                                                                                $i
                                                                                            ] ?? 0;
                                                                                        },
                                                                                    );
                                                                                @endphp
                                                                                <span>@format_currency($monthly_total)</span>
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                            <!-- Sub-type Total -->
                                                            <div class="account-row"
                                                                style="border-top: 1px solid #000; font-weight: bold;">
                                                                <span>Total for {{ $sub_type->account_type_name }}</span>
                                                                @for ($i = 0; $i < 12; $i++)
                                                                    @php
                                                                        $monthly_total = $subtype_accounts->sum(
                                                                            function ($account) use ($i) {
                                                                                return $account->monthly_balances[$i] ??
                                                                                    0;
                                                                            },
                                                                        );
                                                                    @endphp
                                                                    <span>@format_currency($monthly_total)</span>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <!-- Primary Type Total -->
                                                <div class="account-row"
                                                    style="border-top: 1px solid #000; font-weight: bold;">
                                                    <span>Total for {{ $details['label'] }}</span>
                                                    @for ($i = 0; $i < 12; $i++)
                                                        @php
                                                            $monthly_total = $accounts
                                                                ->whereIn(
                                                                    'account_sub_type_id',
                                                                    $account_sub_types
                                                                        ->where('account_primary_type', $type)
                                                                        ->pluck('id'),
                                                                )
                                                                ->sum(function ($account) use ($i) {
                                                                    return $account->monthly_balances[$i] ?? 0;
                                                                });
                                                        @endphp
                                                        <span>@format_currency($monthly_total)</span>
                                                    @endfor
                                                </div>
                                            </div>
                                        </td>
                                    </tr>``
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
            dateRangeSettings.startDate = moment('{{ $start_date }}');
            dateRangeSettings.endDate = moment('{{ $end_date }}');

            $('#date_range_filter').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    apply_filter();
                }
            );
            $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_range_filter').val('');
                apply_filter();
            });

            function apply_filter() {
                var start = '';
                var end = '';

                if ($('#date_range_filter').val()) {
                    start = $('input#date_range_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    end = $('input#date_range_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                }

                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('start_date', start);
                urlParams.set('end_date', end);
                window.location.search = urlParams;
            }

            // Print button event listener
            $('#printButton').on('click', function() {
                window.print();
            });
        });

        $(document).ready(function() {
            $('#year_filter').on('change', function() {
                const selectedYear = $(this).val();
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('year', selectedYear);
                window.location.search = urlParams;
            });
        });


        // Global toggleDropdown function
        function toggleDropdown(el) {
            $(el).next('.dropdown-content').slideToggle(300);
        }
    </script>
@endsection
