@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.monthly_purchase_ledger'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
<div style="height: 80vh; overflow: hidden;">

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
                        {!! Form::label('supplier_id', __('contact.supplier') . ':') !!}
                        {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent

    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.monthly_purchase_ledger')])

    <div class="reusable-table-container" style="max-height: calc(80vh - 200px); overflow-y: auto; overflow-x: auto;">
        <!-- Row limit controls -->
      @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table wide-table sticky-first-col" id="purchase-ledger-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-xs">ល.រ</th>
                    <th rowspan="2" class="col-xs">កាលបរិច្ឆេទ</th>
                    <th rowspan="2" class="text-center">លេខវិក្កយបត្រ ឬប្រតិវេទគយ</th>
                    <th colspan="3" class="text-center">អ្នកផ្គត់ផ្គង់</th>
                    <th rowspan="2" class="col-xs">តម្លៃសរុបលើវិក្កយបត្រ</th>
                    <th colspan="2" class="text-center">តម្លៃ​​មិន​រួមអតប និងមិនជាប់​អតប​</th>
                    <th rowspan="2" class="col-xs">អាករលើតម្លៃបន្ថែមអត្រា ០%</th>
                    <th colspan="2" class="text-center">អាករលើតម្លៃបន្ថែម</th>
                    <th colspan="3" class="text-center">អាករលើតម្លៃបន្ថែម(បន្ទុករដ្ឋ)</th>
                    <th rowspan="2" class="col-sm">បរិយាយ</th>
                    <th rowspan="2" class="col-xs">ស្ថានភាពប្រកាសពន្ធ</th>
                </tr>
                <tr>
                    <th class="col-xs">ប្រភេទ</th>
                    <th class="col-sm">លេខសម្គាល់ចុះបញ្ជីពន្ធដារ</th>
                    <th class="col-sm">ឈ្មោះ</th>
                    <th class="col-sm">តម្លៃមិនរួមអតប</th>
                    <th class="col-sm">ការទិញមិនជាប់អតប</th>
                    <th class="col-xs">ការទិញក្នុងស្រុក</th>
                    <th class="col-xs">អតបលើការនាំចូល</th>
                    <th class="col-xs">ការទិញក្នុងស្រុក</th>
                    <th class="col-xs">ការនាំចូល១០%</th>
                    <th class="col-xs">មិនអនុញ្ញាតឥណទាន</th>

                </tr>
            </thead>
            <tbody id="purchase-ledger-table-body">
                <!-- Data will be loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>សរុប៖</strong></td>
                    <td id="total_amount" class="text-right"><strong>0</strong></td>
                    <td id="total_amount_without_vat" class="text-right"><strong>0</strong></td>
                    <td id="total_non_taxable" class="text-right"><strong>0</strong></td>
                    <td id="total_zero_rate" class="text-right"><strong>0</strong></td>
                    <td id="total_domestic_vat" class="text-right"><strong>0</strong></td>
                    <td id="total_import_vat" class="text-right"><strong>0</strong></td>
                    <td id="total_domestic_vat_state" class="text-right"><strong>0</strong></td>
                    <td id="total_import_vat_state" class="text-right"><strong>0</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

    <script>
        const tablename = "#purchase-ledger-table";
        const reportname = "របាយការណ៍ទិញប្រចាំខែ";
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
            function loadPurchaseLedgerData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_id: $('#location_id').val(),
                    supplier_id: $('#supplier_id').val(),
                    page: currentPage,
                    limit: rowLimit
                };

                $.ajax({
                    url: '{{ action('\Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController@monthlyPurchaseLedger') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // Loading indicator
                        $('#purchase-ledger-table-body').html(
                            '<tr><td colspan="16" class="text-center">កំពុងផ្ទុកទិន្នន័យ...</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#purchase-ledger-table-body');
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
                                    $('<td>').text(row.purchase_date),
                                    $('<td>').text(row.invoice_number),
                                    $('<td>').text(row.supplier_type),
                                    $('<td>').text(row.tax_id),
                                    $('<td>').text(row.supplier_name),
                                    $('<td>').text(__currency_trans_from_en(row.total_amount, false)),
                                    $('<td>').text(__currency_trans_from_en(row.amount_without_vat, false)),
                                    $('<td>').text(__currency_trans_from_en(row.non_taxable_amount, false)),
                                    $('<td>').text(__currency_trans_from_en(row.zero_rate_vat, false)),
                                    $('<td>').text(__currency_trans_from_en(row.domestic_vat, false)),
                                    $('<td>').text(__currency_trans_from_en(row.import_vat_10, false)),
                                    $('<td>').text(0),
                                    $('<td>').text(__currency_trans_from_en(row.domestic_vat_state, false)),
                                    $('<td>').text(__currency_trans_from_en(row.import_vat_10_state, false)),
                                    $('<td>').text(row.description),
                                    $('<td>').text(row.tax_declaration_status)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.html(
                                '<tr><td colspan="16" class="text-center">មិនមានទិន្នន័យសម្រាប់ការចម្រាញ់​ជ្រើសរើស​។</td></tr>'
                            );
                        }

                        // Update totals
                        $('#total_amount').html('<strong>' + __currency_trans_from_en(response.total_amount || 0, false) + '</strong>');
                        $('#total_amount_without_vat').html('<strong>' + __currency_trans_from_en(response.total_amount_without_vat || 0, false) + '</strong>');
                        $('#total_non_taxable').html('<strong>' + __currency_trans_from_en(response.total_non_taxable || 0, false) + '</strong>');
                        $('#total_zero_rate').html('<strong>' + __currency_trans_from_en(0, false) + '</strong>');
                        $('#total_domestic_vat').html('<strong>' + __currency_trans_from_en(response.total_domestic_vat || 0, false) + '</strong>');
                        $('#total_import_vat').html('<strong>' + __currency_trans_from_en(0, false) + '</strong>');
                        $('#total_domestic_vat_state').html('<strong>' + __currency_trans_from_en(0, false) + '</strong>');
                        $('#total_import_vat_state').html('<strong>' + __currency_trans_from_en(0, false) + '</strong>');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#purchase-ledger-table-body').html(
                            '<tr><td colspan="16" class="text-center text-danger">មានបញ្ហាក្នុងការទាញយកទិន្នន័យ។ សូមព្យាយាមម្តងទៀត។</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadPurchaseLedgerData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadPurchaseLedgerData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadPurchaseLedgerData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadPurchaseLedgerData();
                }
            });

            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    currentPage = 1; // Reset to first page when changing filters
                    loadPurchaseLedgerData(); // Reload data for non-custom range selections
                }
            });

            // Event listeners for location and supplier filter changes
            $('#location_id, #supplier_id').on('change', function() {
                currentPage = 1; // Reset to first page when changing filters
                loadPurchaseLedgerData();
            });

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to first page when changing filters
                    loadPurchaseLedgerData();
                } else {
                    alert('Please select both start and end dates.');
                }
            });
        });
    </script>

@endsection 
