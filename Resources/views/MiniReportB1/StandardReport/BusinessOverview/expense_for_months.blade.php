@extends('layouts.app')
@section('title', 'ចំណាយប្រចាំខែ (Monthly Expenses)')
@include('minireportb1::MiniReportB1.components.linkforinclude')


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

        @include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'Expense Report (Monthly Expense)'
    ])


    <div class="reusable-table-container">
        {{-- Row limit controls --}}
        @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table" id="expense-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-sm">ថ្ងៃ ខែ ឆ្នាំ (Date)</th>
                    <th class="col-md">អ្នកផ្គត់ផ្គង់ (Supplier)</th>
                    <th class="col-md">បរិយាយចំណាយ (Description)</th>
                    <th class="col-xs">ប្រភេទ (Type)</th>
                    <th class="col-sm">លេខប័ណ្ណ# (Voucher No.)</th>
                    <th class="col-sm">តម្លៃសរុប(ដុល្លារ) (Total USD)</th>
                    <th class="col-sm">តម្លៃសុទ្ធ(ដុល្លារ) (Net Amount USD)</th>
                    <th class="col-xs">VAT input 10%(ដុល្លារ) (VAT Input USD)</th>
                    <th class="col-xs">អត្រាប្ដូរប្រាក់ (Exchange Rate)</th>
                    <th class="col-sm">តម្លៃសុទ្ធ(រៀល) (Net Amount KHR)</th>
                    <th class="col-xs">VAT Input 10% (រៀល) (VAT Input KHR)</th>
                    <th class="col-sm">តម្លៃដុល(រៀល) (Total KHR)</th>
                    <th class="col-xs">WHT 10%(រៀល) (WHT KHR)</th>
                    <th class="col-xs">លេខអត្តសញ្ញាណកម្មសារពើពន្ធ (VAT TIN)</th>
                </tr>
            </thead>
            <tbody id="expense-table-body">
                <tr>
                    <td colspan="15" class="text-center">កំពុងផ្ទុកទិន្នន័យ... (Loading data...)</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>សរុប (Total):</strong></td>
                    <td class="number" id="total-final-total"><strong>0.00</strong></td>
                    <td class="number" id="total-net-amount-usd"><strong>0.00</strong></td>
                    <td class="number" id="total-vat-input-usd"><strong>0.00</strong></td>
                    <td></td>
                    <td class="number" id="total-net-amount-khr"><strong>0</strong></td>
                    <td class="number" id="total-vat-input-khr"><strong>0</strong></td>
                    <td class="number" id="total-final-total-khr"><strong>0</strong></td>
                    <td class="number" id="total-wht-khr"><strong>0</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#expense-table";
        const reportname = "របាយការណ៍ចំណាយប្រចាំខែ (Monthly Expense Report)";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection

