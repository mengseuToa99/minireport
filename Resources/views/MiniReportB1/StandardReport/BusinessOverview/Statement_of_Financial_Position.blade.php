@extends('layouts.app')

{{-- @section('title', __('accounting::lang.balance_sheet')) --}}

<style>
    /* Screen styles */
    .report-header {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .report-title {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
    }
    
    .report-subtitle {
        font-size: 22px;
        font-weight: bold;
        margin: 5px 0;
    }
    
    .report-date {
        font-size: 16px;
        margin: 0;
    }
    
    .financial-table {
        width: 100% !important;
        border-collapse: collapse;
        font-size: 15px;
        margin-top: 10px;
    }
    
    .financial-table th, .financial-table td {
        border: 1px solid #000;
        padding: 8px;
    }
    
    .financial-table th {
        text-align: left;
        background-color: #f8f9fa;
        font-size: 16px;
    }
    
    .table-container {
        width: 100%;
        overflow-x: auto;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .dropdown-content {
        padding-left: 20px;
        display: none; /* Ensure initial hidden state */
        margin-top: 0;
        margin-bottom: 0;
    }
    
    .account-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 3px 0;
        font-size: 15px;
        margin: 0;
    }
    
    .account-row span {
        flex: 1;
        text-align: right;
    }
    
    .account-row span:first-child {
        text-align: left;
        flex: 3; /* Give more space to account names */
    }
    
    /* Remove extra spacing */
    button[onclick="toggleDropdown(this)"] {
        padding: 8px !important;
        margin: 0 !important;
    }
    
    /* Reduce spacing in total sections */
    div[style*="border-top"] {
        margin-top: 5px !important;
        margin-bottom: 5px !important;
    }

    /* Print styles */
    @media print {
        /* Page setup */
        @page {
            size: landscape;
            margin: 15mm;
        }

        /* Hide non-print elements */
        .no-print {
            display: none !important;
        }

        /* Remove background colors and borders */
        body, html, section, div, .box {
            background-color: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        /* Header styling */
        .box-header {
            margin-bottom: 5mm !important;
            text-align: center !important;
        }

        /* Expand all dropdown content */
        .dropdown-content {
            display: block !important;
            padding-left: 20px !important;
            visibility: visible !important;
            height: auto !important;
            opacity: 1 !important;
            overflow: visible !important;
            margin: 0 !important;
        }

        /* Replace buttons with their labels */
        button[onclick="toggleDropdown(this)"] {
            visibility: hidden;
            position: relative;
            padding: 4px 0 !important;
            margin: 0 !important;
            background-color: transparent !important;
            line-height: 1.2 !important;
        }

        button[onclick="toggleDropdown(this)"]::after {
            content: attr(data-label);
            visibility: visible;
            position: absolute;
            left: 0;
            top: 0;
            font-weight: bold;
            background-color: #ffffff !important;
            line-height: 1.2 !important;
            padding: 4px 0 !important;
        }

        /* Main table styling */
        .financial-table {
            page-break-inside: auto !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }

        /* Account rows */
        .account-row {
            page-break-inside: avoid !important;
            display: flex !important;
            justify-content: space-between !important;
            visibility: visible !important;
            padding: 2px 0 !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }

        .account-row span {
            padding: 0 !important;
            line-height: 1.2 !important;
            margin: 0 !important;
        }

        /* Font size for print */
        body, th, td, .account-row span {
            font-size: 12px !important;
        }
        
        /* Ensure all content is visible */
        * {
            overflow: visible !important;
        }
        
        /* Remove excess spacing */
        br {
            display: none !important;
        }
        
        div[style*="border-top"] {
            margin-top: 2px !important;
            margin-bottom: 2px !important;
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
    }
</style>

@section('content')

@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <section class="content" style="background-color: #f7f8fa; padding: 20px;">
        <div class="filters-container no-print">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_range_filter">@lang('report.date_range')</label>
                        <input type="text" class="form-control" id="date_range_filter" name="date_range_filter"
                            value="{{ $start_date }} ~ {{ $end_date }}">
                    </div>
                </div>
                <button id="printButton" class="btn btn-primary " style="margin-top:24px ">
                    <i class="fas fa-print"></i> Print
                </button>
            @endcomponent
        </div>
        <div class="col-md-12" style="background-color: #ffffff; padding: 0; margin-top: 15px;">
            <div class="box" style="background-color: #ffffff; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 15px;">
                <div class="box-header with-border text-center" style="padding-bottom: 10px;">
                    <h4 class="box-title report-title">{{ Session::get('business.name') }}</h4>
                    <h5 class="box-title report-subtitle"><b>@lang('accounting::lang.balance_sheet')</b></h5>
                    <p class="report-date">As of {{ @format_date($start_date) }} ~ {{ @format_date($end_date) }}</p>
                    <p class="report-date" style="margin-left:80%; font-weight: bold;">Exchange Rate: 1 USD = {{ number_format($khr_rate, 0) }} KHR</p>
                </div>

                <div class="table-container">
                    <table class="financial-table"
                        style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                        <thead style="background-color: #ffffff;">
                            <tr>
                                <th
                                    style="width: 60%; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align:left; padding: 10px;">
                                    Account Name
                                </th>
                                <th
                                    style="width: 20%; border-top: 1px solid #000; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align:right; padding: 10px;">
                                    USD
                                </th>
                                <th
                                    style="width: 20%; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align:right; padding: 10px;">
                                    KHR
                                </th>
                            </tr>
                        </thead>

                        <tbody id="balance-sheet-body" style="background-color: #ffffff;">

                            @foreach ($account_types as $type => $details)
                            @php
                                $primary_type_total_balance = $accounts
                                    ->whereIn(
                                        'account_sub_type_id',
                                        $account_sub_types->where('account_primary_type', $type)->pluck('id')
                                    )
                                    ->sum('balance');
                                
                                // Calculate KHR values using exchange rate from controller
                                $primary_type_total_khr = $primary_type_total_balance * $khr_rate;
                            @endphp
                        
                            @if (in_array($type, ['expenses', 'liability', 'equity', 'income', 'asset']))
                                <tr style="background-color: #ffffff;">
                                    <td colspan="3" style="padding: 0;">
                                        <!-- Primary Dropdown Button -->
                                        <button onclick="toggleDropdown(this)" data-label="{{ $details['label'] }}"
                                            style="cursor: pointer; padding: 8px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff; font-size: 18px; margin: 0;">
                                            &#9654; {{ $details['label'] }} <!-- Default arrow: right -->
                                        </button>
                        
                                        <!-- Primary Dropdown Content -->
                                        <div class="dropdown-content" style="display: none; margin: 0; padding-left: 15px;">
                                            @foreach ($account_sub_types->where('account_primary_type', $type)->all() as $sub_type)
                                                @php
                                                    $subtype_accounts = $accounts
                                                        ->where('account_sub_type_id', $sub_type->id)
                                                        ->sortBy('name');
                                                    $subtype_accounts_grouped = $subtype_accounts->groupBy('detail_type.id');
                                                    $total_balance = $subtype_accounts->sum('balance');
                                                    $total_balance_khr = $total_balance * $khr_rate;
                                                @endphp
                        
                                                <div style="padding-left: 15px; margin: 0;">
                                                    <!-- Sub-type Dropdown Button -->
                                                    <button onclick="toggleDropdown(this)"
                                                        data-label="{{ $sub_type->name }}"
                                                        style="cursor: pointer; padding: 6px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff; font-size: 16px; margin: 0;">
                                                        &#9654; {{ $sub_type->name }} <!-- Default arrow: right -->
                                                    </button>
                        
                                                    <!-- Sub-type Dropdown Content -->
                                                    <div class="dropdown-content" style="display: none; margin: 0; padding-left: 15px;">
                                                        @foreach ($subtype_accounts_grouped as $detail_type_id => $accounts_in_detail)
                                                            @php
                                                                $detail_type = $account_detail_types->firstWhere('id', $detail_type_id);
                                                                if (!$detail_type) {
                                                                    continue;
                                                                }
                                                                $detail_total = $accounts_in_detail->sum('balance');
                                                                $detail_total_khr = $detail_total * $khr_rate;
                                                            @endphp
                        
                                                            <div style="padding-left: 15px; margin: 0;">
                                                                <!-- Detail-type Dropdown Button -->
                                                                <button onclick="toggleDropdown(this)"
                                                                    data-label="{{ $detail_type->name }}"
                                                                    style="cursor: pointer; padding: 6px; text-align: left; border: none; outline: none; width: 100%; font-weight: light; background-color: #ffffff; font-size: 15px; margin: 0;">
                                                                    &#9654; {{ $detail_type->name }} <!-- Default arrow: right -->
                                                                </button>
                        
                                                                <!-- Detail-type Dropdown Content -->
                                                                <div class="dropdown-content" style="display: none; margin: 0; padding-left: 15px;">
                                                                    @foreach ($accounts_in_detail as $account)
                                                                        @php
                                                                            $account_balance_khr = $account->balance * $khr_rate;
                                                                        @endphp
                                                                        <div class="account-row"
                                                                            data-balance="{{ $account->balance }}"
                                                                            style="padding-left: 15px; display: flex; justify-content: space-between; background-color: #ffffff; margin: 0; padding-top: 2px; padding-bottom: 2px;">
                                                                            <span style="flex: 3; font-size: 15px;">{{ $account->account_number }}&nbsp;&nbsp;&nbsp;{{ $account->name }}</span>
                                                                            <a href="{{ route('accounting.ledger', $account->id) }}"
                                                                                style="text-decoration: none; color: inherit; flex: 1; text-align: right; font-size: 15px;">
                                                                                <span>@format_currency($account->balance)</span>
                                                                            </a>
                                                                            <span style="flex: 1; text-align: right; font-size: 15px;">{{ number_format($account_balance_khr, 0) }}</span>
                                                                        </div>
                                                                    @endforeach
                        
                                                                    <!-- Detail-type Total -->
                                                                    <div
                                                                        style="border-top: 1px solid #000; padding: 4px 15px; font-weight: light; display: flex; justify-content: space-between; background-color: #ffffff; margin-top: 4px; margin-bottom: 4px;">
                                                                        <span style="flex: 3; font-size: 15px;">Total for {{ $detail_type->name }}</span>
                                                                        <span style="flex: 1; text-align: right; font-size: 15px;">@format_currency($detail_total)</span>
                                                                        <span style="flex: 1; text-align: right; font-size: 15px;">{{ number_format($detail_total_khr, 0) }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                        
                                                        <!-- Sub-type Total -->
                                                        <div
                                                            style="border-top: 1px solid #000; padding: 6px 15px; font-weight: bold; display: flex; justify-content: space-between; background-color: #f8f9fa; margin-top: 4px; margin-bottom: 4px;">
                                                            <span style="flex: 3; font-size: 16px;">Total for {{ $sub_type->name }}</span>
                                                            <span style="flex: 1; text-align: right; font-size: 16px;">@format_currency($total_balance)</span>
                                                            <span style="flex: 1; text-align: right; font-size: 16px;">{{ number_format($total_balance_khr, 0) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                        
                                        <!-- Primary Type Total -->
                                        <div
                                            style="border-top: 2px solid #000; padding: 8px 15px; font-weight: bold; display: flex; justify-content: space-between; background-color: #f8f9fa; margin-top: 4px; margin-bottom: 4px;">
                                            <span style="flex: 3; font-size: 18px;">Total for {{ $details['label'] }}</span>
                                            <span style="flex: 1; text-align: right; font-size: 18px;">@format_currency($primary_type_total_balance)</span>
                                            <span style="flex: 1; text-align: right; font-size: 18px;">{{ number_format($primary_type_total_khr, 0) }}</span>
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
        // Print function
        function printReport() {
            // Create a clone of the report to print
            const reportSection = document.querySelector('.box');
            const printWindow = window.open('', '_blank', 'width=1000,height=800,scrollbars=yes');
            
            if (!printWindow) {
                alert("Please allow popup windows to print the report.");
                return;
            }
            
            // Get content for the print window
            const reportTitle = document.querySelector('.report-title').innerText;
            const reportSubtitle = document.querySelector('.report-subtitle').innerText;
            const reportDates = document.querySelectorAll('.report-date');
            
            // Clone the report content and expand all dropdowns
            const contentClone = reportSection.cloneNode(true);
            
            // Process the clone to ensure all dropdowns are expanded
            const dropdowns = contentClone.querySelectorAll('.dropdown-content');
            dropdowns.forEach(dropdown => {
                dropdown.style.display = 'block';
                dropdown.style.visibility = 'visible';
                dropdown.style.height = 'auto';
                dropdown.style.opacity = '1';
            });
            
            // Hide toggle buttons in the clone
            const toggleButtons = contentClone.querySelectorAll('button[onclick]');
            toggleButtons.forEach(button => {
                button.style.display = 'none';
            });
            
            // Remove BR tags in the clone
            const brTags = contentClone.querySelectorAll('br');
            brTags.forEach(br => {
                if (br.parentNode) br.parentNode.removeChild(br);
            });
            
            // Create simplified HTML without template literals
            printWindow.document.open();
            printWindow.document.write('<!DOCTYPE html>');
            printWindow.document.write('<html>');
            printWindow.document.write('<head>');
            printWindow.document.write('<meta charset="UTF-8">');
            printWindow.document.write('<title>Statement of Financial Position</title>');
            
            // Write CSS styles directly
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 15px; }');
            printWindow.document.write('.report-header { text-align: center; margin-bottom: 15px; }');
            printWindow.document.write('.report-title { font-size: 24px; font-weight: bold; margin: 0 0 5px 0; }');
            printWindow.document.write('.report-subtitle { font-size: 20px; font-weight: bold; margin: 0 0 5px 0; }');
            printWindow.document.write('.report-date { font-size: 16px; margin: 0 0 5px 0; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 15px 0; }');
            printWindow.document.write('th, td { border: 1px solid #000; padding: 8px; text-align: left; }');
            printWindow.document.write('th { background-color: #f2f2f2; }');
            printWindow.document.write('.dropdown-content { display: block !important; visibility: visible !important; padding-left: 15px !important; }');
            printWindow.document.write('.account-row { display: flex !important; justify-content: space-between !important; padding: 2px 0; margin: 0; }');
            printWindow.document.write('.account-row span:first-child { flex: 3; text-align: left; }');
            printWindow.document.write('.account-row span { flex: 1; text-align: right; }');
            printWindow.document.write('button[onclick] { display: none !important; }');
            printWindow.document.write('br { display: none !important; }');
            printWindow.document.write('div[style*="border-top"] { border-top: 1px solid #000 !important; padding: 3px 5px !important; margin: 2px 0 !important; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head>');
            printWindow.document.write('<body>');
            
            // Add report header
            printWindow.document.write('<div class="report-header">');
            printWindow.document.write('<h1 class="report-title">' + reportTitle + '</h1>');
            printWindow.document.write('<h2 class="report-subtitle">' + reportSubtitle + '</h2>');
            reportDates.forEach(date => {
                printWindow.document.write('<p class="report-date">' + date.innerText + '</p>');
            });
            printWindow.document.write('</div>');
            
            // Add the table
            const tableElement = contentClone.querySelector('.financial-table');
            if (tableElement) {
                printWindow.document.write('<div class="table-container">');
                printWindow.document.write(tableElement.outerHTML);
                printWindow.document.write('</div>');
            } else {
                printWindow.document.write(contentClone.innerHTML);
            }
            
            printWindow.document.write('</body>');
            printWindow.document.write('</html>');
            printWindow.document.close();
            
            // Wait a moment for the content to load, then print
            setTimeout(function() {
                printWindow.print();
            }, 500);
        }
        
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
            
            // Attach the print function to the print button
            document.getElementById('printButton')?.addEventListener('click', function() {
                printReport();
            });
        });

        // Toggle dropdown function
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
    </script>

@endsection