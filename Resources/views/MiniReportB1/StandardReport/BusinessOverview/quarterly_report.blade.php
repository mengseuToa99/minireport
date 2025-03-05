@extends('layouts.app')

@section('title', __('report.customer') . ' - ' . __('report.supplier') . ' ' . __('report.reports'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>របាយការណ៍ប្រចាំត្រីមាស</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                @component('components.filters', ['title' => __('report.filters')])
                    <div class="row">
                        <!-- Customer Group -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('cg_customer_group_id', __('lang_v1.customer_group_name') . ':') !!}
                                {!! Form::select('cnt_customer_group_id', $customer_group, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'id' => 'cnt_customer_group_id',
                                    'placeholder' => __('lang_v1.all'),
                                ]) !!}
                            </div>
                        </div>

                        <!-- Contact Type -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('type', __('lang_v1.type') . ':') !!}
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
                                {!! Form::label('cs_report_location_id', __('sale.location') . ':') !!}
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
                                {!! Form::label('scr_contact_id', __('report.contact') . ':', ['class' => 'mb-2 mb-md-0 mr-md-2']) !!}
                                {!! Form::select('scr_contact_id', $contact_dropdown, null, [
                                    'class' => 'form-control select2 w-100',
                                    'id' => 'scr_contact_id',
                                    'placeholder' => __('lang_v1.all'),
                                ]) !!}
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <div class="form-group">
                                {!! Form::label('scr_date_filter', __('report.date_range') . ':') !!}
                                {!! Form::text('date_range', null, [
                                    'placeholder' => __('lang_v1.select_a_date_range'),
                                    'class' => 'form-control',
                                    'id' => 'scr_date_filter',
                                    'readonly',
                                ]) !!}
                            </div>
                        </div>

                        <!-- Total Sale Filter -->
                        <div class="col-12 col-md-6 col-lg-6 mb-6">
                            <div class="form-group" style="display: flex;">
                                {!! Form::label('total_sale_filter', __('Total Sale Greater Than') . ':') !!}
                                {!! Form::number('total_sale_filter', null, [
                                    'class' => 'form-control',
                                    'id' => 'total_sale_filter',
                                    'placeholder' => __('Enter amount'),
                                ]) !!}
                      
                                <button class="btn btn-primary" style="margin-left: 8px">
                                    <i class="fa fa-search"></i> @lang('search')
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
                            <i class="fa fa-print"></i> @lang('messages.print')
                        </button>
                    </div>

                    <div class="reusable-table">
                        <table class="reusable-table-container" id="supplier_report_tbl">
                            <thead>
                                <tr>
                                    <th>@lang('No.')</th> <!-- Auto-numbering column -->
                                    <th>@lang('report.contact')</th>
                                    <th>@lang('contact.mobile')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('State')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('report.total_sell')</th>
                                    <th>@lang('lang_v1.opening_balance_due')</th>
                                    <th>@lang('report.total_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print"
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
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Last 3 Months': [moment().subtract(2, 'months').startOf('month'), moment().endOf('month')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Month Last Year': [moment().subtract(1, 'year').startOf('month'), moment().subtract(1, 'year').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                    'Current Financial Year': [moment().month(3).startOf('month'), moment().month(2).endOf('month').add(1, 'year')],
                    'Last Financial Year': [moment().month(3).startOf('month').subtract(1, 'year'), moment().month(2).endOf('month')],
                    'Custom Range': [moment().subtract(30, 'days'), moment()]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear',
                    customRangeLabel: 'Custom Range'
                }
            });

            // Update the input field when a range is selected
            $('#scr_date_filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                supplierReportTable.ajax.reload();
            });

            // Clear the input field when the "Clear" button is clicked
            $('#scr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                supplierReportTable.ajax.reload();
            });

            // Initialize DataTable
            var supplierReportTable = $('#supplier_report_tbl').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('sr_quarterly_report') }}',
                    type: 'GET',
                    data: function(d) {
                        d.customer_group_id = $('#cnt_customer_group_id').val();
                        d.contact_type = $('#contact_type').val();
                        d.location_id = $('#cs_report_location_id').val();
                        d.contact_id = $('#scr_contact_id').val();
                        d.date_range = $('#scr_date_filter').val();
                        d.total_sale_filter = $('#total_sale_filter').val();
                    }
                },
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                columns: [
                    {
                        data: null,
                        name: 'no',
                        render: function(data, type, row, meta) {
                            // Auto-numbering based on row index
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="print-only">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    { data: 'mobile', name: 'mobile' },
                    { data: 'city', name: 'city' },
                    { data: 'state', name: 'state' },
                    { data: 'country', name: 'country' },
                    { data: 'total_invoice', name: 'total_invoice' },
                    { data: 'opening_balance_due', name: 'opening_balance_due' },
                    { data: 'due', name: 'due' }
                ]
            });

            // Apply filters on change
            $('#cnt_customer_group_id, #contact_type, #cs_report_location_id, #scr_contact_id, #total_sale_filter').on(
                'change',
                function() {
                    supplierReportTable.ajax.reload();
                });

            
        });
    </script>
      <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
