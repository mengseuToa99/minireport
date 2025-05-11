@extends('layouts.app')

@section('title', __('accounting::lang.profit_loss'))

<style>
    /* Screen styles */
    .report-header {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .report-title {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
    }
    
    .report-subtitle {
        font-size: 18px;
        font-weight: bold;
        margin: 10px 0 5px 0;
    }
    
    .report-date {
        font-size: 14px;
        margin: 5px 0;
    }
    
    .financial-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .financial-table th, .financial-table td {
        border: 1px solid #000;
        padding: 8px;
    }
    
    .financial-table th {
        text-align: left;
        background-color: #f8f9fa;
    }
    
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    
    .dropdown-content {
        padding-left: 20px;
        display: none; /* Ensure initial hidden state */
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
            margin-bottom: 10mm !important;
            text-align: center !important;
        }

        /* Exchange Rate Table - preserve layout in print */
        table[style*="margin-left: 90px"] {
            margin-left: 0 !important;
            width: 100% !important;
            page-break-inside: avoid !important;
        }

        table[style*="margin-left: 90px"] th {
            border: none !important;
            background: none !important;
            font-size: 9px !important;
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
        }

        .account-row span {
            padding: 2px 0 !important;
        }

        /* Reduce font size for better fit */
        body, th, td, .account-row span {
            font-size: 10px !important;
        }
    }
</style>

