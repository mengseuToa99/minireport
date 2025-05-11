@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.vat_sales_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <div style="margin: 16px" class="no-print">

        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">

                <form id="filterForm">
                    @include('minireportb1::MiniReportB1.components.filterdate')
                    <div class="form-group mt-3">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                    <div class="form-group mt-3">
                        {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent

    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.vat_sales_report')])

    <div class="reusable-table-container">
        <!-- Row limit controls -->
      @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table wide-table sticky-first-col" id="vat-sales-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-xs">ល.រ</th>
                    <th rowspan="2" class="col-xs">កាលបរិច្ឆេទ</th>
                    <th rowspan="2" class="text-center">លេខវិក្កយបត្រ ឬប្រតិវេទគយ</th>
                    <th rowspan="2" class="text-center">លេខលិខិតជូនដំណឹងឥណទាន</th>
                    <th colspan="3" class="text-center">អ្នកទិញ</th>
                    <th rowspan="2" class="col-xs">តម្លៃសរុបលើវិក្កយបត្រ</th>
                    <th colspan="2" class="text-center">តម្លៃ​​មិន​រួមអតប និងមិនជាប់​អតប​</th>
                    <th rowspan="2" class="col-xs">អាករលើតម្លៃបន្ថែមអត្រា ០%</th>
                    <th colspan="2" class="text-center">អាករលើតម្លៃបន្ថែម</th>
                    <th rowspan="2" class="text-center">អាករលើតម្លៃបន្ថែម(បន្ទុករដ្ឋ)</th>
                    <th rowspan="2" class="col-xs">អតប កាត់ទុកដោយរតនាគារជាតិ</th>
                    <th rowspan="2" class="col-xs">អាករបំភ្លឺសាធារណៈ</th>
                    <th rowspan="2" class="col-xs">អាករពិសេសលើទំនិញមួយចំនួន</th>
                    <th rowspan="2" class="col-xs">អាករពិសេសលើសេវាមួយចំនួន</th>
                    <th rowspan="2" class="col-xs">អាករលើការស្នាក់នៅ</th>
                    <th rowspan="2" class="col-xs">អត្រាប្រាក់រំដោះពន្ធលើប្រាក់ចំណូល</th>
                    <th rowspan="2" class="col-sm">កំណត់សម្គាល់</th>
                    <th rowspan="2" class="col-sm">បរិយាយ</th>
                    <th rowspan="2" class="col-sm">ការលក់ក្នុងស្រុក</th>

                </tr>
                <tr>
                    <th class="col-xs">ប្រភេទ</th>
                    <th class="col-sm">លេខសម្គាល់ចុះបញ្ជីពន្ធដារ</th>
                    <th class="col-sm">ឈ្មោះ</th>
                    <th class="col-sm">តម្លៃមិនរួមអតប</th>
                    <th class="col-sm">ការលក់មិនជាប់អតប</th>
                    <th class="col-xs">ការលក់ក្នុងស្រុក</th>
                    <th class="col-xs">អតបលើការនាំចេញ</th>
                </tr>
            </thead>
            <tbody id="vat-sales-table-body">
                <!-- Data will be loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-right"><strong>សរុប៖</strong></td>
                    <td id="total_final" class="text-right"><strong>0</strong></td>
                    <td id="total_before_tax" class="text-right"><strong>0</strong></td>
                    <td id="total_exempt" class="text-right"><strong>0</strong></td>
                    <td id="total_tax" class="text-right"><strong>0</strong></td>
                    <td colspan="12"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#vat-sales-table";
        const reportname = "របាយការណ៍អាករលើតម្លៃបន្ថែម";
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

            // Function to load data via AJAX
            function loadVatSalesData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_id: $('#location_id').val(),
                    customer_id: $('#customer_id').val(),
                    page: currentPage,
                    limit: rowLimit
                };

                $.ajax({
                    url: '{{ action('\Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController@vatSalesReport') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // Loading indicator
                        $('#vat-sales-table-body').html(
                            '<tr><td colspan="22" class="text-center">កំពុងផ្ទុកទិន្នន័យ...</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#vat-sales-table-body');
                        tbody.empty();

                        // Update pagination variables
                        totalPages = response.total_pages || 1;
                        $('#total-pages').text(totalPages);
                        $('#current-page').text(currentPage);
                        
                        // Update pagination buttons
                        $('#prev-page').prop('disabled', currentPage <= 1);
                        $('#next-page').prop('disabled', currentPage >= totalPages);

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                // Calculate the global row index across all pages
                                const rowIndex = (currentPage - 1) * rowLimit + index + 1;
                                
                                const tr = $('<tr>').append(
                                    $('<td>').text(rowIndex),
                                    $('<td>').text(row.date),
                                    $('<td>').text(row.invoice_no),
                                    $('<td>').text(row.cn_number),
                                    $('<td>').text(row.contact_type),
                                    $('<td>').text(row.tax_number),
                                    $('<td>').text(row.contact_name),
                                    $('<td>').text(__currency_trans_from_en(row.final_total, false)),
                                    $('<td>').text(__currency_trans_from_en(row.total_before_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.exempt_amount, false)),
                                    $('<td>').text(__currency_trans_from_en(row.export_vat, false)),
                                    $('<td>').text(__currency_trans_from_en(row.domestic_sale, false)),
                                    $('<td>').text(__currency_trans_from_en(row.domestic_sale_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.withholding_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.public_lighting_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.special_goods_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.special_services_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.accommodation_tax, false)),
                                    $('<td>').text(__currency_trans_from_en(row.income_tax_rate, false)),
                                    $('<td>').text(row.notes),
                                    $('<td>').text(''),
                                    $('<td>').text(row.description),
                                    $('<td>').text(row.status)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.html(
                                '<tr><td colspan="24" class="text-center">មិនមានទិន្នន័យសម្រាប់ការចម្រាញ់​ជ្រើសរើស​។</td></tr>'
                            );
                        }

                        // Update totals - we use the grand totals from all records
                        $('#total_final').html('<strong>' + __currency_trans_from_en(response.total_final || 0, false) + '</strong>');
                        $('#total_before_tax').html('<strong>' + __currency_trans_from_en(response.total_before_tax || 0, false) + '</strong>');
                        $('#total_exempt').html('<strong>' + __currency_trans_from_en(response.total_exempt || 0, false) + '</strong>');
                        $('#total_tax').html('<strong>' + __currency_trans_from_en(response.total_tax || 0, false) + '</strong>');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#vat-sales-table-body').html(
                            '<tr><td colspan="24" class="text-center text-danger">មានបញ្ហាក្នុងការទាញយកទិន្នន័យ។ សូមព្យាយាមម្តងទៀត។</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadVatSalesData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadVatSalesData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadVatSalesData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadVatSalesData();
                }
            });

            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    currentPage = 1; // Reset to first page when changing filters
                    loadVatSalesData(); // Reload data for non-custom range selections
                }
            });

            // Event listeners for location and customer filter changes
            $('#location_id, #customer_id').on('change', function() {
                currentPage = 1; // Reset to first page when changing filters
                loadVatSalesData();
            });

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to first page when changing filters
                    loadVatSalesData();
                } else {
                    alert('Please select both start and end dates.');
                }
            });
        });
    </script>

@endsection 