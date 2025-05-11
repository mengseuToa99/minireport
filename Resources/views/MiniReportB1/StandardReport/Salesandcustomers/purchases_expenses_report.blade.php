@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.purchases_expenses_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

<style>
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
        
        /* Hide the checkbox column when printing */
        th:first-child, td:first-child {
            display: none !important;
        }
        
        /* Remove borders and adjust spacing for better print layout */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        
        /* Important: Keep the hide-row class hidden even when printing */
        .hide-row {
            display: none !important;
        }
        
        /* Convert links to text in print view */
        a.transaction-link {
            text-decoration: none;
            color: inherit !important;
        }
    }
    
    /* Hide this class for both screen and print */
    .hide-row {
        display: none !important;
    }
    
    /* Style for the Apply Selection button */
    .apply-selection-btn {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        margin-left: 10px;
        cursor: pointer;
    }
    
    .apply-selection-btn:hover {
        background-color: #218838;
    }
    
    .reset-selection-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        margin-left: 10px;
        cursor: pointer;
    }
    
    .reset-selection-btn:hover {
        background-color: #c82333;
    }
    
    .selection-applied {
        background-color: #f8f9fa;
        padding: 10px;
        margin-top: 10px;
        border-radius: 4px;
        text-align: center;
    }
    
    /* Style for transaction links */
    a.transaction-link {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }
    
    a.transaction-link:hover {
        text-decoration: underline;
        color: #0056b3;
    }
