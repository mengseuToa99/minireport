@extends('minireportb1::layouts.master2')
@section('title', __('lang_v1.all_sales'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @isset($file_name)
                {{ $file_name }} <!-- Display the file name if it exists -->
            @else
                @lang('sale.sells') <!-- Fall back to the default title -->
            @endisset
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">


        @if (!isset($file_name) || empty($file_name))
            @include('minireportb1::MiniReportB1.multitable.partials.dropdown')
        @endif

        @component('components.filters', ['title' => __('report.filters')])
            @include('sell.partials.sell_list_filters')
            @if ($payment_types)
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('lang_v1.payment_method') . ':') !!}
                        {!! Form::select('payment_method', $payment_types, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

            @if (!empty($sources))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_source', __('lang_v1.sources') . ':') !!}

                        {!! Form::select('sell_list_filter_source', $sources, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
            @can('direct_sell.access')
                @slot('tool')
                    <div class="box-tools">
                        {{-- <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                            href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a> --}}
                    </div>
                @endslot
            @endcan
            @if (auth()->user()->can('direct_sell.view') ||
                    auth()->user()->can('view_own_sell_only') ||
                    auth()->user()->can('view_commission_agent_sell'))
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                @endphp
                <table class="table table-bordered table-striped ajax_view" id="sell_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>#</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('lang_v1.contact_no')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('lang_v1.payment_method')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.sell_return_due')</th>
                            <th>@lang('lang_v1.shipping_status')</th>
                            <th>@lang('lang_v1.total_items')</th>
                            <th>@lang('lang_v1.types_of_service')</th>
                            <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') }}
                            </th>
                            <th>{{ $custom_labels['sell']['custom_field_1'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_2'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_3'] ?? '' }}</th>
                            <th>{{ $custom_labels['sell']['custom_field_4'] ?? '' }}</th>
                            <th>@lang('lang_v1.added_by')</th>
                            <th>@lang('sale.sell_note')</th>
                            <th>@lang('sale.staff_note')</th>
                            <th>@lang('sale.shipping_details')</th>
                            <th>@lang('restaurant.table')</th>
                            <th>@lang('restaurant.service_staff')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_payment_status_count"></td>
                            <td class="payment_method_count"></td>
                            <td class="footer_sale_total"></td>
                            <td class="footer_total_paid"></td>
                            <td class="footer_total_remaining"></td>
                            <td class="footer_total_sell_return_due"></td>
                            <td colspan="2"></td>
                            <td class="service_type_count"></td>
                            <td colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        @endcomponent
    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>

@stop

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('javascript')
    <script type="text/javascript">
        var visibleColumnNames = @json($visibleColumnNames ?? []); // This is correct
        var isViewMode = @json(isset($file_name));
        var tablename = "#sell_table";
        var reportName = "saleReport";
        var filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format; // Assuming this is defined globally
        const dateSeparator = ' - ';
        var d = {}

        console.log("filter:", filterCriteria);

        if (filterCriteria.dateRange) {
            // For payroll, dateRange is expected to be in 'mm/yyyy' format
            const [month, year] = filterCriteria.dateRange.split('/');
            d.month = month; // Add month to the data object
            d.year = year; // Add year to the data object
        }
        if (filterCriteria.locationId) {
            d.location_id = filterCriteria.locationId; // Add location ID to the data object
        }
        if (filterCriteria.userId) {
            d.user_id = filterCriteria.userId; // Add user ID to the data object
        }
        if (filterCriteria.departmentId) {
            d.department_id = filterCriteria.departmentId; // Add department ID to the data object
        }
        if (filterCriteria.designationId) {
            d.designation_id = filterCriteria.designationId; // Add designation ID to the data object
        }

        // Other filters
        d.is_direct_sale = 1;
        d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
        d.service_staffs = $('#service_staffs').val();

        if ($('#shipping_status').length) {
            d.shipping_status = $('#shipping_status').val();
        }

        if ($('#sell_list_filter_source').length) {
            d.source = $('#sell_list_filter_source').val();
        }

        if ($('#only_subscriptions').is(':checked')) {
            d.only_subscriptions = 1;
        }

        if ($('#payment_method').length) {
            d.payment_method = $('#payment_method').val();
        }


        $(document).ready(function() {
            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    if (typeof dateRangeSettings === 'undefined') {
                        var ranges = {};
                        ranges[LANG.today] = [moment(), moment()];
                        ranges[LANG.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
                        ranges[LANG.last_7_days] = [moment().subtract(6, 'days'), moment()];
                        ranges[LANG.last_30_days] = [moment().subtract(29, 'days'), moment()];
                        ranges[LANG.this_month] = [moment().startOf('month'), moment().endOf('month')];
                        ranges[LANG.last_month] = [
                            moment().subtract(1, 'month').startOf('month'),
                            moment().subtract(1, 'month').endOf('month')
                        ];
                        ranges[LANG.this_month_last_year] = [
                            moment().subtract(1, 'year').startOf('month'),
                            moment().subtract(1, 'year').endOf('month')
                        ];
                        ranges[LANG.this_year] = [moment().startOf('year'), moment().endOf('year')];
                        ranges[LANG.last_year] = [
                            moment().startOf('year').subtract(1, 'year'),
                            moment().endOf('year').subtract(1, 'year')
                        ];
                        ranges[LANG.this_financial_year] = [financial_year.start, financial_year.end];
                        ranges[LANG.last_financial_year] = [
                            moment(financial_year.start._i).subtract(1, 'year'),
                            moment(financial_year.end._i).subtract(1, 'year')
                        ];

                        window.dateRangeSettings = {
                            ranges: ranges,
                            startDate: financial_year.start,
                            endDate: financial_year.end,
                            locale: {
                                cancelLabel: LANG.clear,
                                applyLabel: LANG.apply,
                                customRangeLabel: LANG.custom_range,
                                format: moment_date_format,
                                toLabel: '~'
                            }
                        };
                    }

                    if ($('#sell_list_filter_date_range').length) {
                        $('#sell_list_filter_date_range').daterangepicker(
                            dateRangeSettings,
                            function(start, end) {
                                $('#sell_list_filter_date_range').val(start.format(moment_date_format) +
                                    ' ~ ' + end.format(moment_date_format));
                                sell_table.ajax.reload();
                            }
                        );
                        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                            $('#sell_list_filter_date_range').val('');
                            sell_table.ajax.reload();
                        });
                    }

                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    sell_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sell_table.ajax.reload();
            });

            sell_table = $('#sell_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                aaSorting: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": "/sells",
                    "data": function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.is_direct_sale = 1;

                        d.location_id = $('#sell_list_filter_location_id').val();
                        d.customer_id = $('#sell_list_filter_customer_id').val();
                        d.payment_status = $('#sell_list_filter_payment_status').val();
                        d.created_by = $('#created_by').val();
                        d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                        d.service_staffs = $('#service_staffs').val();

                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }

                        if ($('#sell_list_filter_source').length) {
                            d.source = $('#sell_list_filter_source').val();
                        }

                        if ($('#only_subscriptions').is(':checked')) {
                            d.only_subscriptions = 1;
                        }

                        if ($('#payment_method').length) {
                            d.payment_method = $('#payment_method').val();
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: isViewMode ? visibleColumnNames.includes("Action") ||
                            visibleColumnNames.includes("លំអិត") : true
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date',
                        visible: isViewMode ? visibleColumnNames.includes("Date") || visibleColumnNames
                            .includes("ថ្ងៃខែឆ្នាំម៉ោង") : true
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                        visible: isViewMode ? visibleColumnNames.includes("Invoice No.") ||
                            visibleColumnNames.includes("លេខ") : true
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name',
                        visible: isViewMode ? visibleColumnNames.includes("Customer name") ||
                            visibleColumnNames.includes("ឈ្មោះ") : true
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile',
                        visible: isViewMode ? visibleColumnNames.includes("Contact Number") ||
                            visibleColumnNames.includes("ទូរស័ព្ទ") : true
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name',
                        visible: isViewMode ? visibleColumnNames.includes("Location") ||
                            visibleColumnNames.includes("សាខា") : true
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        visible: isViewMode ? visibleColumnNames.includes("Payment Status") ||
                            visibleColumnNames.includes("ស្ថានភាពប្រាក់") : true
                    },
                    {
                        data: 'payment_methods',
                        orderable: false,
                        searchable: false,
                        visible: isViewMode ? visibleColumnNames.includes("Payment Method") ||
                            visibleColumnNames.includes("មធ្យោបាយបង់ប្រាក់") : true
                    },
                    {
                        data: 'final_total',
                        name: 'final_total',
                        visible: isViewMode ? visibleColumnNames.includes("Total Amount") ||
                            visibleColumnNames.includes("សរុប") : true
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        searchable: false,
                        visible: isViewMode ? visibleColumnNames.includes("Total Paid") ||
                            visibleColumnNames.includes("សរុបបានបង់") : true
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining',
                        visible: isViewMode ? visibleColumnNames.includes("Total Due") ||
                            visibleColumnNames.includes("ជំពាក់") : true
                    },
                    {
                        data: 'return_due',
                        orderable: false,
                        searchable: false,
                        visible: isViewMode ? visibleColumnNames.includes("Return Due") ||
                            visibleColumnNames.includes("ទិញវិញជំពាក់") : true
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status',
                        visible: isViewMode ? visibleColumnNames.includes("Shipping Status") ||
                            visibleColumnNames.includes("ស្ថានភាពដឹក") : true
                    },
                    {
                        data: 'total_items',
                        name: 'total_items',
                        searchable: false,
                        visible: isViewMode ? visibleColumnNames.includes("Total Items") ||
                            visibleColumnNames.includes("មុខទំនិញ") : true
                    },
                    {
                        data: 'types_of_service_name',
                        name: 'tos.name',
                        visible: isViewMode ? visibleColumnNames.includes("Types of Service") ||
                            visibleColumnNames.includes("ប្រភេទនៃសេវាកម្ម") : true
                    },
                    {
                        data: 'service_custom_field_1',
                        name: 'service_custom_field_1',
                        visible: isViewMode ? visibleColumnNames.includes("Service Custom Field 1") ||
                            visibleColumnNames.includes("តារាងបន្ថែម 1") : true
                    },
                    {
                        data: 'custom_field_1',
                        name: 'transactions.custom_field_1',
                        visible: isViewMode ? visibleColumnNames.includes("Custom Field 1") ||
                            visibleColumnNames.includes("") : true
                    },
                    {
                        data: 'custom_field_2',
                        name: 'transactions.custom_field_2',
                        visible: isViewMode ? visibleColumnNames.includes("Custom Field 2") ||
                            visibleColumnNames.includes("") : true
                    },
                    {
                        data: 'custom_field_3',
                        name: 'transactions.custom_field_3',
                        visible: isViewMode ? visibleColumnNames.includes("Custom Field 3") ||
                            visibleColumnNames.includes("") : true
                    },
                    {
                        data: 'custom_field_4',
                        name: 'transactions.custom_field_4',
                        visible: isViewMode ? visibleColumnNames.includes("Custom Field 4") ||
                            visibleColumnNames.includes("") : true
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name',
                        visible: isViewMode ? visibleColumnNames.includes("Added By") ||
                            visibleColumnNames.includes("ទិន្នន័យ") : true
                    },
                    {
                        data: 'additional_notes',
                        name: 'additional_notes',
                        visible: isViewMode ? visibleColumnNames.includes("Sell note") ||
                            visibleColumnNames.includes("ចំណាំ") : true
                    },
                    {
                        data: 'staff_note',
                        name: 'staff_note',
                        visible: isViewMode ? visibleColumnNames.includes("Staff note") ||
                            visibleColumnNames.includes("កំណត់ត្រាបុគ្គលិក") : true
                    },
                    {
                        data: 'shipping_details',
                        name: 'shipping_details',
                        visible: isViewMode ? visibleColumnNames.includes("Shipping Details") ||
                            visibleColumnNames.includes("លំអិត") : true
                    },
                    {
                        data: 'table_name',
                        name: 'tables.name',
                        visible: isViewMode ? visibleColumnNames.includes("Table") || visibleColumnNames
                            .includes("តុ") : true
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        visible: isViewMode ? visibleColumnNames.includes("Service Staff") ||
                            visibleColumnNames.includes("បុគ្គលិកប្រចាំការ") : true
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                "footerCallback": function(row, data, start, end, display) {
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;
                    for (var r in data) {
                        footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(
                            data[r].final_total).data('orig-value')) : 0;
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(
                            data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ?
                            parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due')
                            .data('orig-value') ? parseFloat($(data[r].return_due).find(
                                '.sell_return_due').data('orig-value')) : 0;
                    }

                    $('.footer_total_sell_return_due').html(__currency_trans_from_en(
                        footer_total_sell_return_due));
                    $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                    $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                    $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

                    $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                    $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                    $('.payment_method_count').html(__count_status(data, 'payment_methods'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source, #payment_method',
                function() {
                    sell_table.ajax.reload();
                });

            $('#only_subscriptions').on('ifChanged', function(event) {
                sell_table.ajax.reload();
            });


            function applySavedFilters() {
                if (!isViewMode) return;

                console.log('Applying saved filters:', filterCriteria);

                // Apply date range filter
                if (filterCriteria.dateRange) {
                    const [startDate, endDate] = filterCriteria.dateRange.split(dateSeparator);
                    if (moment(startDate, dateFormat).isValid() && moment(endDate, dateFormat).isValid()) {
                        console.log('Setting date range:', filterCriteria.dateRange);
                        $('#sell_list_filter_date_range').val(filterCriteria.dateRange);
                        $('#sell_list_filter_date_range').data('daterangepicker').setStartDate(startDate);
                        $('#sell_list_filter_date_range').data('daterangepicker').setEndDate(endDate);
                    }
                }

                // Map filter keys to their corresponding DOM selectors
                const filterMap = {
                    locationId: '#sell_list_filter_location_id',
                    customerId: '#sell_list_filter_customer_id',
                    paymentStatus: '#sell_list_filter_payment_status',
                    createdBy: '#created_by',
                    salesCmsnAgnt: '#sales_cmsn_agnt',
                    serviceStaffs: '#service_staffs',
                    shippingStatus: '#shipping_status'
                };

                // Apply filters from filterCriteria to the DOM elements
                Object.entries(filterMap).forEach(([key, selector]) => {
                    if (filterCriteria[key]) {
                        console.log(`Applying filter ${key}:`, filterCriteria[key]);
                        $(selector).val(filterCriteria[key]).trigger('change');
                    }
                });

                // // Apply subscription filter if applicable
                // if (filterCriteria.onlySubscriptions) {
                //     console.log('Enabling only_subscriptions filter');
                //     $('#only_subscriptions').iCheck('check');
                // }

                // Manually reload the DataTable
                console.log('Reloading DataTable...');
                sell_table.ajax.reload();
            }

            // Initialize date range picker (required for date range filter)
            function initializeDateRangePicker() {
                $('#sell_list_filter_date_range').daterangepicker(
                    dateRangeSettings, // Assuming this is defined globally
                    function(start, end) {
                        const displayDate = start.format(dateFormat) + dateSeparator + end.format(dateFormat);
                        $(this).val(displayDate);
                        validateAndApplyDateRange(start, end);
                    }
                ).on('cancel.daterangepicker', function(ev) {
                    $(this).val('');
                    sell_table.ajax.reload(); // Assuming sell_table is the DataTable instance
                });
            }

            // Validate and apply date range (helper function for date range picker)
            function validateAndApplyDateRange(start, end) {
                if (start.isValid() && end.isValid()) {
                    sell_table.ajax.reload(); // Reload DataTable with new date range
                } else {
                    toastr.error('Invalid date range selected');
                    $(this).val('');
                }
            }

            // Call the function to apply filters when the page loads
            $(document).ready(function() {
                initializeDateRangePicker(); // Initialize date range picker
                applySavedFilters(); // Apply saved filters
            })
        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>


@endsection