@section('javascript')
<script src="{{ asset('assets/js/date_range_filter.js') }}"></script>
<script src="{{ asset('modules/minireportb1/js/pagination.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            // Pagination variables
            let currentPage = 1;
            let totalPages = 1;
            let rowLimit = 10;

            // Initialize date picker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            // Listen for date filter changes from the filterdate component
            document.addEventListener('dateFilterChanged', function(e) {
                const { dateFilter, startDate, endDate } = e.detail;
                
                // Update the form values
                $('#date_filter').val(dateFilter);
                if (startDate) $('#start_date').val(startDate);
                if (endDate) $('#end_date').val(endDate);
                
                // Load data with new filter
                loaloadDatadExpenseData();
            });

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
                    url: '{{ route("sr_exspend_month") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function () {
                        $('#expense-table-body').html(
                            '<tr><td colspan="15" class="text-center">កំពុងផ្ទុកទិន្នន័យ... (Loading data...)</td></tr>'
                        );
                    },
                    success: function (response) {
                        const tbody = $('#expense-table-body');
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
                                
                                // Inside your Ajax success function:
                                const tr = $('<tr>').append(
                                    $('<td>').text(rowIndex),
                                    $('<td>').text(row.transaction_date ? formatDate(row.transaction_date.split(' ')[0]) : ''),
                                    $('<td>').text(row.contact_name || ''),
                                    $('<td>').text(row.additional_notes || ''),
                                    $('<td>').text('PV'),
                                    $('<td>').text(row.ref_no),
                                    $('<td class="number" data-value="' + parseFloat(row.final_total || 0) + '">').text(formatNumber(parseFloat(row.final_total || 0).toFixed(2))),
                                    $('<td class="number" data-value="' + parseFloat(row.net_amount_usd || 0) + '">').text(formatNumber(parseFloat(row.net_amount_usd || 0).toFixed(2))),
                                    $('<td class="number" data-value="' + parseFloat(row.vat_input_usd || 0) + '">').text(formatNumber(parseFloat(row.vat_input_usd || 0).toFixed(2))),
                                    $('<td class="number">').text(formatNumber(parseInt(row.exchange_rate_khr || 0))),
                                    $('<td class="number" data-value="' + parseInt(row.net_amount_khr || 0) + '">').text(formatNumber(parseInt(row.net_amount_khr || 0))),
                                    $('<td class="number" data-value="' + parseInt(row.vat_input_khr || 0) + '">').text(formatNumber(parseInt(row.vat_input_khr || 0))),
                                    $('<td class="number" data-value="' + parseInt(row.final_total_khr || 0) + '">').text(formatNumber(parseInt(row.final_total_khr || 0))),
                                    $('<td class="number" data-value="' + parseInt(row.wht_khr || 0) + '">').text(formatNumber(parseInt(row.wht_khr || 0))),
                                    $('<td>').text(row.tax_number || '')
                                );
                                tbody.append(tr);
                            });

                            // Calculate page totals from visible rows
                            setTimeout(calculatePageTotals, 200);
                        } else {
                            tbody.append(
                                '<tr><td colspan="15" class="text-center">មិនមានកំណត់ត្រាចំណាយទេ (No expense records)</td></tr>'
                            );
                            
                            // Clear totals when no data
                            $('#total-final-total strong').text('0.00');
                            $('#total-net-amount-usd strong').text('0.00');
                            $('#total-vat-input-usd strong').text('0.00');
                            $('#total-net-amount-khr strong').text('0');
                            $('#total-vat-input-khr strong').text('0');
                            $('#total-final-total-khr strong').text('0');
                            $('#total-wht-khr strong').text('0');
                        }
                        
                        // Update the report header date range
                        updateReportHeaderDateRange();
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#expense-table-body').html(
                            '<tr><td colspan="15" class="text-center text-danger">មានបញ្ហាក្នុងការផ្ទុកទិន្នន័យ (Error loading data)</td></tr>'
                        );
                    }
                });
            }

            // Function to calculate totals from visible rows only
            function calculatePageTotals() {
                console.log('Calculating page totals...');
                
                let totalFinalTotal = 0;
                let totalNetAmountUsd = 0;
                let totalVatInputUsd = 0;
                let totalNetAmountKhr = 0;
                let totalVatInputKhr = 0;
                let totalFinalTotalKhr = 0;
                let totalWhtKhr = 0;
                
                // Get all visible rows in the table body
                $('#expense-table-body tr').each(function() {
                    // Add values from data attributes
                    totalFinalTotal += parseFloat($(this).find('td:nth-child(7)').data('value') || 0);
                    totalNetAmountUsd += parseFloat($(this).find('td:nth-child(8)').data('value') || 0);
                    totalVatInputUsd += parseFloat($(this).find('td:nth-child(9)').data('value') || 0);
                    totalNetAmountKhr += parseInt($(this).find('td:nth-child(11)').data('value') || 0);
                    totalVatInputKhr += parseInt($(this).find('td:nth-child(12)').data('value') || 0);
                    totalFinalTotalKhr += parseInt($(this).find('td:nth-child(13)').data('value') || 0);
                    totalWhtKhr += parseInt($(this).find('td:nth-child(14)').data('value') || 0);
                });
                
                // Log for debugging
                console.log('Calculated totals:', {
                    totalFinalTotal,
                    totalNetAmountUsd,
                    totalVatInputUsd,
                    totalNetAmountKhr,
                    totalVatInputKhr,
                    totalFinalTotalKhr,
                    totalWhtKhr
                });
                
                // Update the totals in the footer
                $('#total-final-total strong').text(formatNumber(totalFinalTotal.toFixed(2)));
                $('#total-net-amount-usd strong').text(formatNumber(totalNetAmountUsd.toFixed(2)));
                $('#total-vat-input-usd strong').text(formatNumber(totalVatInputUsd.toFixed(2)));
                $('#total-net-amount-khr strong').text(formatNumber(totalNetAmountKhr));
                $('#total-vat-input-khr strong').text(formatNumber(totalVatInputKhr));
                $('#total-final-total-khr strong').text(formatNumber(totalFinalTotalKhr));
                $('#total-wht-khr strong').text(formatNumber(totalWhtKhr));
            }

            // Helper function to format numbers with commas
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Helper function to format date as DD/MM/YYYY
            function formatDate(dateString) {
                if (!dateString) return '';
                const parts = dateString.split('-');
                if (parts.length === 3) {
                    return `${parts[2]}/${parts[1]}/${parts[0]}`; // Convert YYYY-MM-DD to DD/MM/YYYY
                }
                return dateString;
            }

            // Function to update the report header date range for printing
            function updateReportHeaderDateRange() {
                const dateFilter = $('#date_filter').val();
                let dateRangeText = '';
                
                if (dateFilter === 'custom_month_range') {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();
                    if (startDate && endDate) {
                        const startFormatted = formatDateForDisplay(startDate);
                        const endFormatted = formatDateForDisplay(endDate);
                        dateRangeText = startFormatted + ' - ' + endFormatted;
                    }
                } else if (dateFilter) {
                    // For predefined date ranges, get the text from the selected option
                    const selectedOption = $('#date_filter option:selected');
                    dateRangeText = selectedOption.text();
                } else {
                    dateRangeText = '{{ trans("minireportb1::minireportb1.all_dates") }}';
                }
                
                // Update the date range in the header without page refresh
                $('#report-date-range').text(dateRangeText);
            }
            
            // Helper function to format date for display (YYYY-MM-DD to DD/MM/YYYY)
            function formatDateForDisplay(dateString) {
                if (!dateString) return '';
                const parts = dateString.split('-');
                if (parts.length === 3) {
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return dateString;
            }

            // Handle the visibility of custom date range inputs
            function updateDateRangeVisibility() {
                const dateFilter = $('#date_filter').val();
                if (dateFilter === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                }
            }

            // Initial visibility based on default selection
            updateDateRangeVisibility();

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

            // Add event listener for any table filtering
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadData();
            });

            // Initial data load
            loadData();
        });
    </script>
@endsection