@section('content')
    <section class="content" style="background-color: #f7f8fa;">
        <div class="filters-container no-print">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="date-picker-container row">
                    <!-- First Period Selectors -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>First Period</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select id="first_year" name="first_year" class="form-control">
                                        @for ($year = date('Y'); $year >= date('Y')-5; $year--)
                                            <option value="{{ $year }}" {{ $first_date->format('Y') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select id="first_month" name="first_month" class="form-control">
                                        @foreach (['01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April', 
                                                  '05'=>'May', '06'=>'June', '07'=>'July', '08'=>'August', 
                                                  '09'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'] as $num => $name)
                                            <option value="{{ $num }}" {{ $first_date->format('m') == $num ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Second Period Selectors -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Second Period</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select id="second_year" name="second_year" class="form-control">
                                        @for ($year = date('Y'); $year >= date('Y')-5; $year--)
                                            <option value="{{ $year }}" {{ $second_date->format('Y') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select id="second_month" name="second_month" class="form-control">
                                        @foreach (['01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April', 
                                                  '05'=>'May', '06'=>'June', '07'=>'July', '08'=>'August', 
                                                  '09'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'] as $num => $name)
                                            <option value="{{ $num }}" {{ $second_date->format('m') == $num ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="col-md-2" style="margin-top: 25px;">
                        <button id="updateDates" class="btn btn-primary" style="margin-right: 5px;">
                            <i class="fas fa-sync"></i> Update
                        </button>
                        <button id="printButton" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            @endcomponent
        </div>

        <div class="col-md-10 col-md-offset-1" style="background-color: #ffffff;">
            <div class="box" style="background-color: #ffffff;">
                <div class="box-header with-border text-center">
                    <h4 class="box-title report-title" style="font-size: 24px;">{{ Session::get('business.name') }}</h4>
                    <br><br>
                    <h5 class="box-title report-subtitle"><b>@lang('accounting::lang.profit_loss_comparison')</b></h5>
                    <p class="report-date">Comparing {{ $first_date->format('F Y') }} vs {{ $second_date->format('F Y') }}</p>
                    <p class="report-date" style="margin-left:80%; font-weight: bold;">Exchange Rate: 1 USD = {{ number_format($khr_rate ?? 4100, 0) }} KHR</p>
                </div>

                <div class="table-container">
                    <!-- Main Financial Table -->
                    <table class="financial-table">
                        <thead>
                            <tr>
                                <th style="width: 30%; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align:left;">
                                    Account Name
                                </th>
                                @foreach ($comparison_periods as $period)
                                    <th style="width: 17.5%; border-top: 1px solid #000; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align:right;">
                                        {{ $period['name'] }} (USD)
                                    </th>
                                    <th style="width: 17.5%; border-top: 1px solid #000; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align:right;">
                                        {{ $period['name'] }} (KHR)
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="comparison-body">
                            @foreach ($account_types as $type => $details)
                                <tr>
                                    <td colspan="{{ count($comparison_periods) * 2 + 1 }}">
                                        <button onclick="toggleDropdown(this)" 
                                                data-label="{{ $details['label'] }}"
                                                data-type="{{ $type }}"
                                                style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff;">
                                            &#9654; {{ $details['label'] }}
                                        </button>

                                        <div class="dropdown-content" style="display: none;">
                                            @foreach ($account_sub_types->where('account_primary_type', $type) as $sub_type)
                                                @php
                                                    $sub_type_id = $sub_type->id;
                                                @endphp
                                                <div style="padding-left: 20px;">
                                                    <button onclick="toggleDropdown(this)" 
                                                            data-label="{{ $sub_type->account_type_name }}"
                                                            style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff;">
                                                        &#9654; {{ $sub_type->account_type_name }}
                                                    </button>
                                                    <div class="dropdown-content" style="display: none;">
                                                        @if (isset($account_details[$type][$sub_type_id]))
                                                            @foreach ($account_details[$type][$sub_type_id]['detail_types'] as $detail_type_id => $detail_type)
                                                                <div style="padding-left: 20px;">
                                                                    <button onclick="toggleDropdown(this)"
                                                                        data-label="{{ $detail_type['name'] }}"
                                                                        style="cursor: pointer; padding: 10px; text-align: left; border: none; outline: none; width: 100%; font-weight: light; background-color: #ffffff;">
                                                                        &#9654; {{ $detail_type['name'] }}
                                                                    </button>
                                                                    <div class="dropdown-content" style="display: none;">
                                                                        @foreach ($detail_type['accounts'] as $account_id => $account)
                                                                            <div class="account-row"
                                                                                style="padding-left: 20px; display: flex; justify-content: space-between; background-color: #ffffff;">
                                                                                <span style="flex: 3;">{{ $account['account_number'] }}&nbsp;&nbsp;&nbsp;{{ $account['name'] }}</span>
                                                                                <div style="display: flex; min-width: 70%;">
                                                                                    @foreach ($comparison_periods as $period)
                                                                                        @php
                                                                                            $account_balance = $detailed_data[$period['name']][$type][$sub_type_id]['detail_types'][$detail_type_id]['accounts'][$account_id] ?? 0;
                                                                                            $account_balance_khr = $account_balance * ($khr_rate ?? 4100);
                                                                                        @endphp
                                                                                        <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                                                            @format_currency($account_balance)
                                                                                        </span>
                                                                                        <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                                                            {{ number_format($account_balance_khr, 0) }}
                                                                                        </span>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                        <!-- Detail-type Total -->
                                                                        <div style="border-top: 1px solid #000; padding-left: 10px; font-weight: light; display: flex; background-color: #ffffff;">
                                                                            <span style="flex: 3; text-align: left;">Total for {{ $detail_type['name'] }}</span>
                                                                            <div style="display: flex; min-width: 70%;">
                                                                                @foreach ($comparison_periods as $period)
                                                                                    @php
                                                                                        $detail_type_total = $detailed_data[$period['name']][$type][$sub_type_id]['detail_types'][$detail_type_id]['total'] ?? 0;
                                                                                        $detail_type_total_khr = $detail_type_total * ($khr_rate ?? 4100);
                                                                                    @endphp
                                                                                    <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                                                        @format_currency($detail_type_total)
                                                                                    </span>
                                                                                    <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                                                        {{ number_format($detail_type_total_khr, 0) }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        <!-- Sub-type Total -->
                                                        <div style="border-top: 1px solid #000; padding-left: 10px; font-weight: bold; display: flex; background-color: #ffffff;">
                                                            <span style="flex: 3; text-align: left;">Total for {{ $sub_type->account_type_name }}</span>
                                                            <div style="display: flex; min-width: 70%;">
                                                                @foreach ($comparison_periods as $period)
                                                                    @php
                                                                        $subtype_total = $detailed_data[$period['name']][$type][$sub_type_id]['total'] ?? 0;
                                                                        $subtype_total_khr = $subtype_total * ($khr_rate ?? 4100);
                                                                    @endphp
                                                                    <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                                        @format_currency($subtype_total)
                                                                    </span>
                                                                    <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                                        {{ number_format($subtype_total_khr, 0) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <!-- Primary Type Total -->
                                            <div style="border-top: 1px solid #000; padding-left: 10px; font-weight: bold; display: flex; background-color: #ffffff;">
                                                <span style="flex: 3; text-align: left;">Total for {{ $details['label'] }}</span>
                                                <div style="display: flex; min-width: 70%;">
                                                    @foreach ($comparison_periods as $period)
                                                        @php
                                                            $primary_total = $data[$period['name']][$type];
                                                            $primary_total_khr = $primary_total * ($khr_rate ?? 4100);
                                                        @endphp
                                                        <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                            @format_currency($primary_total)
                                                        </span>
                                                        <span style="flex: 1; text-align: right; padding-right: 10px;">
                                                            {{ number_format($primary_total_khr, 0) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Net Profit/Loss Row -->
                            <tr>
                                <td colspan="{{ count($comparison_periods) * 2 + 1 }}">
                                    <div style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 10px; font-weight: bold; display: flex; background-color: #ffffff;">
                                        <span style="flex: 3; text-align: left;">Net Profit/Loss</span>
                                        <div style="display: flex; min-width: 70%;">
                                            @foreach ($comparison_periods as $period)
                                                @php
                                                    $net_profit = $data[$period['name']]['income'] - $data[$period['name']]['expenses'];
                                                    $net_profit_khr = $net_profit * ($khr_rate ?? 4100);
                                                @endphp
                                                <span style="flex: 1; text-align: right; padding-right: 10px; font-weight: bold;">
                                                    @format_currency($net_profit)
                                                </span>
                                                <span style="flex: 1; text-align: right; padding-right: 10px; font-weight: bold;">
                                                    {{ number_format($net_profit_khr, 0) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        // Print function matching Statement_of_Financial_Position.blade.php
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
            printWindow.document.write('<title>Income Statement Comparison</title>');
            
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
            // Update button handler for year and month dropdowns
            $('#updateDates').on('click', function() {
                // Get selected values from dropdowns
                const firstYear = $('#first_year').val();
                const firstMonth = $('#first_month').val();
                const secondYear = $('#second_year').val();
                const secondMonth = $('#second_month').val();
                
                // Format dates for the API call (YYYY-MM-DD)
                const firstDate = `${firstYear}-${firstMonth}-01`;
                const secondDate = `${secondYear}-${secondMonth}-01`;
                
                // Update URL parameters and reload
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('first_date', firstDate);
                urlParams.set('second_date', secondDate);
                window.location.search = urlParams;
            });

            // Add event listeners for dropdowns to enable "Enter" key submission
            $('#first_year, #first_month, #second_year, #second_month').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    $('#updateDates').click();
                }
            });

            // Handle print button click
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