</style>

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    {{-- Add meta tags for business information --}}
    <meta name="business-name" content="{{ $business_name ?? '' }}">
    @if(isset($business_logo) && !empty($business_logo))
        <meta name="business-logo" content="{{ url('/uploads/business_logos/' . $business_logo) }}">
    @else
        <meta name="business-logo" content="">
    @endif

    <div style="margin: 16px" class="no-print">

        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">

                <form id="filterForm">
                    <div class="form-group mt-3">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                    <div class="form-group mt-3">
                        {!! Form::label('supplier_id', __('purchase.supplier') . ':') !!}
                        {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                    <div class="form-group mt-3">
                        {!! Form::label('type', __('minireportb1::minireportb1.transaction_type') . ':') !!}
                        {!! Form::select('type', [
                            '' => __('minireportb1::minireportb1.all_types'),
                            'purchase' => __('minireportb1::minireportb1.transaction_type_purchase'),
                            'expense' => __('minireportb1::minireportb1.transaction_type_expense')
                        ], null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                    </div>
                    @include('minireportb1::MiniReportB1.components.filterdate')
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent

    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.expense_purchase_report')])

    <!-- Test image (hidden from view) -->
    <div style="display: none;" class="logo-test">
        <h3>Business Logo Test:</h3>
        <div>
            Raw logo value: {{ $business_logo ?? 'Not available' }}
        </div>
        @php
            // Get the business directly from the model
            $business = \App\Business::where('id', session('user.business_id'))->first();
        @endphp
        
        @if($business && $business->logo)
            <img src="{{ asset('/uploads/business_logos/' . e($business->logo)) }}" alt="Business Logo Test" id="test-logo">
            <script>
                // Create a debug element to verify the test logo is working
                document.addEventListener('DOMContentLoaded', function() {
                    const testLogo = document.getElementById('test-logo');
                    if (testLogo) {
                        console.log('Test logo element exists with src:', testLogo.src);
                        
                        testLogo.onload = function() {
                            console.log('Test logo loaded successfully');
                        };
                        
                        testLogo.onerror = function() {
                            console.error('Test logo failed to load');
                        };
                    }
                });
            </script>
        @else
            <p>No business logo available</p>
        @endif
    </div>

    <div class="reusable-table-container">
        <!-- Row limit controls -->
        @include('minireportb1::MiniReportB1.components.pagination')

        <!-- Selection controls -->
        <div class="selection-controls no-print" style="margin-bottom: 10px;">
            <button id="select_all" class="btn btn-sm btn-outline-primary">
                {{ __('minireportb1::minireportb1.select_all') }}
            </button>
            <button id="select_none" class="btn btn-sm btn-outline-secondary">
                {{ __('minireportb1::minireportb1.select_none') }}
            </button>
            <span class="ml-2" id="selected_count">0 {{ __('minireportb1::minireportb1.select_rows') }}</span>
            <button id="apply_selection" class="apply-selection-btn" disabled>
                {{ __('Apply Selection') }}
            </button>
            <button id="reset_selection" class="reset-selection-btn" style="display: none;">
                {{ __('Reset Selection') }}
            </button>
        </div>
        
        <!-- Selection applied message -->
        <div id="selection_applied_message" class="selection-applied no-print" style="display: none;">
            {{ __('Selection applied. Only selected rows are shown.') }}
        </div>
        
        <table class="reusable-table wide-table sticky-first-col" id="purchases-expenses-table">
            <thead>
                <tr>
                    <th width="40px">
                        <input type="checkbox" id="check_all">
                    </th>
                    <th class="col-xs">{{ __('minireportb1::minireportb1.no') }}</th>
                    <th class="col-sm">{{ __('minireportb1::minireportb1.date') }}</th>
                    <th class="col-sm">{{ __('minireportb1::minireportb1.transaction_no') }}</th>
                    <th class="col-sm">{{ __('business.location') }}</th>
                    <th class="col-xs">{{ __('minireportb1::minireportb1.transaction_type') }}</th>
                    <th class="col-sm">{{ __('sale.amount') }}</th>
                    <th class="col-xs">{{ __('sale.status') }}</th>
                    <th class="col-sm">{{ __('purchase.supplier') }}</th>
                </tr>
            </thead>
            <tbody id="purchases-expenses-table-body">
                <!-- Data will be loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>{{ __('sale.total') }}:</strong></td>
                    <td id="total_amount" class="text-right"><strong>0</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#purchases-expenses-table";
        const reportname = "{{ __('minireportb1::minireportb1.purchases_expenses_report') }}";
        
        // Debug business logo
        console.log("Business Logo from Meta:", document.querySelector('meta[name="business-logo"]')?.content);
    </script>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
    <!-- JavaScript for AJAX and Filters -->
    <script>
        $(document).ready(function() {
            // Initialize jQuery UI Datepicker
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
            });

            // Pagination variables
            let currentPage = 1;
            let totalPages = 1;
            let rowLimit = 10;
            window.selectedRows = new Set();
            let originalTotalHtml = '';
            window.selectionApplied = false;
            
            // Override the print button functionality
            // We need to intercept the click before the original handler in printbutton.blade.php
            const originalPrintButton = document.getElementById('print-button');
            if (originalPrintButton) {
                const newPrintButton = originalPrintButton.cloneNode(true);
                originalPrintButton.parentNode.replaceChild(newPrintButton, originalPrintButton);
                
                newPrintButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Check if any rows are selected
                    if (window.selectedRows.size === 0) {
                        alert('{{ __("Please select at least one row to print") }}');
                        return false;
                    }
                    
                    // Apply selection if not already applied
                    if (!window.selectionApplied && window.selectedRows.size > 0) {
                        window.applySelection();
                    }
                    
                    // Create a clean print view with only selected rows
                    createCustomPrintView();
                });
            }
            
            // Function to create a custom print view with selected rows
            function createCustomPrintView() {
                // Get business name
                let businessName = '';
                const businessElements = [
                    document.querySelector('.report-title'),
                    document.querySelector('.normal-view-title:first-child'),
                    document.querySelector('.business-name'),
                    document.querySelector('meta[name="business-name"]')
                ];
                
                for (const element of businessElements) {
                    if (element && element.innerText) {
                        businessName = element.innerText.trim();
                        break;
                    } else if (element && element.content) {
                        businessName = element.content.trim();
                        break;
                    }
                }
                
                if (!businessName) {
                    businessName = '{{ $business_name ?? "Business Name" }}';
                }
                
                // Get business logo - retrieve directly from meta tag to avoid Blade template issues
                let businessLogo = '';
                
                // Method 1: Try to get from meta tag (our preferred method)
                const metaBusinessLogo = document.querySelector('meta[name="business-logo"]');
                if (metaBusinessLogo && metaBusinessLogo.content) {
                    businessLogo = metaBusinessLogo.content;
                    console.log("Logo from meta tag:", businessLogo);
                } 
                
                // Method 2: Try to find from the test image we added
                if (!businessLogo) {
                    const testLogoImg = document.querySelector('.logo-test img');
                    if (testLogoImg && testLogoImg.src) {
                        businessLogo = testLogoImg.src;
                        console.log("Logo from test image:", businessLogo);
                    }
                }
                
                // Method 3: Try to find any business logo in the page
                if (!businessLogo) {
                    const anyLogos = document.querySelectorAll('img[src*="business_logos"]');
                    if (anyLogos.length > 0) {
                        businessLogo = anyLogos[0].src;
                        console.log("Logo from page search:", businessLogo);
                    }
                }
                
                // Method 4: Get the URL from console.log that works
                if (!businessLogo || !businessLogo.includes('business_logos')) {
                    // Try using the known working path format
                    const hardcodedPath = '{{ url("/") }}' + '/uploads/business_logos/{{ $business_logo }}';
                    console.log("Using hardcoded path:", hardcodedPath);
                    businessLogo = hardcodedPath;
                }
                
                console.log("Final business logo URL:", businessLogo);
                
                // Fix relative URLs by converting them to absolute
                if (businessLogo && businessLogo.startsWith('/')) {
                    businessLogo = window.location.origin + businessLogo;
                }
                
                // Direct approach using the business ID - most reliable
                @php
                $business_id = session('user.business_id');
                $business = \App\Business::find($business_id);
                @endphp
                
                @if($business && $business->logo)
                const directLogoUrl = '{{ asset('/uploads/business_logos/' . e($business->logo)) }}';
                console.log("Direct logo URL from PHP:", directLogoUrl);
                businessLogo = directLogoUrl;
                @endif
                
                // Get report title
                let reportTitle = "{{ __('minireportb1::minireportb1.purchases_expenses_report') }}";
                
                // Get date range info
                let dateRangeText = '';
                const dateFilter = $('#date_filter').val() || '';
                const startDate = $('#start_date').val() || '';
                const endDate = $('#end_date').val() || '';
                
                if (startDate && endDate) {
                    dateRangeText = `Date Range: ${startDate} to ${endDate}`;
                } else if (dateFilter) {
                    // Create more descriptive date ranges for standard filters
                    const today = new Date();
                    let fromDate = new Date();
                    let toDate = new Date();
                    
                    switch(dateFilter) {
                        case 'today':
                            dateRangeText = `Today (${today.toLocaleDateString()})`;
                            break;
                        case 'yesterday':
                            fromDate.setDate(today.getDate() - 1);
                            dateRangeText = `Yesterday (${fromDate.toLocaleDateString()})`;
                            break;
                        case 'this_week':
                            fromDate.setDate(today.getDate() - today.getDay());
                            dateRangeText = `This Week (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                            break;
                        case 'this_month':
                            fromDate.setDate(1);
                            dateRangeText = `This Month (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                            break;
                        case 'last_month':
                            fromDate.setMonth(today.getMonth() - 1);
                            fromDate.setDate(1);
                            toDate.setDate(0); // Last day of previous month
                            dateRangeText = `Last Month (${fromDate.toLocaleDateString()} to ${toDate.toLocaleDateString()})`;
                            break;
                        case 'last_3_months':
                            fromDate.setMonth(today.getMonth() - 3);
                            fromDate.setDate(1);
                            dateRangeText = `Last 3 Months (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                            break;
                        case 'last_6_months':
                            fromDate.setMonth(today.getMonth() - 6);
                            fromDate.setDate(1);
                            dateRangeText = `Last 6 Months (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                            break;
                        case 'this_quarter':
                            fromDate.setMonth(Math.floor(today.getMonth() / 3) * 3);
                            fromDate.setDate(1);
                            dateRangeText = `This Quarter (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                            break;
                        case 'last_quarter':
                            fromDate.setMonth(Math.floor(today.getMonth() / 3) * 3 - 3);
                            fromDate.setDate(1);
                            toDate.setMonth(Math.floor(today.getMonth() / 3) * 3);
                            toDate.setDate(0);
                            dateRangeText = `Last Quarter (${fromDate.toLocaleDateString()} to ${toDate.toLocaleDateString()})`;
                            break;
                        case 'this_year':
                            fromDate.setMonth(0);
                            fromDate.setDate(1);
                            dateRangeText = `This Year (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                            break;
                        case 'last_year':
                            fromDate.setFullYear(today.getFullYear() - 1);
                            fromDate.setMonth(0);
                            fromDate.setDate(1);
                            toDate.setFullYear(today.getFullYear() - 1);
                            toDate.setMonth(11);
                            toDate.setDate(31);
                            dateRangeText = `Last Year (${fromDate.toLocaleDateString()} to ${toDate.toLocaleDateString()})`;
                            break;
                        default:
                            const filterMapping = {
                                'today': 'Today',
                                'this_month': 'This Month',
                                'last_month': 'Last Month',
                                'last_3_months': 'Last 3 Months',
                                'last_6_months': 'Last 6 Months',
                                'this_quarter': 'This Quarter',
                                'last_quarter': 'Last Quarter',
                                'this_year': 'This Year',
                                'last_year': 'Last Year'
                            };
                            dateRangeText = `Date Filter: ${filterMapping[dateFilter] || dateFilter.replace(/_/g, ' ')}`;
                    }
                }
                
                // Get location filter
                let locationText = '';
                const locationFilter = $('#location_id');
                if (locationFilter && locationFilter.val() && locationFilter.val() !== '') {
                    const selectedOption = locationFilter.find('option:selected');
                    if (selectedOption && selectedOption.text()) {
                        locationText = `<p>Location: ${selectedOption.text().trim()}</p>`;
                    }
                }
                
                // Get supplier filter
                let supplierText = '';
                const supplierFilter = $('#supplier_id');
                if (supplierFilter && supplierFilter.val() && supplierFilter.val() !== '') {
                    const selectedOption = supplierFilter.find('option:selected');
                    if (selectedOption && selectedOption.text()) {
                        supplierText = `<p>Supplier: ${selectedOption.text().trim()}</p>`;
                    }
                }
                
                // Get type filter
                let typeText = '';
                const typeFilter = $('#type');
                if (typeFilter && typeFilter.val() && typeFilter.val() !== '') {
                    const selectedOption = typeFilter.find('option:selected');
                    if (selectedOption && selectedOption.text()) {
                        typeText = `<p>Type: ${selectedOption.text().trim()}</p>`;
                    }
                }
                
                // Create a clone of the table with only selected rows
                const table = document.querySelector('#purchases-expenses-table');
                const tableClone = table.cloneNode(true);
                
                // Remove checkbox column
                const headerCheckboxCell = tableClone.querySelector('thead tr th:first-child');
                if (headerCheckboxCell) {
                    headerCheckboxCell.remove();
                }
                
                // Process each row in the tbody
                const tbody = tableClone.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    // Remove checkbox cell from each row
                    const checkboxCell = row.querySelector('td:first-child');
                    if (checkboxCell) {
                        checkboxCell.remove();
                    }
                    
                    // Keep only the selected rows
                    const rowId = row.getAttribute('data-id');
                    if (!window.selectedRows.has(rowId)) {
                        // This row is not selected, remove it
                        row.remove();
                    }
                });
                
                // Get total amount
                const totalAmount = $('#total_amount').html();
                console.log("Total amount for print view:", totalAmount);
                
                // Make sure the total is properly included in the print view
                let formattedTotal = totalAmount;
                if (totalAmount.includes('<strong>')) {
                    // Extract the value from the HTML
                    formattedTotal = $(totalAmount).text();
                }
                
                // For print view: Convert links to plain text 
                const links = tableClone.querySelectorAll('.transaction-link');
                links.forEach(link => {
                    const refNo = link.textContent.trim();
                    const span = document.createElement('span');
                    span.textContent = refNo;
                    span.className = 'print-ref-no';
                    link.parentNode.replaceChild(span, link);
                });
                
                // Create a new window with better styling
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>${reportTitle} - ${businessName}</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                margin: 20px; 
                                padding: 0; 
                                color: #333; 
                            }
                            .report-header {
                                margin-bottom: 20px;
                                display: flex;
                                flex-wrap: wrap;
                                justify-content: space-between;
                                align-items: center;
                                background-color: #f8f9fa;
                                padding: 15px;
                            }
                            .header-left {
                                display: flex;
                                align-items: center;
                                flex: 1;
                            }
                            .header-right {
                                flex: 1;
                                text-align: right;
                            }
                            .business-logo {
                                max-height: 50px;
                                max-width: 50px;
                                margin-right: 15px;
                            }
                            .business-name {
                                font-size: 20px;
                                font-weight: 600;
                            }
                            .report-name {
                                font-size: 22px;
                                font-weight: 600;
                                margin-bottom: 5px;
                            }
                            .date-range {
                                font-size: 14px;
                                margin-top: 5px;
                            }
                            .date-info {
                                text-align: center;
                                margin: 10px 0;
                                font-size: 14px;
                            }
                            .table-container {
                                width: 100%;
                                overflow-x: auto;
                                margin-bottom: 20px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                font-size: 12px;
                            }
                            table th {
                                background-color: #f8f9fa;
                                border: 1px solid #dee2e6;
                                padding: 8px;
                                text-align: left;
                                font-weight: bold;
                            }
                            table td {
                                border: 1px solid #dee2e6;
                                padding: 8px;
                                text-align: left;
                            }
                            table tr:nth-child(even) {
                                background-color: #f9f9f9;
                            }
                            .print-controls {
                                text-align: center;
                                margin: 20px 0;
                            }
                            .print-button-window {
                                display: block;
                                margin: 20px auto;
                                padding: 10px 20px;
                                background-color: #0f8800;
                                color: white;
                                border: none;
                                border-radius: 4px;
                                font-size: 14px;
                                cursor: pointer;
                            }
                            .top-controls {
                                text-align: right;
                                margin-bottom: 20px;
                            }
                            /* Hide rows that are not selected */
                            .hide-row {
                                display: none !important;
                            }
                            @media print {
                                .print-button-window, .top-controls {
                                    display: none;
                                }
                                body {
                                    margin: 0;
                                    padding: 0;
                                }
                                table th {
                                    background-color: #f8f9fa !important;
                                    color: #333 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                                table tr:nth-child(even) {
                                    background-color: #f9f9f9 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                                thead {
                                    display: table-header-group;
                                }
                                tr {
                                    page-break-inside: avoid;
                                }
                                .report-header {
                                    background-color: #f8f9fa !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="top-controls">
                            <button class="print-button-window" onclick="window.print()">Print Report</button>
                        </div>
                        
                        <div class="report-header">
                            <div class="header-left">
                                <img src="${businessLogo}" 
                                     class="business-logo" 
                                     onerror="console.error('Print view: Failed to load logo:', this.src); this.style.display='none';" 
                                     onload="console.log('Print view: Successfully loaded logo')"
                                     alt="Business Logo">
                                <div class="business-name">${businessName}</div>
                            </div>
                            <div class="header-right">
                                <div class="report-name">${reportTitle}</div>
                                <div class="date-range">${dateRangeText}</div>
                                <div class="date-range">Printed on${new Date().toLocaleString()}</div>
                            </div>
                        </div>
                        
                     
                        
                        <div class="table-container">
                            ${tableClone.outerHTML}
                        </div>
                        
                        <div class="total-row">
                            <p style="text-align: right; font-weight: bold; margin-right: 20px; font-size: 14px;">
                                <span style="display: inline-block; width: 100px; text-align: right;">Total:</span> 
                                <span style="display: inline-block; width: 150px; text-align: right; margin-right: 30px;">${formattedTotal}</span>
                            </p>
                        </div>
                        
                        <div class="print-controls">
                            <button class="print-button-window" onclick="window.print()">Print Report</button>
                        </div>
                    </body>
                    </html>
                `);
                printWindow.document.close();
                
                // Print after the window is loaded
                printWindow.onload = function() {
                    setTimeout(function() {
                        printWindow.print();
                    }, 500);
                };
            }
            
            // Function to update selected count display
            function updateSelectedCount() {
                $('#selected_count').text(window.selectedRows.size + ' {{ __("minireportb1::minireportb1.select_rows") }}');
                // Enable/disable apply selection button
                $('#apply_selection').prop('disabled', window.selectedRows.size === 0);
            }

            // Function to translate payment status
            function translatePaymentStatus(status) {
                switch(status.toLowerCase()) {
                    case 'due':
                        return '{{ __("minireportb1::minireportb1.payment_status_due") }}';
                    case 'paid':
                        return '{{ __("minireportb1::minireportb1.payment_status_paid") }}';
                    case 'partial':
                        return '{{ __("minireportb1::minireportb1.payment_status_partial") }}';
                    default:
                        return status;
                }
            }

            // Function to translate transaction type
            function translateTransactionType(type) {
                switch(type.toLowerCase()) {
                    case 'purchase':
                        return '{{ __("minireportb1::minireportb1.transaction_type_purchase") }}';
                    case 'expense':
                        return '{{ __("minireportb1::minireportb1.transaction_type_expense") }}';
                    default:
                        return type;
                }
            }

            // Function to update the total amount based on selected rows
            function updateSelectedTotal() {
                if (window.selectedRows.size === 0 || !window.selectionApplied) {
                    // No rows selected, show the original total
                    $('#total_amount').html(originalTotalHtml);
                    return;
                }
                
                let selectedTotal = 0;
                $('#purchases-expenses-table tbody tr').each(function() {
                    // Only consider visible rows (when selection is applied)
                    if (!$(this).hasClass('hide-row')) {
                        const amount = $(this).find('td:eq(7)').attr('data-amount');
                        if (amount) {
                            selectedTotal += parseFloat(amount);
                        }
                    }
                });
                
                $('#total_amount').html('<strong>' + __currency_trans_from_en(selectedTotal, false) + '</strong>');
            }

            // Function to apply selection
            window.applySelection = function() {
                if (window.selectedRows.size === 0) {
                    alert('{{ __("Please select at least one row to apply selection") }}');
                    return;
                }
                
                // Hide unselected rows
                $('#purchases-expenses-table tbody tr').each(function() {
                    const rowId = $(this).attr('data-id');
                    if (!window.selectedRows.has(rowId)) {
                        $(this).addClass('hide-row');
                    } else {
                        $(this).removeClass('hide-row'); // Ensure selected rows are visible
                    }
                });
                
                // Mark selection as applied
                window.selectionApplied = true;
                
                // Update the UI
                $('#apply_selection').hide();
                $('#reset_selection').show();
                $('#selection_applied_message').show();
                
                // Update the total based on visible rows
                updateSelectedTotal();
            }
            
            // Function to reset selection
            window.resetSelection = function() {
                // Show all rows
                $('#purchases-expenses-table tbody tr').removeClass('hide-row');
                
                // Reset selection status
                window.selectionApplied = false;
                
                // Update the UI
                $('#reset_selection').hide();
                $('#apply_selection').show();
                $('#selection_applied_message').hide();
                
                // Reset total to original
                $('#total_amount').html(originalTotalHtml);
            }

            // Function to load data via AJAX
            function loadPurchasesExpensesData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_id: $('#location_id').val(),
                    supplier_id: $('#supplier_id').val(),
                    type: $('#type').val(),
                    page: currentPage,
                    limit: rowLimit
                };

                $.ajax({
                    url: '{{ action('\Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController@purchasesExpensesReport') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // Loading indicator
                        $('#purchases-expenses-table-body').html(
                            '<tr><td colspan="9" class="text-center">{{ __("minireportb1::minireportb1.loading") }}</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#purchases-expenses-table-body');
                        tbody.empty();

                        // Update pagination variables
                        totalPages = response.total_pages || 1;
                        $('#total-pages').text(totalPages);
                        $('#current-page').text(currentPage);
                        
                        // Update pagination buttons
                        $('#prev-page').prop('disabled', currentPage <= 1);
                        $('#next-page').prop('disabled', currentPage >= totalPages);

                        // Store original total
                        originalTotalHtml = '<strong>' + __currency_trans_from_en(response.total_amount || 0, false) + '</strong>';
                        $('#total_amount').html(originalTotalHtml);

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                // Calculate the global row index across all pages
                                const rowIndex = (currentPage - 1) * rowLimit + index + 1;
                                
                                // Check if this row is in the selected set
                                const isSelected = window.selectedRows.has(row.id.toString());
                                
                                // Create row with the hide-row class if selection is applied and not selected
                                const rowClass = (window.selectionApplied && !isSelected) ? 'hide-row' : '';
                                
                                const tr = $('<tr>')
                                    .attr('data-id', row.id)
                                    .addClass(rowClass)
                                    .append(
                                        $('<td>').append(
                                            $('<input>')
                                                .attr('type', 'checkbox')
                                                .addClass('row-select')
                                                .attr('data-id', row.id)
                                                .prop('checked', isSelected)
                                        ),
                                        $('<td>').text(rowIndex),
                                        $('<td>').text(row.date),
                                        $('<td>').html(function() {
                                            // Create a link based on the transaction type that goes directly to the transaction detail page
                                            const transactionUrl = row.type.toLowerCase() === 'purchase' 
                                                ? '{{ url("/purchases") }}/' + row.id
                                                : '{{ url("/expenses") }}/' + row.id;
                                            
                                            return `<a href="${transactionUrl}" target="_blank" class="transaction-link" 
                                                      data-type="${row.type.toLowerCase()}" data-id="${row.id}">
                                                      ${row.tran_no}
                                                   </a>`;
                                        }),
                                        $('<td>').text(row.location),
                                        $('<td>').text(translateTransactionType(row.type)),
                                        $('<td>')
                                            .attr('data-amount', row.final_total)
                                            .text(__currency_trans_from_en(row.final_total, false)),
                                        $('<td>').text(translatePaymentStatus(row.payment_status)),
                                        $('<td>').text(row.contact_or_supplier_name),
                                        
                                    );
                                tbody.append(tr);
                            });

                            // Add event listeners to checkboxes
                            $('.row-select').on('change', function() {
                                const id = $(this).data('id').toString();
                                
                                if ($(this).is(':checked')) {
                                    window.selectedRows.add(id);
                                } else {
                                    window.selectedRows.delete(id);
                                }
                                
                                updateSelectedCount();
                                
                                // If selection is already applied, automatically show/hide
                                if (window.selectionApplied) {
                                    const row = $(this).closest('tr');
                                    if ($(this).is(':checked')) {
                                        row.removeClass('hide-row');
                                    } else {
                                        row.addClass('hide-row');
                                    }
                                    
                                    // Update the total based on visible rows
                                    updateSelectedTotal();
                                }
                            });
                        } else {
                            tbody.html(
                                '<tr><td colspan="9" class="text-center">{{ __("minireportb1::minireportb1.no_data_available") }}</td></tr>'
                            );
                        }
                        
                        // Update check all checkbox state
                        updateCheckAllState();
                        
                        // If selection is applied, update the total
                        if (window.selectionApplied) {
                            updateSelectedTotal();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#purchases-expenses-table-body').html(
                            '<tr><td colspan="9" class="text-center text-danger">{{ __("minireportb1::minireportb1.error_loading_data") }}</td></tr>'
                        );
                    }
                });
            }

            // Function to update "check all" checkbox state
            function updateCheckAllState() {
                const checkboxes = $('.row-select:visible');
                const checkedBoxes = $('.row-select:visible:checked');
                $('#check_all').prop('checked', checkboxes.length > 0 && checkboxes.length === checkedBoxes.length);
            }

            // Initial load
            loadPurchasesExpensesData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadPurchasesExpensesData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadPurchasesExpensesData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadPurchasesExpensesData();
                }
            });

            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    currentPage = 1; // Reset to first page when changing filters
                    loadPurchasesExpensesData(); // Reload data for non-custom range selections
                }
            });

            // Event listeners for location, supplier and type filter changes
            $('#location_id, #supplier_id, #type').on('change', function() {
                currentPage = 1; // Reset to first page when changing filters
                loadPurchasesExpensesData();
            });

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to first page when changing filters
                    loadPurchasesExpensesData();
                } else {
                    alert('{{ __("minireportb1::minireportb1.please_select_dates") }}');
                }
            });

            // Select all checkboxes
            $('#check_all').on('change', function() {
                const isChecked = $(this).is(':checked');
                
                // Only affect visible rows
                $('.row-select:visible').prop('checked', isChecked);
                
                // Update selectedRows set
                $('.row-select:visible').each(function() {
                    const id = $(this).data('id').toString();
                    
                    if (isChecked) {
                        window.selectedRows.add(id);
                    } else {
                        window.selectedRows.delete(id);
                    }
                });
                
                updateSelectedCount();
                
                // If selection is already applied, automatically update totals
                if (window.selectionApplied) {
                    updateSelectedTotal();
                }
            });

            // Select all button
            $('#select_all').on('click', function() {
                $('#check_all').prop('checked', true).trigger('change');
            });

            // Select none button
            $('#select_none').on('click', function() {
                $('#check_all').prop('checked', false).trigger('change');
            });
            
            // Apply selection button
            $('#apply_selection').on('click', function() {
                window.applySelection();
            });
            
            // Reset selection button
            $('#reset_selection').on('click', function() {
                window.resetSelection();
            });

            // Handle transaction link clicks
            $(document).on('click', '.transaction-link', function(e) {
                // Prevent the row selection
                e.stopPropagation();
                
                const type = $(this).data('type');
                const id = $(this).data('id');
                
                // Log the click
                console.log(`Opening ${type} with ID ${id}`);
                
                // The href in the link will handle the navigation
            });
        });
    </script>

@endsection 