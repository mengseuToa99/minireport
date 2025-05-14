@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.withholding_tax_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')

<div style="margin: 16px" class="no-print">
    @include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
</div>

<div style="margin: 16px" class="no-print">
    @component('components.filters', ['title' => __('report.filters')])
    <div class="filter-container">
        <form id="filterForm">

            @include('minireportb1::MiniReportB1.components.filterlocation', ['locations' => $business_locations])

            <!-- Supplier Filter -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="supplier_id">{{ __('minireportb1::minireportb1.supplier') }}</label>
                    <select class="form-control" id="supplier_id" name="supplier_id">
                        <option value="">{{ __('minireportb1::minireportb1.all_suppliers') }}</option>
                        @foreach($suppliers as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tax Type Filter -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tax_type">{{ __('minireportb1::minireportb1.tax_type') }}</label>
                    <select class="form-control" id="tax_type" name="tax_type">
                        <option value="">{{ __('minireportb1::minireportb1.all_tax_types') }}</option>
                        <option value="resident">{{ __('minireportb1::minireportb1.resident') }}</option>
                        <option value="non_resident">{{ __('minireportb1::minireportb1.non_resident') }}</option>
                    </select>
                </div>
            </div>

            @include('minireportb1::MiniReportB1.components.filterdate')

            
            <!-- Filter Buttons -->
          
            
            @include('minireportb1::MiniReportB1.components.printbutton')
        </form>
    </div>
    @endcomponent
</div>


@include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.withholding_tax_report')])

<!-- Exchange Rate Information -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title khmer-text">អត្រាប្តូរប្រាក់</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>1 USD = <span id="exchange-rate-display">4100</span> KHR</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="reusable-table-container">
    <!-- Row limit controls -->
    @include('minireportb1::MiniReportB1.components.pagination')

    <table class="reusable-table wide-table sticky-first-col" id="withholding-tax-table">
        <thead>
            <tr>
                <th class="col-xs khmer-text" rowspan="2">ល.រ</th>
                <th class="col-xs khmer-text" rowspan="2">កាលបរិច្ឆេទ</th>
                <th class="text-center khmer-text" rowspan="2">លេខវិក្កយបត្រ</th>
                <th class="text-center khmer-text" rowspan="2">ប្រភេទពន្ធកាត់ទុក</th>
                <th class="text-center khmer-text" colspan="2">អ្នកទទួលប្រាក់</th>
                <th class="col-xs khmer-text" rowspan="2">អត្រាពន្ធ</th>
                <th class="col-xs khmer-text" rowspan="2">ទឹកប្រាក់ត្រូវបើក (KHR)</th>
                <th class="col-xs khmer-text" rowspan="2">ពន្ធកាត់ទុក (KHR)</th>
                <th class="col-xs khmer-text" rowspan="2">លេខសក្ខីបត្រ</th>
                <th class="col-xs khmer-text" rowspan="2">ស្ថានភាពប្រកាសពន្ធ</th>
            </tr>
            <tr>
                <th class="text-center khmer-text">ឈ្មោះ</th>
                <th class="text-center khmer-text">លេខសម្គាល់</th>
            </tr>
        </thead>
        <tbody id="withholding-tax-table-body">
            <!-- Data will be loaded via AJAX -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right"><strong>សរុប:</strong></td>
                <td class="text-right" id="total-payable-khr"></td>
                <td class="text-right" id="total-withholding-khr"></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Pagination variables
        let currentPage = 1;
        let totalPages = 1;
        let rowLimit = 10;
        let exchangeRate = 4100; // Default exchange rate: 1 USD = 4,100 KHR

        // Function to load data via AJAX
        function loadWithholdingTaxData() {
            // Show loading indicator
            $('#withholding-tax-table-body').html('<tr><td colspan="11" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</td></tr>');
            
            const formData = {
                date_filter: $('#date_filter').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                location_filter: $('#location_filter').val(),
                supplier_id: $('#supplier_id').val(),
                tax_type: $('#tax_type').val(),
                page: currentPage,
                limit: rowLimit
            };

            $.ajax({
                url: "{{ route('minireportb1.getWithholdingTaxData') }}",
                type: "GET",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log('Received response:', response);
                    
                    // Update exchange rate display
                    exchangeRate = response.exchange_rate || 4100;
                    $('#exchange-rate-display').text(exchangeRate.toLocaleString());
                    
                    if (response.data && response.data.length > 0) {
                        let html = '';
                        $.each(response.data, function(index, row) {
                            const rowIndex = (currentPage - 1) * rowLimit + index + 1;
                            const payableAmount = parseFloat(row.payable_amount) || 0;
                            const withholdingTax = parseFloat(row.withholding_tax) || 0;
                            const payableAmountKHR = payableAmount * exchangeRate;
                            const withholdingTaxKHR = withholdingTax * exchangeRate;
                            
                            // Format date from MySQL format (YYYY-MM-DD) to DD-MM-YYYY
                            let formattedDate = '';
                            if (row.date) {
                                const dateParts = row.date.split('-');
                                if (dateParts.length === 3) {
                                    formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
                                } else {
                                    formattedDate = row.date;
                                }
                            }
                            
                            html += `
                                <tr>
                                    <td>${rowIndex}</td>
                                    <td>${formattedDate}</td>
                                    <td>${row.invoice_no || ''}</td>
                                    <td>${row.tax_type || ''}</td>
                                    <td>${row.contact_name || ''}</td>
                                    <td>${row.tax_number || ''}</td>
                                    <td>${row.tax_rate || ''}</td>
                                    <td class="text-right">${__currency_trans_from_en(payableAmountKHR, false)}</td>
                                    <td class="text-right">${__currency_trans_from_en(withholdingTaxKHR, false)}</td>
                                    <td>${row.certificate_number || ''}</td>
                                    <td>${row.tax_status || ''}</td>
                                </tr>
                            `;
                        });
                        $('#withholding-tax-table-body').html(html);

                        // Update totals
                        const totalPayableAmount = parseFloat(response.total_payable_amount) || 0;
                        const totalWithholdingTax = parseFloat(response.total_withholding_tax) || 0;
                        const totalPayableKHR = totalPayableAmount * exchangeRate;
                        const totalWithholdingKHR = totalWithholdingTax * exchangeRate;
                        
                        $('#total-payable-khr').html(__currency_trans_from_en(totalPayableKHR, false));
                        $('#total-withholding-khr').html(__currency_trans_from_en(totalWithholdingKHR, false));

                        // Update pagination
                        totalPages = response.total_pages || 1;
                        updatePagination();
                    } else {
                        $('#withholding-tax-table-body').html('<tr><td colspan="11" class="text-center">No data found</td></tr>');
                        $('#total-payable-khr, #total-withholding-khr').html('0.00');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', xhr.responseText);
                    $('#withholding-tax-table-body').html('<tr><td colspan="11" class="text-center">Error loading data. Please try again.</td></tr>');
                }
            });
        }

        // Function to update pagination controls
        function updatePagination() {
            let totalRecords = totalPages * rowLimit;
            let currentStart = ((currentPage - 1) * rowLimit) + 1;
            let currentEnd = Math.min(currentPage * rowLimit, totalRecords);
            
            $('.pagination-info').text(`Showing ${currentStart} to ${currentEnd} of ${totalRecords} entries`);
            $('.pagination-controls').html(`
                <button class="btn btn-default" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Previous</button>
                <span>Page ${currentPage} of ${totalPages}</span>
                <button class="btn btn-default" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>
            `);
        }

        // Function to change page
        window.changePage = function(page) {
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                loadWithholdingTaxData();
            }
        }

        // Function to change row limit
        window.changeRowLimit = function(limit) {
            rowLimit = parseInt(limit);
            currentPage = 1;
            loadWithholdingTaxData();
        }

        // Apply filter button click handler
        $('#apply_filter').click(function(e) {
            e.preventDefault();
            currentPage = 1;
            loadWithholdingTaxData();
        });

        // Reset filter button click handler
        $('#reset_filter').click(function(e) {
            e.preventDefault();
            $('#filterForm')[0].reset();
            if ($('#custom_range_inputs').length) {
                $('#custom_range_inputs').hide();
            }
            currentPage = 1;
            loadWithholdingTaxData();
        });

        // Load initial data
        loadWithholdingTaxData();

        // Handle print button click
        $('.print-button').click(function() {
            window.print();
        });
    });
</script>

@endsection