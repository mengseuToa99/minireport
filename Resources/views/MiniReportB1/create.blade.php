@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

<style>
    #sell_table th {
        cursor: move;
        position: relative;
        padding-right: 25px !important;
    }

    #sell_table th::after {
        content: '⋮';
        /* Vertical dots for drag handle */
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.5;
        cursor: move;
    }

    #sell_table th:hover::after {
        opacity: 1;
    }

    .column-dragging {
        opacity: 0.5;
        background: #e9ecef !important;
    }

    .column-drag-over {
        border-left: 2px solid #4CAF50;
    }

    .column-dragging {
        opacity: 0.5;
        background: #e9ecef !important;
    }

    .column-drag-over {
        border-left: 2px solid #4CAF50;
    }

    th .grip-icon {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        transition: opacity 0.2s ease;
        color: #666;
        cursor: move;
    }

    th:hover .grip-icon {
        opacity: 1;
    }

    .table-container {
        overflow-x: auto;
        max-width: 100%;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table th {
        background-color: #f8fafc;
        font-weight: 600;
        text-align: left;
        position: relative;
        user-select: none;
        cursor: move;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    #sell_table th {
        cursor: move;
        position: relative;
        padding-right: 25px !important;
    }

    #sell_table th::after {
        content: '⋮';
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.5;
        cursor: move;
    }

    #sell_table th:hover::after {
        opacity: 1;
    }

    .column-dragging {
        opacity: 0.5;
        background: #e9ecef !important;
    }

    .column-drag-over {
        border-left: 2px solid #4CAF50;
    }

    .table-container {
        overflow-x: auto;
        max-width: 100%;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table th {
        background-color: #f8fafc;
        font-weight: 600;
        text-align: left;
        position: relative;
        user-select: none;
        cursor: move;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

@section('content')
    <form id="createFileForm" class="mb-3">

        @csrf
        <div class="row">
            <!-- File Name Input -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fileName">File Name</label>
                    <input type="text" class="form-control" id="fileName" name="file_name" required>
                </div>
            </div>

            <!-- Parent Folder Dropdown -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="parentFolder">Select Session</label>
                    <select class="form-control" id="parentFolder" name="parent_id" required>
                        @foreach ($folders->where('type', 'report_section') as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->folder_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save mr-2"></i> Save
                </button>
            </div>
        </div>
    </form>


    @component('components.filters', ['title' => __('report.filters')])
        @include('sell.partials.sell_list_filters')
        @if ($business_locations)
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('business.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                    ]) !!}
                </div>
            </div>
        @endif

        @if ($customers)
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                    {!! Form::select('customer_id', $customers, null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                    ]) !!}
                </div>
            </div>
        @endif

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
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('sale.sells')
            <!-- Add this button near the top of your content section -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#columnVisibilityModal">
                <i class="fas fa-columns"></i> Show/Hide Columns
            </button>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
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

        <!-- Column Visibility Modal -->
        <div class="modal fade" id="columnVisibilityModal" tabindex="-1" role="dialog"
            aria-labelledby="columnVisibilityModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="columnVisibilityModalLabel">Show/Hide Columns</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="columnVisibilityForm">
                            <!-- Static checkboxes for each column -->
                            @foreach ([
            'Action' => 0,
            'Date' => 1,
            'Invoice No' => 2,
            'Customer Name' => 3,
            'Contact No' => 4,
            'Location' => 5,
            'Payment Status' => 6,
            'Payment Method' => 7,
            'Total Amount' => 8,
            'Total Paid' => 9,
            'Sell Due' => 10,
            'Sell Return Due' => 11,
            'Shipping Status' => 12,
            'Total Items' => 13,
            'Types of Service' => 14,
            $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') => 15,
            $custom_labels['sell']['custom_field_1'] ?? '' => 16,
            $custom_labels['sell']['custom_field_2'] ?? '' => 17,
            $custom_labels['sell']['custom_field_3'] ?? '' => 18,
            $custom_labels['sell']['custom_field_4'] ?? '' => 19,
            'Added By' => 20,
            'Sell Note' => 21,
            'Staff Note' => 22,
            'Shipping Details' => 23,
            'Table' => 24,
            'Service Staff' => 25,
        ] as $columnName => $columnIndex)
                                @if (!empty($columnName))
                                    <div class="form-check" style="margin-top: 20px; margin-left: 20px">
                                        <!-- Adjust margin-top value here -->
                                        <input class="form-check-input" type="checkbox"
                                            style="transform: scale(2.5); width: 20px; height: 20px; margin-right: 25px;"
                                            id="column_{{ $columnIndex }}" data-column-index="{{ $columnIndex }}" checked>
                                        <label class="form-check-label" for="column_{{ $columnIndex }}">
                                            {{ $columnName }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="unselectAllColumns">Unselect All</button>
                        <button type="button" class="btn btn-primary" id="saveColumnVisibility">Okay</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
@stop
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('javascript')
    <script type="text/javascript">
        $(document).on('click', '#unselectAllColumns', function() {
            $('#columnVisibilityForm input[type="checkbox"]').prop('checked', false);
        });
        $(document).ready(function() {
            // CSRF Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Variables
            let draggedColumn = null;
            let draggedIndex = null;
            let isDragging = false;

            // Initialize DataTable
            sell_table = $('#sell_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                ordering: false, // Disable sorting
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                colReorder: {
                    realtime: true,
                    fixedColumnsLeft: 1 // Keep first column fixed
                },
                ajax: {
                    url: "/sells",
                    data: function(d) {
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

                        if ($('#shipping_status').length) d.shipping_status = $('#shipping_status')
                            .val();
                        if ($('#sell_list_filter_source').length) d.source = $(
                            '#sell_list_filter_source').val();
                        if ($('#only_subscriptions').is(':checked')) d.only_subscriptions = 1;
                        if ($('#payment_method').length) d.payment_method = $('#payment_method').val();

                        return __datatable_ajax_callback(d);
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'payment_methods',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        searchable: false
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining'
                    },
                    {
                        data: 'return_due',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items',
                        searchable: false
                    },
                    {
                        data: 'types_of_service_name',
                        name: 'tos.name',
                        visible: @json(!empty($is_types_service_enabled))
                    },
                    {
                        data: 'service_custom_field_1',
                        name: 'service_custom_field_1',
                        visible: @json(!empty($is_types_service_enabled))
                    },
                    {
                        data: 'custom_field_1',
                        name: 'transactions.custom_field_1',
                        visible: @json(!empty($custom_labels['sell']['custom_field_1']))
                    },
                    {
                        data: 'custom_field_2',
                        name: 'transactions.custom_field_2',
                        visible: @json(!empty($custom_labels['sell']['custom_field_2']))
                    },
                    {
                        data: 'custom_field_3',
                        name: 'transactions.custom_field_3',
                        visible: @json(!empty($custom_labels['sell']['custom_field_3']))
                    },
                    {
                        data: 'custom_field_4',
                        name: 'transactions.custom_field_4',
                        visible: @json(!empty($custom_labels['sell']['custom_field_4']))
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                    {
                        data: 'additional_notes',
                        name: 'additional_notes'
                    },
                    {
                        data: 'staff_note',
                        name: 'staff_note'
                    },
                    {
                        data: 'shipping_details',
                        name: 'shipping_details'
                    },
                    {
                        data: 'table_name',
                        name: 'tables.name',
                        visible: @json(!empty($is_tables_enabled))
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        visible: @json(!empty($is_service_staff_enabled))
                    }
                ],
                drawCallback: function(settings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                footerCallback: function(row, data, start, end, display) {
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;

                    for (var r in data) {
                        footer_sale_total += $(data[r].final_total).data('orig-value') ?
                            parseFloat($(data[r].final_total).data('orig-value')) : 0;
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ?
                            parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ?
                            parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due')
                            .data('orig-value') ?
                            parseFloat($(data[r].return_due).find('.sell_return_due').data(
                                'orig-value')) : 0;
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
                initComplete: function() {
                    addDragAndDrop();
                }
            });

            // Initialize drag and drop functionality
            function addDragAndDrop() {
                const headers = $('#sell_table thead th').toArray();

                headers.forEach(th => {
                    $(th).attr('draggable', true)
                        .on('dragstart', function(e) {
                            draggedColumn = th;
                            draggedIndex = $(th).index();
                            $(th).addClass('column-dragging');
                            e.originalEvent.dataTransfer.setData('text/plain', '');
                        })
                        .on('dragend', function() {
                            $(th).removeClass('column-dragging');
                            draggedColumn = null;
                            draggedIndex = null;
                            $('.column-drag-over').removeClass('column-drag-over');
                        })
                        .on('dragover', function(e) {
                            e.preventDefault();
                            if (draggedColumn && draggedColumn !== this) {
                                $(this).addClass('column-drag-over');
                            }
                        })
                        .on('dragleave', function() {
                            $(this).removeClass('column-drag-over');
                        })
                        .on('drop', function(e) {
                            e.preventDefault();
                            $(this).removeClass('column-drag-over');

                            if (!draggedColumn || draggedColumn === this) return;

                            const targetIndex = $(this).index();
                            const table = $('#sell_table').DataTable();
                            const order = table.colReorder.order();

                            // Swap columns in order array
                            const temp = order[draggedIndex];
                            order[draggedIndex] = order[targetIndex];
                            order[targetIndex] = temp;

                            // Apply new order
                            table.colReorder.order(order);
                            table.draw(false);
                        });
                });
            }

            // File saving functionality
            $('#createFileForm').on('submit', function(e) {
                e.preventDefault();
                createFile();
            });

            function createFile() {
                const fileName = $('#fileName').val();
                const parentFolder = $('#parentFolder').val();

                if (!fileName) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter a file name'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while we save your file',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const table = $('#sell_table').DataTable();

                // Extract visible column names
                const visibleColumnNames = [];
                table.columns().every(function(index) {
                    if (this.visible()) {
                        const th = $(this.header());
                        visibleColumnNames.push(th.text().trim());
                    }
                });

                // Prepare the final data structure for saving
                const tableData = {
                    visibleColumnNames: visibleColumnNames, // Save only column names
                };

                // Send the data to the backend
                $.ajax({
                    url: '/minireportb1/create',
                    method: 'POST',
                    data: {
                        file_name: fileName,
                        parent_id: parentFolder,
                        table_data: tableData,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.msg,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Redirect to the Laravel route after success
                                window.location.href = "{{ route('MiniReportB1.dashboard') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.msg || 'Error saving file'
                            });
                        }
                    },

                    error: function(xhr, status, error) {
                        console.error('Save error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving file: ' + error
                        });
                    }
                });
            }



            // Helper functions for table data extraction
            function getTableHeaders(table) {
                const headers = [];
                table.columns().header().each(function(th) {
                    headers.push({
                        value: $(th).text().trim(),
                        field: $(th).data('field') || 'custom',
                        table_name: $(th).data('table') || null
                    });
                });
                return headers;
            }

            function getTableRows(table) {
                return table.data().toArray().map(row => {
                    return Object.values(row).map(cell => ({
                        value: cell,
                        colspan: "1",
                        rowspan: "1",
                        merged: "false"
                    }));
                });
            }

            function getUsedFields(table) {
                return Array.from(table.columns().header())
                    .filter(th => $(th).data('field'))
                    .map(th => ({
                        table_name: $(th).data('table') || null,
                        field_name: $(th).data('field')
                    }));
            }

            // Event handlers for filters
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $(this).val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    sell_table.ajax.reload();
                }
            );

            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function() {
                $(this).val('');
                sell_table.ajax.reload();
            });

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source, #payment_method',
                function() {
                    sell_table.ajax.reload();
                });

            $('#only_subscriptions').on('ifChanged', function() {
                sell_table.ajax.reload();
            });

            function toggleColumnVisibility(columnIndex, isVisible) {
                sell_table.column(columnIndex).visible(isVisible);
            }

            $('#saveColumnVisibility').on('click', function() {
                $('#columnVisibilityForm input[type="checkbox"]').each(function() {
                    var columnIndex = $(this).data('column-index');
                    var isVisible = $(this).is(':checked');
                    toggleColumnVisibility(columnIndex, isVisible);
                });
                // Save the column visibility state using the createFile function

                // Close the modal
                $('#columnVisibilityModal').modal('hide');
            });

            function getColumnVisibility(table) {
                var visibility = [];
                table.columns().every(function() {
                    visibility.push(this.visible());
                });
                return visibility;
            }

        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
