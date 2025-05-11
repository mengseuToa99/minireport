@extends('layouts.app')

@section('title', 'របាយការណ៍ប្រចាំត្រីមាស (Quarterly Report)')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>របាយការណ៍ប្រចាំត្រីមាស (Quarterly Report)</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                @component('components.filters', ['title' => 'តម្រង (Filters)'])
                    <div class="row">
                        <!-- Customer Group -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('cg_customer_group_id', 'ឈ្មោះក្រុមអតិថិជន (Customer Group Name):') !!}
                                {!! Form::select('cnt_customer_group_id', $customer_group, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'id' => 'cnt_customer_group_id',
                                    'placeholder' => 'ទាំងអស់ (All)',
                                ]) !!}
                            </div>
                        </div>

                        <!-- Contact Type -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('type', 'ប្រភេទ (Type):') !!}
                                {!! Form::select('contact_type', $types, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'id' => 'contact_type',
                                ]) !!}
                            </div>
                        </div>

                        <!-- Business Location -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('cs_report_location_id', 'ទីតាំង (Location):') !!}
                                {!! Form::select('cs_report_location_id', $business_locations, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'id' => 'cs_report_location_id',
                                ]) !!}
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group d-flex flex-column flex-md-row align-items-start align-items-md-center">
                                {!! Form::label('scr_contact_id', 'ទំនាក់ទំនង (Contact):', ['class' => 'mb-2 mb-md-0 mr-md-2']) !!}
                                {!! Form::select('scr_contact_id', $contact_dropdown, null, [
                                    'class' => 'form-control select2 w-100',
                                    'id' => 'scr_contact_id',
                                    'placeholder' => 'ទាំងអស់ (All)',
                                ]) !!}
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('scr_date_filter', 'ចន្លោះកាលបរិច្ឆេទ (Date Range):') !!}
                                {!! Form::text('date_range', null, [
                                    'placeholder' => 'ជ្រើសរើសចន្លោះកាលបរិច្ឆេទ (Select a date range)',
                                    'class' => 'form-control',
                                    'id' => 'scr_date_filter',
                                    'readonly',
                                ]) !!}
                            </div>
                        </div>

                        <!-- Total Sale Filter -->
                        <div class="col-12 col-md-6 col-lg-6 mb-6">
                            <div class="form-group" style="display: flex;">
                                {!! Form::label('total_sale_filter', 'ការលក់សរុបច្រើនជាង (Total Sale Greater Than):') !!}
                                {!! Form::number('total_sale_filter', null, [
                                    'class' => 'form-control',
                                    'id' => 'total_sale_filter',
                                    'placeholder' => 'បញ្ចូលចំនួនទឹកប្រាក់ (Enter amount)',
                                ]) !!}
                      
                                <button class="btn btn-primary" style="margin-left: 8px">
                                    <i class="fa fa-search"></i> ស្វែងរក (Search)
                                </button>
                            
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @component('components.widget', ['class' => 'box-primary'])
                    <!-- Print Button -->
                    <div class="text-right mb-3">
                        <button id="printButton" class="btn btn-primary">
                            <i class="fa fa-print"></i> បោះពុម្ព (Print)
                        </button>
                    </div>

                    <div class="reusable-table">
                        <table class="reusable-table-container" id="supplier_report_tbl">
                            <thead>
                                <tr>
                                    <th>ល.រ (No.)</th> <!-- Auto-numbering column -->
                                    <th>ទំនាក់ទំនង (Contact)</th>
                                    <th>លេខទូរស័ព្ទ (Mobile)</th>
                                    <th>ទីក្រុង (City)</th>
                                    <th>ខេត្ត/រដ្ឋ (State)</th>
                                    <th>ប្រទេស (Country)</th>
                                    <th>ការលក់សរុប (Total Sell)</th>
                                    <th>សមតុល្យដើមគ្រាដែលជំពាក់ (Opening Balance Due)</th>
                                    <th>ទឹកប្រាក់ជំពាក់សរុប (Total Due) &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print"
                                            data-toggle="tooltip" data-placement="bottom" data-html="true"
                                            data-original-title="{{ __('messages.due_tooltip') }}" aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('javascript')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <!-- Date Range Picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: inline;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            a {
                text-decoration: none !important; /* Remove underline from links */
            }
        }
    </style>

    <script>
        const tablename = "#supplier_report_tbl";
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Initialize date range picker with predefined ranges
            $('#scr_date_filter').daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                ranges: {
                    'ថ្ងៃនេះ (Today)': [moment(), moment()],
                    'ម្សិលមិញ (Yesterday)': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '៧ ថ្ងៃចុងក្រោយ (Last 7 Days)': [moment().subtract(6, 'days'), moment()],
                    '៣០ ថ្ងៃចុងក្រោយ (Last 30 Days)': [moment().subtract(29, 'days'), moment()],
                    '៣ ខែចុងក្រោយ (Last 3 Months)': [moment().subtract(2, 'months').startOf('month'), moment().endOf('month')],
                    'ខែនេះ (This Month)': [moment().startOf('month'), moment().endOf('month')],
                    'ខែមុន (Last Month)': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'ខែនេះឆ្នាំមុន (This Month Last Year)': [moment().subtract(1, 'year').startOf('month'), moment().subtract(1, 'year').endOf('month')],
                    'ឆ្នាំនេះ (This Year)': [moment().startOf('year'), moment().endOf('year')],
                    'ឆ្នាំមុន (Last Year)': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                }
            });

            // Handle date range selection
            $('#scr_date_filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                supplier_report_table.ajax.reload();
            });

            // Handle date range clear
            $('#scr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                supplier_report_table.ajax.reload();
            });

            // Print button functionality
            $('#printButton').click(function() {
                window.print();
            });

            // Initialize DataTable
            let supplier_report_table = $('#supplier_report_tbl').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\MiniReportB1\Http\Controllers\StandardReport\ProductReportController::class, 'quarterly']) }}",
                    data: function(d) {
                        d.customer_group_id = $('#cnt_customer_group_id').val();
                        d.contact_type = $('#contact_type').val();
                        d.location_id = $('#cs_report_location_id').val();
                        d.contact_id = $('#scr_contact_id').val();
                        
                        // Get selected date range
                        if($('#scr_date_filter').val()) {
                            const start_date = $('#scr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            const end_date = $('#scr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start_date;
                            d.end_date = end_date;
                        }
                        
                        d.total_sale_filter = $('#total_sale_filter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'name', name: 'contacts.name' },
                    { data: 'mobile', name: 'contacts.mobile' },
                    { data: 'city', name: 'contacts.city' },
                    { data: 'state', name: 'contacts.state' },
                    { data: 'country', name: 'contacts.country' },
                    { data: 'total_sell', name: 'total_sell', searchable: false },
                    { data: 'opening_balance_due', name: 'opening_balance_due', searchable: false },
                    { data: 'due', name: 'due', searchable: false }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#supplier_report_tbl'));
                    // Add responsive class to table
                    $('#supplier_report_tbl').addClass('table-responsive');
                }
            });

            // Event handlers for filter changes
            $('#cnt_customer_group_id, #contact_type, #cs_report_location_id, #scr_contact_id, #total_sale_filter').change(function() {
                supplier_report_table.ajax.reload();
            });
        });
    </script>
@endsection
