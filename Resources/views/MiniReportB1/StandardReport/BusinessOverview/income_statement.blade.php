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
        table-layout: fixed;
        border-collapse: collapse;
        font-size: 11px;
    }
    
    .financial-table th, .financial-table td {
        border: 1px solid #000;
        padding: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .financial-table th {
        text-align: center;
        background-color: #f8f9fa;
        font-size: 10px;
        font-weight: bold;
    }
    
    .table-container {
        width: 100%;
        overflow-x: auto;
        padding: 0 15px;
    }
    
    .dropdown-content {
        padding-left: 20px;
        display: none; /* Ensure initial hidden state */
    }
    
    .account-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2px 0;
        font-size: 11px;
    }
    
    .account-row span {
        flex: 1;
        text-align: right;
        padding-right: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .account-row span:first-child {
        text-align: left;
        flex: 2;
        min-width: 220px;
        padding-right: 10px;
    }

    .month-value {
        min-width: 60px;
        text-align: right !important;
        font-size: 10px;
    }
    
    .month-column-pair {
        width: 100%;
        display: flex;
    }
    
    .month-header {
        border-bottom: 1px solid #ddd;
    }
    
    .month-cell {
        width: 50%;
        text-align: center;
    }
    
    .currency-cell {
        font-size: 9px;
        text-align: center;
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
        body, .account-row span {
            font-size: 9px !important;
        }
        
        th, td {
            font-size: 8px !important;
        }
    }
</style>

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

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

        <br><br>
        <!-- Report container -->
        <div class="col-md-12" style="background-color: #ffffff;">
            <div class="box" style="background-color: #ffffff;">
                <div class="box-header with-border text-center">
                    <h4 class="box-title report-title">{{ Session::get('business.name') }}</h4>
                    <br><br>
                    <h5 class="box-title report-subtitle"><b>@lang('accounting::lang.profit_loss')</b></h5>
                    <p class="report-date">@lang('minireportb1::minireportb1.for_the_period') {{ $year }}</p>
                    <p class="report-date" style="margin-left:80%; font-weight: bold;">Exchange Rates (KHR) by Month</p>
                </div>

                <div class="table-container">
                    <!-- Exchange Rate Table -->
                    <table style="width: 98%; table-layout: fixed; border: 8px; margin-left: 20px; margin-bottom: 20px;">
                        <tr>
                            @foreach ($months as $month)
                                <th style="text-align: center; font-weight: bold; font-size: 10px; width: calc(100% / {{ count($months) }});">
                                    {{ $month['name'] }}<br>{{ $month['rate'] }}
                                </th>
                            @endforeach
                        </tr>
                    </table>
                    
                    <!-- Main Financial Table - Rebuilt with aligned columns -->
                    <table class="table table-bordered table-striped" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                        <thead>
                            <tr>
                                <th style="width: 220px; text-align: left; border: 1px solid #000; padding: 5px;">Account Name</th>
                                @foreach ($months as $month)
                                    <th style="text-align: center; border: 1px solid #000; padding: 2px 1px;">
                                        <div style="margin-bottom: 2px;">{{ substr($month['name'], 0, 3) }}</div>
                                        <div style="display: flex; font-size: 9px; font-weight: normal;">
                                            <div style="width: 50%; text-align: center; border-right: 1px dotted #ccc;">USD</div>
                                            <div style="width: 50%; text-align: center;">KHR</div>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($account_types as $type => $details)
                                @if (in_array($type, ['expenses', 'income']))
                                    <tr>
                                        <td colspan="{{ count($months) + 1 }}" style="padding: 0;">
                                            <!-- Primary Dropdown Button -->
                                            <button onclick="toggleDropdown(this)" data-label="{{ $details['label'] }}"
                                                style="cursor: pointer; padding: 8px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff; font-size: 12px;">
                                                &#9654; {{ $details['label'] }}
                                            </button>
                        
                                            <!-- Primary Dropdown Content -->
                                            <div class="dropdown-content" style="display: none;">
                                                @foreach ($account_sub_types->where('account_primary_type', $type)->all() as $sub_type)
                                                    @php
                                                        $subtype_accounts = $accounts
                                                            ->where('account_sub_type_id', $sub_type->id)
                                                            ->sortBy('name');
                                                        $subtype_accounts_grouped = $subtype_accounts->groupBy('detail_type.id');
                                                    @endphp
                        
                                                    <div style="padding-left: 20px;">
                                                        <!-- Sub-type Dropdown Button -->
                                                        <button onclick="toggleDropdown(this)"
                                                            data-label="{{ $sub_type->name }}"
                                                            style="cursor: pointer; padding: 8px; text-align: left; border: none; outline: none; width: 100%; font-weight: bold; background-color: #ffffff; font-size: 12px;">
                                                            &#9654; {{ $sub_type->name }}
                                                        </button>
                        
                                                        <!-- Sub-type Dropdown Content -->
                                                        <div class="dropdown-content" style="display: none;">
                                                            @foreach ($subtype_accounts_grouped as $detail_type_id => $accounts_in_detail)
                                                                @php
                                                                    $detail_type = $account_detail_types->firstWhere('id', $detail_type_id);
                                                                    if (!$detail_type) {
                                                                        continue;
                                                                    }
                                                                @endphp
                        
                                                                <div style="padding-left: 20px;">
                                                                    <!-- Detail-type Dropdown Button -->
                                                                    <button onclick="toggleDropdown(this)"
                                                                        data-label="{{ $detail_type->name }}"
                                                                        style="cursor: pointer; padding: 6px; text-align: left; border: none; outline: none; width: 100%; font-weight: normal; background-color: #ffffff; font-size: 11px;">
                                                                        &#9654; {{ $detail_type->name }}
                                                                    </button>
                        
                                                                    <!-- Detail-type Dropdown Content -->
                                                                    <div class="dropdown-content" style="display: none;">
                                                                        @foreach ($accounts_in_detail as $account)
                                                                            <div style="display: flex; padding: 3px 0 3px 20px; font-size: 11px;">
                                                                                <div style="width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: left;">
                                                                                    {{ $account->account_number }}&nbsp;&nbsp;{{ $account->name }}
                                                                                </div>
                                                                                @foreach ($account->monthly_balances as $index => $balance)
                                                                                    <div style="flex: 1; display: flex; min-width: 60px;">
                                                                                        <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                                            @format_currency($balance)
                                                                                        </div>
                                                                                        <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                                            {{ number_format($balance * ($months[$index]['rate'] != 'N/A' ? $months[$index]['rate'] : 4100), 0) }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endforeach
                        
                                                                        <!-- Detail-type Total -->
                                                                        <div style="display: flex; padding: 5px 0 5px 20px; border-top: 1px solid #ddd; font-weight: bold; font-size: 11px;">
                                                                            <div style="width: 220px; text-align: left;">
                                                                                Total for {{ $detail_type->name }}
                                                                            </div>
                                                                            @for ($i = 0; $i < 12; $i++)
                                                                                @php
                                                                                    $monthly_total = $accounts_in_detail->sum(
                                                                                        function ($account) use ($i) {
                                                                                            return $account->monthly_balances[$i] ?? 0;
                                                                                        },
                                                                                    );
                                                                                    $monthly_total_khr = $monthly_total * ($months[$i]['rate'] != 'N/A' ? $months[$i]['rate'] : 4100);
                                                                                @endphp
                                                                                <div style="flex: 1; display: flex; min-width: 60px;">
                                                                                    <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                                        @format_currency($monthly_total)
                                                                                    </div>
                                                                                    <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                                        {{ number_format($monthly_total_khr, 0) }}
                                                                                    </div>
                                                                                </div>
                                                                            @endfor
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                        
                                                            <!-- Sub-type Total -->
                                                            <div style="display: flex; padding: 5px 0 5px 20px; border-top: 1px solid #000; font-weight: bold; font-size: 11px;">
                                                                <div style="width: 220px; text-align: left;">
                                                                    Total for {{ $sub_type->name }}
                                                                </div>
                                                                @for ($i = 0; $i < 12; $i++)
                                                                    @php
                                                                        $monthly_total = $subtype_accounts->sum(
                                                                            function ($account) use ($i) {
                                                                                return $account->monthly_balances[$i] ?? 0;
                                                                            },
                                                                        );
                                                                        $monthly_total_khr = $monthly_total * ($months[$i]['rate'] != 'N/A' ? $months[$i]['rate'] : 4100);
                                                                    @endphp
                                                                    <div style="flex: 1; display: flex; min-width: 60px;">
                                                                        <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                            @format_currency($monthly_total)
                                                                        </div>
                                                                        <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                            {{ number_format($monthly_total_khr, 0) }}
                                                                        </div>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                        
                                                <!-- Primary Type Total -->
                                                <div style="display: flex; padding: 8px 0 8px 20px; border-top: 1px solid #000; font-weight: bold; font-size: 11px;">
                                                    <div style="width: 220px; text-align: left;">
                                                        Total for {{ $details['label'] }}
                                                    </div>
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
                                                            $monthly_total_khr = $monthly_total * ($months[$i]['rate'] != 'N/A' ? $months[$i]['rate'] : 4100);
                                                        @endphp
                                                        <div style="flex: 1; display: flex; min-width: 60px;">
                                                            <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                @format_currency($monthly_total)
                                                            </div>
                                                            <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 10px;">
                                                                {{ number_format($monthly_total_khr, 0) }}
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            <!-- Net Profit/Loss Row -->
                            <tr>
                                <td colspan="{{ count($months) + 1 }}" style="padding: 0;">
                                    <div style="display: flex; padding: 10px 0 10px 0; border-top: 2px solid #000; border-bottom: 2px solid #000; font-weight: bold; font-size: 12px;">
                                        <div style="width: 220px; text-align: left;">
                                            Net Profit/Loss
                                        </div>
                                        @for ($i = 0; $i < 12; $i++)
                                            @php
                                                $income_total = $accounts
                                                    ->whereIn(
                                                        'account_sub_type_id',
                                                        $account_sub_types
                                                            ->where('account_primary_type', 'income')
                                                            ->pluck('id'),
                                                    )
                                                    ->sum(function ($account) use ($i) {
                                                        return $account->monthly_balances[$i] ?? 0;
                                                    });
                                                
                                                $expense_total = $accounts
                                                    ->whereIn(
                                                        'account_sub_type_id',
                                                        $account_sub_types
                                                            ->where('account_primary_type', 'expenses')
                                                            ->pluck('id'),
                                                    )
                                                    ->sum(function ($account) use ($i) {
                                                        return $account->monthly_balances[$i] ?? 0;
                                                    });
                                                
                                                $net_profit = $income_total - $expense_total;
                                                $net_profit_khr = $net_profit * ($months[$i]['rate'] != 'N/A' ? $months[$i]['rate'] : 4100);
                                            @endphp
                                            <div style="flex: 1; display: flex; min-width: 60px;">
                                                <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 11px;">
                                                    @format_currency($net_profit)
                                                </div>
                                                <div style="width: 50%; text-align: right; padding-right: 5px; font-size: 11px;">
                                                    {{ number_format($net_profit_khr, 0) }}
                                                </div>
                                            </div>
                                        @endfor
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
            printWindow.document.write('<title>Income Statement</title>');
            
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
            printWindow.document.write('.account-row span:first-child { flex: 2; text-align: left; }');
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
            // Year filter change handler
            $('#year_filter').on('change', function() {
                const selectedYear = $(this).val();
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('year', selectedYear);
                window.location.search = urlParams;
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
