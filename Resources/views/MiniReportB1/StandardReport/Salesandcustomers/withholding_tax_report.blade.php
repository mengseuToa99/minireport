@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.withholding_tax_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')

    <div style="margin: 16px" class="no-print">

@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

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
                    <div class="form-group mt-3">
                        {!! Form::label('tax_type', __('minireportb1::minireportb1.tax_residence_type') . ':') !!}
                        {!! Form::select('tax_type', $tax_types, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent

    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.withholding_tax_report')])

    <div class="reusable-table-container">
        <!-- Row limit controls -->
        @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table wide-table sticky-first-col" id="withholding-tax-table">
            <thead>
                <tr>
                    <th class="col-xs" rowspan="3">ល.រ</th>
                    <th class="col-xs" rowspan="3">កាលបរិច្ឆេទ</th>
                    <th class="text-center" rowspan="3">លេខវិក្កយបត្រ</th>
                    <th class="text-center" colspan="3">អ្នកទទួលប្រាក់</th>
                    <th class="col-xs" rowspan="3">ទឹកប្រាក់ត្រូវបើក</th>
                    <th class="text-center" colspan="6">ពន្ធកាត់ទុកលើនិវាសនជន</th>
                    <th class="text-center" colspan="5">ពន្ធកាត់ទុកលើអនិវាសនជន</th>
                </tr>
                <tr>
                    <th class="text-center" rowspan="2">ប្រភេទ</th>
                    <th class="text-center" rowspan="2">លេខសំគាល់ចុះបញ្ជី</th>
                    <th class="text-center" rowspan="2">ឈ្មោះ</th>
                    <th class="text-center">ការបំពេញសេវានានា សួយសារចំពោះទ្រព្យ អរូបីភាគកម្មក្នុងធនធានរ៉ែ</th>
                    <th class="text-center">ការបង់ការប្រាក់ ឲ្យអ្នកជាប់ពន្ធមិនមែនធនាគា</th>
                    <th class="text-center">ការបង់ការប្រាក់ ឲ្យអ្នកជាប់ពន្ធដែលមានគណនីសន្សំមានកាលកំណត់</th>
                    <th class="text-center">ការបង់ការប្រាក់ ឲ្យអ្នកជាប់ពន្ធដែលមានគណនីសន្សំគ្មានកាលកំណត់</th>
                    <th class="text-center">ការបង់ថ្លៃឈ្នួលចលនទ្រព្យ និង អចលនទ្រព្យ(នីតិបុគ្គល)</th>
                    <th class="text-center">ការបង់ថ្លៃឈ្នួលចលនទ្រព្យ និង អចលនទ្រព្យ(រូបវន្តបុគ្គល)</th>
                    <th class="text-center">ការបង់ការប្រាក់</th>
                    <th class="text-center">ការបង់សួយសារ ថ្លៃឈ្នួល ចំណូលផ្សេងៗទាក់ទិន នឹងការប្រើប្រាស់ទ្រព្យសម្បត្តិ</th>
                    <th class="text-center">ការទូទាត់ថ្លៃសេវាគ្រប់គ្រង និងសេវាបច្ចេកទេសនានា</th>
                    <th class="text-center">ការបង់ភាគលាភ</th>
                    <th class="text-center">សេវាកម្ម</th>
                </tr>
                <tr>
                    <th class="text-center">១៥%</th>
                    <th class="text-center">១៥%</th>
                    <th class="text-center">៦%</th>
                    <th class="text-center">៤%</th>
                    <th class="text-center">១០%</th>
                    <th class="text-center">១០%</th>
                    <th class="text-center">១៤%</th>
                    <th class="text-center">១៤%</th>
                    <th class="text-center">១៤%</th>
                    <th class="text-center">១៤%</th>
                    <th class="text-center">១៤%</th>
                </tr>
                <tr>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center">('១)</th>
                    <th class="text-center">('២)</th>
                    <th class="text-center">('៣)</th>
                    <th class="text-center">('៤)</th>
                    <th class="text-center">('៥)</th>
                    <th class="text-center">('៦)</th>
                    <th class="text-center">('១)</th>
                    <th class="text-center">('២)</th>
                    <th class="text-center">('៣)</th>
                    <th class="text-center">('៤)</th>
                    <th class="text-center">('៥)</th>
                </tr>
            </thead>
            <tbody id="withholding-tax-table-body">
                <!-- Data will be loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>សរុប៖</strong></td>
                    <td id="total_payable_amount" class="text-right"><strong>0</strong></td>
                    <td id="category_1" class="text-right"><strong>0</strong></td>
                    <td id="category_2" class="text-right"><strong>0</strong></td>
                    <td id="category_3" class="text-right"><strong>0</strong></td>
                    <td id="category_4" class="text-right"><strong>0</strong></td>
                    <td id="category_5" class="text-right"><strong>0</strong></td>
                    <td id="category_6" class="text-right"><strong>0</strong></td>
                    <td id="non_res_1" class="text-right"><strong>0</strong></td>
                    <td id="non_res_2" class="text-right"><strong>0</strong></td>
                    <td id="non_res_3" class="text-right"><strong>0</strong></td>
                    <td id="non_res_4" class="text-right"><strong>0</strong></td>
                    <td id="non_res_5" class="text-right"><strong>0</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#withholding-tax-table";
        const reportname = "តារាងប្រកាសពន្ធកាត់ទុក";
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
            function loadWithholdingTaxData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_id: $('#location_id').val(),
                    supplier_id: $('#supplier_id').val(),
                    tax_type: $('#tax_type').val(),
                    page: currentPage,
                    limit: rowLimit
                };

                $.ajax({
                    url: '{{ action('\Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController@withholdingTaxReport') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // Loading indicator
                        $('#withholding-tax-table-body').html(
                            '<tr><td colspan="18" class="text-center">កំពុងផ្ទុកទិន្នន័យ...</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#withholding-tax-table-body');
                        tbody.empty();

                        // Update pagination variables
                        totalPages = response.total_pages || 1;
                        $('#total-pages').text(totalPages);
                        $('#current-page').text(currentPage);
                        
                        // Update pagination buttons
                        $('#prev-page').prop('disabled', currentPage <= 1);
                        $('#next-page').prop('disabled', currentPage >= totalPages);

                        // Initialize category totals
                        let categoryTotals = {
                            category_1: 0,
                            category_2: 0,
                            category_3: 0,
                            category_4: 0,
                            category_5: 0,
                            category_6: 0,
                            non_res_1: 0,
                            non_res_2: 0,
                            non_res_3: 0,
                            non_res_4: 0,
                            non_res_5: 0
                        };

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                // Calculate the global row index across all pages
                                const rowIndex = (currentPage - 1) * rowLimit + index + 1;
                                
                                // Determine which tax category to use based on tax rate and supplier type
                                let tax_amount = parseFloat(row.tax_amount) || 0;
                                let tax_category = '';
                                let tax_col_index = 0;
                                
                                // Default all cells to empty
                                let tax_cells = Array(11).fill('');
                                
                                // Determine which cell to populate based on tax rate and residence type
                                if (row.tax_residence_type.includes('និវាសនជន') || !row.tax_residence_type.includes('អនិវាសនជន')) {
                                    // Resident tax categories
                                    if (row.tax_rate.includes('១៥%') || row.tax_rate.includes('15%')) {
                                        // Service fees or interest categories (1 or 2)
                                        if (row.transaction_type && row.transaction_type.includes('សេវា')) {
                                            tax_cells[0] = __currency_trans_from_en(tax_amount, false);
                                            categoryTotals.category_1 += tax_amount;
                                        } else {
                                            tax_cells[1] = __currency_trans_from_en(tax_amount, false);
                                            categoryTotals.category_2 += tax_amount;
                                        }
                                    } else if (row.tax_rate.includes('៦%') || row.tax_rate.includes('6%')) {
                                        tax_cells[2] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.category_3 += tax_amount;
                                    } else if (row.tax_rate.includes('៤%') || row.tax_rate.includes('4%')) {
                                        tax_cells[3] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.category_4 += tax_amount;
                                    } else if (row.tax_rate.includes('១០%') || row.tax_rate.includes('10%')) {
                                        // Rental for legal person or individual
                                        if (row.contact_type && row.contact_type.includes('នីតិបុគ្គល')) {
                                            tax_cells[4] = __currency_trans_from_en(tax_amount, false);
                                            categoryTotals.category_5 += tax_amount;
                                        } else {
                                            // For individual rental payments, recalculate as 10% of payable amount
                                            let payable_amount = parseFloat(row.payable_amount) || 0;
                                            // Calculate 10% tax amount (consider exchange rate if present)
                                            let exchange_rate = row.exchange_rate || 1;
                                            let calculated_tax = (payable_amount * 0.1) * exchange_rate;
                                            
                                            tax_cells[5] = __currency_trans_from_en(calculated_tax, false);
                                            categoryTotals.category_6 += calculated_tax;
                                        }
                                    }
                                } else {
                                    // Non-resident tax categories
                                    if (row.transaction_type && row.transaction_type.includes('ប្រាក់')) {
                                        tax_cells[6] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.non_res_1 += tax_amount;
                                    } else if (row.transaction_type && row.transaction_type.includes('សួយសារ')) {
                                        tax_cells[7] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.non_res_2 += tax_amount;
                                    } else if (row.transaction_type && row.transaction_type.includes('សេវា')) {
                                        tax_cells[8] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.non_res_3 += tax_amount;
                                    } else if (row.transaction_type && row.transaction_type.includes('ភាគលាភ')) {
                                        tax_cells[9] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.non_res_4 += tax_amount;
                                    } else {
                                        tax_cells[10] = __currency_trans_from_en(tax_amount, false);
                                        categoryTotals.non_res_5 += tax_amount;
                                    }
                                }
                                
                                // Create table row
                                const tr = $('<tr>').append(
                                    $('<td>').text(rowIndex),
                                    $('<td>').text(row.date),
                                    $('<td>').text(row.invoice_no),
                                    $('<td>').text(row.tax_residence_type.includes('និវាសនជន') ? 'និវាសនជន' : 'អនិវាសនជន'),
                                    $('<td>').text(row.tax_number),
                                    $('<td>').text(row.contact_name),
                                    $('<td>').text(__currency_trans_from_en(row.payable_amount, false)),
                                    $('<td>').html(tax_cells[0]),
                                    $('<td>').html(tax_cells[1]),
                                    $('<td>').html(tax_cells[2]),
                                    $('<td>').html(tax_cells[3]),
                                    $('<td>').html(tax_cells[4]),
                                    $('<td>').html(tax_cells[5]),
                                    $('<td>').html(tax_cells[6]),
                                    $('<td>').html(tax_cells[7]),
                                    $('<td>').html(tax_cells[8]),
                                    $('<td>').html(tax_cells[9]),
                                    $('<td>').html(tax_cells[10])
                                );
                                tbody.append(tr);
                            });
                            
                            // Update totals with actual data
                            $('#total_payable_amount').html('<strong>' + __currency_trans_from_en(response.total_payable_amount || 0, false) + '</strong>');
                            
                            // Update category totals
                            $('#category_1').html('<strong>' + __currency_trans_from_en(categoryTotals.category_1, false) + '</strong>');
                            $('#category_2').html('<strong>' + __currency_trans_from_en(categoryTotals.category_2, false) + '</strong>');
                            $('#category_3').html('<strong>' + __currency_trans_from_en(categoryTotals.category_3, false) + '</strong>');
                            $('#category_4').html('<strong>' + __currency_trans_from_en(categoryTotals.category_4, false) + '</strong>');
                            $('#category_5').html('<strong>' + __currency_trans_from_en(categoryTotals.category_5, false) + '</strong>');
                            $('#category_6').html('<strong>' + __currency_trans_from_en(categoryTotals.category_6, false) + '</strong>');
                            $('#non_res_1').html('<strong>' + __currency_trans_from_en(categoryTotals.non_res_1, false) + '</strong>');
                            $('#non_res_2').html('<strong>' + __currency_trans_from_en(categoryTotals.non_res_2, false) + '</strong>');
                            $('#non_res_3').html('<strong>' + __currency_trans_from_en(categoryTotals.non_res_3, false) + '</strong>');
                            $('#non_res_4').html('<strong>' + __currency_trans_from_en(categoryTotals.non_res_4, false) + '</strong>');
                            $('#non_res_5').html('<strong>' + __currency_trans_from_en(categoryTotals.non_res_5, false) + '</strong>');
                        } else {
                            // Display no data message
                            tbody.html(
                                '<tr><td colspan="18" class="text-center">មិនមានទិន្នន័យសម្រាប់ការចម្រាញ់​ជ្រើសរើស​។</td></tr>'
                            );
                            
                            // Reset totals to zero when no data
                            $('#total_payable_amount').html('<strong>' + __currency_trans_from_en(0, false) + '</strong>');
                            
                            // Reset all category totals to zero
                            $('#category_1, #category_2, #category_3, #category_4, #category_5, #category_6')
                                .add('#non_res_1, #non_res_2, #non_res_3, #non_res_4, #non_res_5')
                                .html('<strong>' + __currency_trans_from_en(0, false) + '</strong>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading withholding tax data:', error);
                        
                        $('#withholding-tax-table-body').html(
                            '<tr><td colspan="18" class="text-center text-danger">មានបញ្ហាក្នុងការទាញយកទិន្នន័យ។ សូមព្យាយាមម្តងទៀត។</td></tr>'
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
                    // Only auto-reload if a non-custom value is selected
                    if (dateFilter !== '') {
                        currentPage = 1;
                        loadWithholdingTaxData();
                    }
                }
            }

            // Check if custom_range_inputs exists on page load
            updateDateRangeVisibility();

            // Initial load
            loadWithholdingTaxData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadWithholdingTaxData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadWithholdingTaxData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadWithholdingTaxData();
                }
            });

            // Event listener for date filter changes
            $('#date_filter').on('change', updateDateRangeVisibility);

            // Event listeners for location and supplier filter changes
            $('#location_id, #supplier_id, #tax_type').on('change', function() {
                currentPage = 1; // Reset to first page when changing filters
                loadWithholdingTaxData();
            });

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to first page when changing filters
                    loadWithholdingTaxData();
                } else {
                    alert('Please select both start and end dates.');
                }
            });
        });
    </script>

@endsection 