@extends('layouts.app')
@include('minireportb1::MiniReportB1.components.linkforinclude')
@section('title', __('minireportb1::minireportb1.cashbook'))

@section('content')

@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">

                <form id="filterForm">
                    @include('minireportb1::MiniReportB1.components.filterdate')
                
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent
    </div>

   
    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => 'សៀវភៅធនាគារ (Cashbook)'])


    <div class="reusable-table-container" id="cashbook">
        <!-- Row limit controls -->
        @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table" id="expense-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-md">Date</th>
                    <th class="col-lg">Voucher No</th>
                    <th class="col-md">Contact Name</th>
                    <th class="col-md">Expense Note</th>
                    <th class="col-md">Cash In</th>
                    <th class="col-md">Cash Out</th>
                    <th class="col-md">Balance</th>
                </tr>
            </thead>
            <tbody id="cashbook-table-body">
                <!-- Data will be loaded via AJAX -->
                <tr>
                    <td colspan="8" class="text-center">Loading data...</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total:</strong></td>
                    <td id="total_cash_in" class="text-right"><strong>0</strong></td>
                    <td id="total_cash_out" class="text-right"><strong>0</strong></td>
                    <td id="total_balance" class="text-right"><strong>0</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Initialize datepickers
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
            });

            // Pagination variables
            let currentPage = 1;
            let totalPages = 1;
            let rowLimit = 10;
            let allData = []; // Store all data for client-side pagination

            // Function to load cashbook data via AJAX
            function loadCashbookData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    show_all: false
                };

                // Add additional filters based on controller implementation
                if ($('#date_filter').val() === 'today') {
                    formData.today = true;
                } else if ($('#date_filter').val() === 'custom_year') {
                    formData.year = $('#year').val();
                }

                $.ajax({
                    url: '{{ route("sr_cashbook") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#cashbook-table-body').html('<tr><td colspan="8" class="text-center">Loading data...</td></tr>');
                    },
                    success: function(response) {
                        // Store all data for client-side pagination
                        allData = response.combined_data || [];
                        
                        // Update pagination
                        updatePagination();
                        
                        // Display the current page
                        displayCurrentPage();
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#cashbook-table-body').html(
                            '<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                });
            }

            // Function to update pagination controls
            function updatePagination() {
                totalPages = Math.ceil(allData.length / rowLimit) || 1;
                $('#total-pages').text(totalPages);
                $('#current-page').text(currentPage);
                
                // Update pagination buttons
                $('#prev-page').prop('disabled', currentPage <= 1);
                $('#next-page').prop('disabled', currentPage >= totalPages);
            }

            // Function to calculate totals for the current page
            function calculatePageTotals(startIndex, endIndex) {
                let pageCashIn = 0;
                let pageCashOut = 0;
                let pageBalance = 0;

                for (let i = startIndex; i < endIndex; i++) {
                    if (i < allData.length) {
                        const row = allData[i];
                        pageCashIn += parseFloat(row.cash_in) || 0;
                        pageCashOut += parseFloat(row.cash_out) || 0;
                        pageBalance = parseFloat(row.balance) || 0; // Last balance will be the final one
                    }
                }

                return {
                    cashIn: pageCashIn,
                    cashOut: pageCashOut,
                    balance: pageBalance
                };
            }

            // Function to display current page of data
            function displayCurrentPage() {
                const tbody = $('#cashbook-table-body');
                tbody.empty();
                
                if (allData.length > 0) {
                    // Calculate start and end indices for the current page
                    const startIndex = (currentPage - 1) * rowLimit;
                    const endIndex = Math.min(startIndex + rowLimit, allData.length);
                    
                    // Calculate totals for all data
                    let totalCashIn = 0;
                    let totalCashOut = 0;
                    let finalBalance = 0;
                    
                    // Sum up all cash values
                    allData.forEach(function(row) {
                        const cashIn = parseFloat(row.cash_in) || 0;
                        const cashOut = parseFloat(row.cash_out) || 0;
                        totalCashIn += cashIn;
                        totalCashOut += cashOut;
                        finalBalance = parseFloat(row.balance) || 0; // Last balance will be the final one
                    });

                    // Calculate page totals
                    const pageTotals = calculatePageTotals(startIndex, endIndex);
                    
                    // Display data for current page
                    for (let i = startIndex; i < endIndex; i++) {
                        const row = allData[i];
                        const rowIndex = i + 1; // 1-based index
                        
                        const tr = $('<tr>').addClass('table-row').append(
                            $('<td>').text(rowIndex),
                            $('<td>').text(row.date),
                            $('<td>').text(row.voucher),
                            $('<td>').text(row.contact_name),
                            $('<td>').text(row.description),
                            $('<td>').text(row.cash_in === '0.00' ? '' : row.cash_in),
                            $('<td>').text(row.cash_out === '0.00' ? '' : row.cash_out),
                            $('<td>').text(row.balance)
                        );
                        tbody.append(tr);
                    }
                    
                    // Update totals in footer
                    $('#total_cash_in').html('<strong>' + pageTotals.cashIn.toFixed(2) + '</strong>');
                    $('#total_cash_out').html('<strong>' + pageTotals.cashOut.toFixed(2) + '</strong>');
                    $('#total_balance').html('<strong>' + pageTotals.balance.toFixed(2) + '</strong>');
                } else {
                    tbody.append(
                        $('<tr>').append(
                            $('<td>').attr('colspan', 8).addClass('text-center').text('No data found for the selected filters.')
                        )
                    );
                    
                    // Reset totals when no data
                    $('#total_cash_in').html('<strong>0.00</strong>');
                    $('#total_cash_out').html('<strong>0.00</strong>');
                    $('#total_balance').html('<strong>0.00</strong>');
                }
            }

            // Initial load
            loadCashbookData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                updatePagination();
                displayCurrentPage();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                    displayCurrentPage();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                    displayCurrentPage();
                }
            });

            // Event listeners for filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    currentPage = 1; // Reset to first page when changing filters
                    loadCashbookData();
                }
            });

            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to first page when changing filters
                    loadCashbookData();
                } else {
                    alert('Please select both start and end dates.');
                }
            });
            
          
        });
    </script>
@endsection