@extends('layouts.app')
@section('title', 'Income for The Month')
@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $title_str = match (true) {
            !empty(request('today')) => "Today's Income - " . now()->format('d F Y'),
            !empty(request('month')) => 'Income for ' .
                \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y'),
            !empty(request('year')) => 'Income for Year ' . request('year'),
            !empty(request('show_all')) => 'Income Report - All Records',
            default => 'Income for ' . now()->format('F Y'),
        };
    @endphp

    @include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

 

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="filter-container">
            <form id="filterForm">
                @include('minireportb1::MiniReportB1.components.date_range_filter')
                
            </form>
            @include('minireportb1::MiniReportB1.components.printbutton')
        </div>
        @endcomponent
    </div>


    @include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'Income Report (Monthly Income)'
    ])

    <div class="reusable-table-container">
        {{-- Row limit controls --}}
        @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table" id="income-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-sm">Date</th>
                    <th class="col-xs">Voucher #</th>
                    <th class="col-sm">Net Price ($)</th>
                    <th class="col-xs">VAT 10%</th>
                    <th class="col-sm">Gross Sale Amount ($)</th>
                    <th class="col-md">Customer</th>
                    <th class="col-md">Invoice NO</th>
                    <th class="col-md">Product Category</th>
                    <th class="col-xs">Rate (KHR)</th>
                    <th class="col-sm">Amount (KHR)</th>
                </tr>
            </thead>
            <tbody id="income-table-body">
                <tr>
                    <td colspan="11" class="text-center">Loading data...</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td class="number" id="total-net-price"><strong>0.00</strong></td>
                    <td class="number"></td>
                    <td class="number" id="total-cross-sale"><strong>0.00</strong></td>
                    <td colspan="4"></td>
                    <td class="number" id="total-khr"><strong>0</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>


@endsection

@section('javascript')
  
    <script>
        $(document).ready(function () {
            // Pagination variables
            let currentPage = 1;
            let totalPages = 1;
            let rowLimit = 10;
            // Store page totals
            let pageTotals = {
                netPrice: '0.00',
                crossSale: '0.00',
                khrAmount: '0'
            };

          

            // Calculate page totals from the displayed rows
            function calculatePageTotals() {
                let netPriceTotal = 0;
                let crossSaleTotal = 0;
                let khrTotal = 0;
                
                // Loop through each row in the table body
                $('#income-table-body tr').each(function() {
                    const cells = $(this).find('td');
                    if (cells.length > 1) { // Skip any message rows
                        // Parse the numeric values from the cells (removing formatting)
                        const netPrice = parseFloat($(cells[3]).text().replace(/,/g, '')) || 0;
                        const crossSale = parseFloat($(cells[5]).text().replace(/,/g, '')) || 0;
                        const khrAmount = parseFloat($(cells[10]).text().replace(/,/g, '')) || 0;
                        
                        // Add to totals
                        netPriceTotal += netPrice;
                        crossSaleTotal += crossSale;
                        khrTotal += khrAmount;
                    }
                });
                
                // Format the totals
                pageTotals.netPrice = number_format(netPriceTotal, 2, '.', ',');
                pageTotals.crossSale = number_format(crossSaleTotal, 2, '.', ',');
                pageTotals.khrAmount = number_format(khrTotal, 0, '.', ',');
                
                // Update the display
                $('#total-net-price').html('<strong>' + pageTotals.netPrice + '</strong>');
                $('#total-cross-sale').html('<strong>' + pageTotals.crossSale + '</strong>');
                $('#total-khr').html('<strong>' + pageTotals.khrAmount + '</strong>');
            }

            // AJAX data loading
            function loadData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    page: currentPage,
                    limit: rowLimit
                };

                $.ajax({
                    url: '{{ action([\Modules\MiniReportB1\Http\Controllers\StandardReport\BusinessOverviewController::class, "getIncomeForMonths"]) }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function () {
                        $('#income-table-body').html(
                            '<tr><td colspan="11" class="text-center">Loading data...</td></tr>'
                        );
                    },
                    success: function (response) {
                        const tbody = $('#income-table-body');
                        tbody.empty();

                        // Update pagination variables
                        totalPages = response.total_pages || 1;
                        $('#total-pages').text(totalPages);
                        $('#current-page').text(currentPage);
                        
                        // Update pagination buttons
                        $('#prev-page').prop('disabled', currentPage <= 1);
                        $('#next-page').prop('disabled', currentPage >= totalPages);

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function (index, row) {
                                // Calculate the global row index across all pages
                                const rowIndex = (currentPage - 1) * rowLimit + index + 1;
                                
                                const tr = $('<tr>').append(
                                    $('<td>').text(rowIndex),
                                    $('<td>').text(row.date || ''),
                                    $('<td>').text(row.voucher || ''),
                                    $('<td class="number">').text(row.unit_price || '0.00'),
                                    $('<td class="number">').text(row.item_tax || '0.00'),
                                    $('<td class="number">').text(row.subtotal || '0.00'),
                                    $('<td>').text(row.customer || ''),
                                    $('<td>').text(row.invoice_no || ''),
                                    $('<td>').text(row.description || ''),
                                    $('<td class="number">').text(row.exchange_rate_khr || '0'),
                                    $('<td class="number khr-amount">').text(row.khr_amount || '0')
                                );
                                tbody.append(tr);
                            });
                            
                            // Calculate page totals after populating the table
                            calculatePageTotals();
                        } else {
                            tbody.append(
                                '<tr><td colspan="11" class="text-center">No income data available for the selected period.</td></tr>'
                            );
                            
                            // Reset page totals when no data
                            pageTotals.netPrice = '0.00';
                            pageTotals.crossSale = '0.00';
                            pageTotals.khrAmount = '0';
                            
                            // Update the display
                            $('#total-net-price').html('<strong>0.00</strong>');
                            $('#total-cross-sale').html('<strong>0.00</strong>');
                            $('#total-khr').html('<strong>0</strong>');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#income-table-body').html(
                            '<tr><td colspan="11" class="text-center text-danger">Error loading data</td></tr>'
                        );
                    }
                });
            }

            // Handle the visibility of custom date range inputs
            function updateDateRangeVisibility() {
                const dateFilter = $('#date_filter').val();
                if (dateFilter === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    // Load data automatically when changing filter (except for custom range)
                    if (dateFilter) {
                        currentPage = 1; // Reset to page 1 when changing filters
                        loadData();
                    }
                }
            }

            // Initial visibility based on default selection
            updateDateRangeVisibility();

            // Event listener for date filter changes
            $('#date_filter').on('change', updateDateRangeVisibility);

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to page 1 when applying custom range
                    loadData();
                } else {
                    alert('Please select both start and end dates');
                }
            });

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadData();
                }
            });

            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadData(); // Reload data for non-custom range selections
                }
            });

            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadData(); // Reload data for custom range
                } else {
                    alert('@lang('minireportb1::minireportb1.please_select_dates')');
                }
            });

            // Apply filters button
            $('#apply_filters').on('click', function() {
                loadData();
            });

            // Initial data load
            loadData();
            
            // Helper function to format numbers
            function number_format(number, decimals, dec_point, thousands_sep) {
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                    toFixedFix = function (n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + Math.round(n * k) / k;
                    };
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
        });
    </script>
@endsection
