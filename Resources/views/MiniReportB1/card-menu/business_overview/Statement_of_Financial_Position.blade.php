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

                /* Replace buttons with their labels */
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

                /* Ensure table and cells are properly styled */
                table {
                    background-color: #ffffff !important;
                    border-collapse: collapse !important;
                    width: 100% !important;
                }

                th, td {
                    border: 1px solid #000 !important;
                    background-color: #ffffff !important;
                }

                /* Adjust padding and alignment for print */
                .account-row span {
                    padding: 2px 0 !important;
                }

                .box-header, .box {
                    background-color: #ffffff !important;
                    border: none !important;
                }

                /* Hide arrows in print view */
                button::before {
                    content: "" !important;
                }
            }
    </style>

@section('content')
    <section class="content" style="background-color: #f7f8fa ">
        <div class="filters-container no-print">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_range_filter">@lang('report.date_range')</label>
                    <input type="text" class="form-control" id="date_range_filter" name="date_range_filter" value="{{ $start_date }} ~ {{ $end_date }}">
                </div>
            </div>
            <button id="printButton" class="btn btn-primary " style="margin-top:24px ">
                <i class="fas fa-print"></i> Print
            </button>
            @endcomponent
        </div>
        <br><br>
        <div class="col-md-10 col-md-offset-1" style="background-color: #ffffff;">
            <div class="box  .no-print" style="background-color: #ffffff; ">
                <div class="box-header with-border text-center">
                    <h4 class="box-title" style="font-size: 24px;">{{ Session::get('business.name') }}</h4>
                    <br><br>
                    <h5 class="box-title"><b>@lang('accounting::lang.balance_sheet')</b></h5>
                    <p>As of {{ @format_date($start_date) }} ~ {{ @format_date($end_date) }}</p>
                </div>

                <div>
                    <table class="table table-striped table-bordered"
                        style="min-height: 300px; width: 100%; border-collapse: collapse; background-color: #ffffff;">
                        <thead style="background-color: #ffffff;">
                            <tr>
                                <th
                                    style="width: 70%; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align:left;">
                                    Account Name
                                </th>
                                <th
                                    style="width: 30%; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align:right;">
                                    Total Balance
                                </th>
                            </tr>
                        </thead>

                        <tbody id="balance-sheet-body" style="background-color: #ffffff;">

                            @foreach ($account_types as $type => $details)
                                @php
                                    $primary_type_total_balance = $accounts
                                        ->whereIn(
                                            'account_sub_type_id',
                                            $account_sub_types->where('account_primary_type', $type)->pluck('id'),
                                        )
                                        ->sum('balance');
                                @endphp
                                @if (in_array($type, ['expenses', 'liability', 'equity', 'income', 'asset']))
                                    <!-- Ensure only allowed types are shown -->
                                    <tr style="background-color: #ffffff;">
                                        <td colspan="2">
                                            <!-- Primary Dropdown Button -->
                                            <button onclick="toggleDropdown(this)" data-label="{{ $details['label'] }}"
                                                style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff;">
                                                &#9660; {{ $details['label'] }}
                                            </button>

                                            <!-- Primary Dropdown Content (hidden by default) -->
                                            <div class="dropdown-content" style="display: none;">
                                                @foreach ($account_sub_types->where('account_primary_type', $type)->all() as $sub_type)
                                                    @php
                                                        $subtype_accounts = $accounts
                                                            ->where('account_sub_type_id', $sub_type->id)
                                                            ->sortBy('name');
                                                        $subtype_accounts_grouped = $subtype_accounts->groupBy(
                                                            'detail_type.id',
                                                        );
                                                        $total_balance = $subtype_accounts->sum('balance');
                                                    @endphp
                                                    <div style="padding-left: 20px;">
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
                                                                    $detail_total = $accounts_in_detail->sum('balance');
                                                                @endphp
                                                                <div style="padding-left: 20px;">
                                                                    <!-- Detail-type Dropdown Button -->
                                                                    <button onclick="toggleDropdown(this)"
                                                                        data-label="{{ $detail_type->account_type_name }}"
                                                                        style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: light; background-color: #ffffff;">
                                                                        &#9660; {{ $detail_type->account_type_name }}
                                                                    </button>

                                                                    <!-- Detail-type Dropdown Content -->
                                                                    <div class="dropdown-content" style="display: none;">
                                                                        @foreach ($accounts_in_detail as $account)
                                                                            <div class="account-row"
                                                                                data-balance="{{ $account->balance }}"
                                                                                style="padding-left: 20px; display: flex; justify-content: space-between; background-color: #ffffff;">
                                                                                <span>{{ $account->account_number }}&nbsp;&nbsp;&nbsp;{{ $account->name }}</span>
                                                                                <a href="{{ route('accounting.ledger', $account->id) }}"
                                                                                    style="text-decoration: none; color: inherit;">
                                                                                    <span>@format_currency($account->balance)</span>
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                        <!-- Detail-type Total -->
                                                                        <div
                                                                            style="border-top: 1px solid #000; padding-left: 10px; font-weight: light; display: flex; justify-content: space-between; background-color: #ffffff;">
                                                                            <span>Total for
                                                                                {{ $detail_type->account_type_name }}</span>
                                                                            <span>@format_currency($detail_total)</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            <!-- Sub-type Total -->
                                                            <div
                                                                style="border-top: 1px solid #000; padding-left: 10px; font-weight: bold; display: flex; justify-content: space-between; background-color: #ffffff;">
                                                                <span>Total for {{ $sub_type->account_type_name }}</span>
                                                                <span>@format_currency($total_balance)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div
                                                style="border-top: 1px solid #000; padding-left: 10px; font-weight: bold; display: flex; justify-content: space-between; background-color: #ffffff;">
                                                <span>Total for {{ $details['label'] }}</span>
                                                <span>@format_currency($primary_type_total_balance)</span>
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
        });

        $('#dropdownMenuButton').on('click', function() {
            const dropdownContent = $('#customDropdownContent');
            dropdownContent.toggle();
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#dropdownMenuButton, #customDropdownContent').length) {
                $('#customDropdownContent').hide();
            }
        });

        document.getElementById('printButton')?.addEventListener('click', function() {
            window.print();
        });

        function toggleDropdown(button) {
            const dropdownContainer = button.nextElementSibling;
            const arrow = button.innerHTML.trim().charAt(0);
            if (dropdownContainer.style.display === "none" || dropdownContainer.style.display === "") {
                dropdownContainer.style.display = "block";
                button.innerHTML = button.innerHTML.replace(arrow, '&#9660;'); // Change arrow down
            } else {
                dropdownContainer.style.display = "none";
                button.innerHTML = button.innerHTML.replace(arrow, '&#9654;'); // Change arrow right
            }
        }

        function printBalanceSheet() {
            var printContents = document.querySelector('.col-md-10.col-md-offset-1').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(); // Reload to restore the JavaScript functionality
        }

        function toggleDropdown(el) {
            $(el).next('.dropdown-content').slideToggle(300);
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            dateRangeSettings.startDate = moment('{{ $start_date }}');
            dateRangeSettings.endDate = moment('{{ $end_date }}');

            $('#date_range_filter').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
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
        });
    </script>

@endsection
