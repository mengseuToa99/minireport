@extends('minireportb1::layouts.master2')
@section('title', __('sale.products'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Stock
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">product</small>
        </h1>
    </section>

    <!-- Main content -->
  <section class="content">
    <div class="row">
        <div class="col-md-12">
            @if (!isset($file_name) || empty($file_name))
                @include('minireportb1::MiniReportB1.multitable.partials.dropdown')
            @endif

            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @can('stock_report.view')
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#product_stock_report" class="product_stock_report" data-toggle="tab"
                                aria-expanded="true"><i class="fa fa-hourglass-half" aria-hidden="true"></i>
                                @lang('report.stock_report')</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="product_stock_report">
                            @include('report.partials.stock_report_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <input type="hidden" id="is_rack_enabled" value="{{ $rack_enabled }}">

    <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>

    @if ($is_woocommerce)
        @include('product.partials.toggle_woocommerce_sync_modal')
    @endif
    @include('product.partials.edit_product_location_modal')
</section>
    <!-- /.content -->

@endsection

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">

        const tablename = "#stock_report_table";
        const visibleColumnNames = @json($visibleColumnNames ?? []);
        const isViewMode = @json(isset($file_name));
        const reportName = "stockReport";
        const filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format;
        const dateSeparator = ' - ';

        $(document).ready(function() {
            // product_table = $('#product_table').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     fixedHeader: false,
            //     aaSorting: [
            //         [3, 'asc']
            //     ],
            //     scrollY: "75vh",
            //     scrollX: true,
            //     scrollCollapse: true,
            //     "ajax": {
            //         "url": "/products",
            //         "data": function(d) {
            //             d.type = $('#product_list_filter_type').val();
            //             d.category_id = $('#product_list_filter_category_id').val();
            //             d.brand_id = $('#product_list_filter_brand_id').val();
            //             d.unit_id = $('#product_list_filter_unit_id').val();
            //             d.tax_id = $('#product_list_filter_tax_id').val();
            //             d.active_state = $('#active_state').val();
            //             d.not_for_selling = $('#not_for_selling').is(':checked');
            //             d.location_id = $('#location_id').val();
            //             if ($('#repair_model_id').length == 1) {
            //                 d.repair_model_id = $('#repair_model_id').val();
            //             }

            //             if ($('#woocommerce_enabled').length == 1 && $('#woocommerce_enabled').is(
            //                     ':checked')) {
            //                 d.woocommerce_enabled = 1;
            //             }

            //             d = __datatable_ajax_callback(d);
            //         }
            //     },
            //     columnDefs: [{
            //         "targets": [0, 1, 2],
            //         "orderable": false,
            //         "searchable": false
            //     }],
            //     columns: [{
            //             data: 'mass_delete'
            //         },
            //         {
            //             data: 'image',
            //             name: 'products.image',
            //             visible: isViewMode ? visibleColumnNames.includes("Product image") : true
            //         },
            //         {
            //             data: 'action',
            //             name: 'action',
            //             visible: isViewMode ? visibleColumnNames.includes("Detail") : true
            //         },
            //         {
            //             data: 'product',
            //             name: 'products.name',
            //             visible: isViewMode ? visibleColumnNames.includes("Product") : true
            //         },
            //         {
            //             data: 'product_locations',
            //             name: 'product_locations',
            //         },
            //         @can('view_purchase_price')
            //             {
            //                 data: 'purchase_price',
            //                 name: 'max_purchase_price',
            //                 searchable: false,
            //                 visible: isViewMode ? visibleColumnNames.includes("Unit Purchase Price") :
            //                     true
            //             },
            //         @endcan
            //         @can('access_default_selling_price')
            //             {
            //                 data: 'selling_price',
            //                 name: 'max_price',
            //                 searchable: false,
            //                 visible: isViewMode ? visibleColumnNames.includes("Selling Price") : true
            //             },
            //         @endcan {
            //             data: 'current_stock',
            //             searchable: false,
            //             visible: isViewMode ? visibleColumnNames.includes("Current stock") : true
            //         },
            //         {
            //             data: 'type',
            //             name: 'products.type',
            //             visible: isViewMode ? visibleColumnNames.includes("Product Type") : true
            //         },
            //         {
            //             data: 'category',
            //             name: 'c1.name',
            //             visible: isViewMode ? visibleColumnNames.includes("Category") : true
            //         },
            //         {
            //             data: 'brand',
            //             name: 'brands.name',
            //             visible: isViewMode ? visibleColumnNames.includes("Brand") : true
            //         },
            //         {
            //             data: 'tax',
            //             name: 'tax_rates.name',
            //             searchable: false,
            //             visible: isViewMode ? visibleColumnNames.includes("Tax") : true
            //         },
            //         {
            //             data: 'sku',
            //             name: 'products.sku',
            //             visible: isViewMode ? visibleColumnNames.includes("SKU") : true
            //         },
            //         {
            //             data: 'product_custom_field1',
            //             name: 'products.product_custom_field1',
            //             visible: $('#cf_1').text().length > 0
            //         },
            //         {
            //             data: 'product_custom_field2',
            //             name: 'products.product_custom_field2',
            //             visible: $('#cf_2').text().length > 0
            //         },
            //         {
            //             data: 'product_custom_field3',
            //             name: 'products.product_custom_field3',
            //             visible: $('#cf_3').text().length > 0
            //         },
            //         {
            //             data: 'product_custom_field4',
            //             name: 'products.product_custom_field4',
            //             visible: $('#cf_4').text().length > 0
            //         },
            //         {
            //             data: 'product_custom_field5',
            //             name: 'products.product_custom_field5',
            //             visible: $('#cf_5').text().length > 0
            //         },
            //         {
            //             data: 'product_custom_field6',
            //             name: 'products.product_custom_field6',
            //             visible: $('#cf_6').text().length > 0
            //         },
            //         {
            //             data: 'product_custom_field7',
            //             name: 'products.product_custom_field7',
            //             visible: $('#cf_7').text().length > 0
            //         }
            //     ],
            //     createdRow: function(row, data, dataIndex) {
            //         if ($('input#is_rack_enabled').val() == 1) {
            //             var target_col = 0;
            //             @can('product.delete')
            //                 target_col = 1;
            //             @endcan
            //             $(row).find('td:eq(' + target_col + ') div').prepend(
            //                 '<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' +
            //                 LANG.details + '"></i>&nbsp;&nbsp;');
            //         }
            //         $(row).find('td:eq(0)').attr('class', 'selectable_td');
            //     },
            //     fnDrawCallback: function(oSettings) {
            //         __currency_convert_recursively($('#product_table'));
            //     },
            // });
            
            // Array to track the ids of the details displayed rows
            var detailRows = [];

            $('#product_table tbody').on('click', 'tr i.rack-details', function() {
                var i = $(this);
                var tr = $(this).closest('tr');
                var row = product_table.row(tr);
                var idx = $.inArray(tr.attr('id'), detailRows);

                if (row.child.isShown()) {
                    i.addClass('fa-plus-circle text-success');
                    i.removeClass('fa-minus-circle text-danger');

                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice(idx, 1);
                } else {
                    i.removeClass('fa-plus-circle text-success');
                    i.addClass('fa-minus-circle text-danger');

                    row.child(get_product_details(row.data())).show();

                    // Add to the 'open' array
                    if (idx === -1) {
                        detailRows.push(tr.attr('id'));
                    }
                }
            });

            $('#opening_stock_modal').on('hidden.bs.modal', function(e) {
                product_table.ajax.reload();
            });

            $('table#product_table tbody').on('click', 'a.delete-product', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    product_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#delete-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();

                if (selected_rows.length > 0) {
                    $('input#selected_rows').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('form#mass_delete_form').submit();
                        }
                    });
                } else {
                    $('input#selected_rows').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
            });

            $(document).on('click', '#deactivate-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();

                if (selected_rows.length > 0) {
                    $('input#selected_products').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            var form = $('form#mass_deactivate_form')

                            var data = form.serialize();
                            $.ajax({
                                method: form.attr('method'),
                                url: form.attr('action'),
                                dataType: 'json',
                                data: data,
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        product_table.ajax.reload();
                                        form
                                            .find('#selected_products')
                                            .val('');
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                } else {
                    $('input#selected_products').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
            })

            $(document).on('click', '#edit-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();

                if (selected_rows.length > 0) {
                    $('input#selected_products_for_edit').val(selected_rows);
                    $('form#bulk_edit_form').submit();
                } else {
                    $('input#selected_products').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
            })

            $('table#product_table tbody').on('click', 'a.activate-product', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                $.ajax({
                    method: "get",
                    url: href,
                    dataType: "json",
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            product_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('change',
                '#product_list_filter_type, #product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #repair_model_id',
                function() {
                    if ($("#product_list_tab").hasClass('active')) {
                        product_table.ajax.reload();
                    }

                    if ($("#product_stock_report").hasClass('active')) {
                        stock_report_table.ajax.reload();
                    }
                });

            $(document).on('ifChanged', '#not_for_selling, #woocommerce_enabled', function() {
                if ($("#product_list_tab").hasClass('active')) {
                    product_table.ajax.reload();
                }

                if ($("#product_stock_report").hasClass('active')) {
                    stock_report_table.ajax.reload();
                }
            });

            $('#product_location').select2({
                dropdownParent: $('#product_location').closest('.modal')
            });

            @if ($is_woocommerce)
                $(document).on('click', '.toggle_woocomerce_sync', function(e) {
                    e.preventDefault();
                    var selected_rows = getSelectedRows();
                    if (selected_rows.length > 0) {
                        $('#woocommerce_sync_modal').modal('show');
                        $("input#woocommerce_products_sync").val(selected_rows);
                    } else {
                        $('input#selected_products').val('');
                        swal('@lang('lang_v1.no_row_selected')');
                    }
                });

                $(document).on('submit', 'form#toggle_woocommerce_sync_form', function(e) {
                    e.preventDefault();
                    var url = $('form#toggle_woocommerce_sync_form').attr('action');
                    var method = $('form#toggle_woocommerce_sync_form').attr('method');
                    var data = $('form#toggle_woocommerce_sync_form').serialize();
                    var ladda = Ladda.create(document.querySelector('.ladda-button'));
                    ladda.start();
                    $.ajax({
                        method: method,
                        dataType: "json",
                        url: url,
                        data: data,
                        success: function(result) {
                            ladda.stop();
                            if (result.success) {
                                $("input#woocommerce_products_sync").val('');
                                $('#woocommerce_sync_modal').modal('hide');
                                toastr.success(result.msg);
                                product_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            @endif
        });

        $(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal, #view_product_modal',
            function() {
                var div = $(this).find('#view_product_stock_details');
                if (div.length) {
                    $.ajax({
                        url: "{{ action([\App\Http\Controllers\ReportController::class, 'getStockReport']) }}" +
                            '?for=view_product&product_id=' + div.data('product_id'),
                        dataType: 'html',
                        success: function(result) {
                            div.html(result);
                            __currency_convert_recursively(div);
                        },
                    });
                }
                __currency_convert_recursively($(this));
            });
        var data_table_initailized = false;
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($(e.target).attr('href') == '#product_stock_report') {
                if (!data_table_initailized) {
                    //Stock report table
                    var stock_report_cols = [{
                            data: 'action',
                            name: 'action',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'sku',
                            name: 'variations.sub_sku',
                            visible: isViewMode ? visibleColumnNames.includes("SKU") : true
                        },
                        {
                            data: 'product',
                            name: 'p.name',
                            visible: isViewMode ? visibleColumnNames.includes("Product") : true
                        },
                        {
                            data: 'variation',
                            name: 'variation',
                            visible: isViewMode ? visibleColumnNames.includes("Variation") : true
                        },
                        {
                            data: 'category_name',
                            name: 'c.name',
                            visible: isViewMode ? visibleColumnNames.includes("Category") : true
                        },
                        {
                            data: 'location_name',
                            name: 'l.name',
                            visible: isViewMode ? visibleColumnNames.includes("Location") : true
                        },
                        {
                            data: 'unit_price',
                            name: 'variations.sell_price_inc_tax',
                            visible: isViewMode ? visibleColumnNames.includes("Unit Selling Price") : true
                        },
                        {
                            data: 'stock',
                            name: 'stock',
                            searchable: false,
                            visible: isViewMode ? visibleColumnNames.includes("Current stock") : true
                        },
                    ];
                    if ($('th.stock_price').length) {
                        stock_report_cols.push({
                            data: 'stock_price',
                            name: 'stock_price',
                            searchable: false
                        });
                        stock_report_cols.push({
                            data: 'stock_value_by_sale_price',
                            name: 'stock_value_by_sale_price',
                            searchable: false,
                            orderable: false
                        });
                        stock_report_cols.push({
                            data: 'potential_profit',
                            name: 'potential_profit',
                            searchable: false,
                            orderable: false
                        });
                    }

                    stock_report_cols.push({
                        data: 'total_sold',
                        name: 'total_sold',
                        searchable: false
                    });
                    stock_report_cols.push({
                        data: 'total_transfered',
                        name: 'total_transfered',
                        searchable: false
                    });
                    stock_report_cols.push({
                        data: 'total_adjusted',
                        name: 'total_adjusted',
                        searchable: false
                    });
                    stock_report_cols.push({
                        data: 'product_custom_field1',
                        name: 'p.product_custom_field1'
                    });
                    stock_report_cols.push({
                        data: 'product_custom_field2',
                        name: 'p.product_custom_field2'
                    });
                    stock_report_cols.push({
                        data: 'product_custom_field3',
                        name: 'p.product_custom_field3'
                    });
                    stock_report_cols.push({
                        data: 'product_custom_field4',
                        name: 'p.product_custom_field4'
                    });

                    if ($('th.current_stock_mfg').length) {
                        stock_report_cols.push({
                            data: 'total_mfg_stock',
                            name: 'total_mfg_stock',
                            searchable: false
                        });
                    }
                    stock_report_table = $('#stock_report_table').DataTable({
                        order: [
                            [1, 'asc']
                        ],
                        processing: true,
                        serverSide: true,
                        scrollY: "75vh",
                        scrollX: true,
                        scrollCollapse: true,
                        fixedHeader: false,
                        ajax: {
                            url: '/minireportb1/stock-report',
                            data: function(d) {
                                d.location_id = $('#location_id').val();
                                d.category_id = $('#product_list_filter_category_id').val();
                                d.brand_id = $('#product_list_filter_brand_id').val();
                                d.unit_id = $('#product_list_filter_unit_id').val();
                                d.type = $('#product_list_filter_type').val();
                                d.active_state = $('#active_state').val();
                                d.not_for_selling = $('#not_for_selling').is(':checked');
                                if ($('#repair_model_id').length == 1) {
                                    d.repair_model_id = $('#repair_model_id').val();
                                }
                            }
                        },
                        columns: stock_report_cols,
                        fnDrawCallback: function(oSettings) {
                            __currency_convert_recursively($('#stock_report_table'));
                        },
                        "footerCallback": function(row, data, start, end, display) {
                            var footer_total_stock = 0;
                            var footer_total_sold = 0;
                            var footer_total_transfered = 0;
                            var total_adjusted = 0;
                            var total_stock_price = 0;
                            var footer_stock_value_by_sale_price = 0;
                            var total_potential_profit = 0;
                            var footer_total_mfg_stock = 0;
                            for (var r in data) {
                                footer_total_stock += $(data[r].stock).data('orig-value') ?
                                    parseFloat($(data[r].stock).data('orig-value')) : 0;

                                footer_total_sold += $(data[r].total_sold).data('orig-value') ?
                                    parseFloat($(data[r].total_sold).data('orig-value')) : 0;

                                footer_total_transfered += $(data[r].total_transfered).data(
                                        'orig-value') ?
                                    parseFloat($(data[r].total_transfered).data('orig-value')) : 0;

                                total_adjusted += $(data[r].total_adjusted).data('orig-value') ?
                                    parseFloat($(data[r].total_adjusted).data('orig-value')) : 0;

                                total_stock_price += $(data[r].stock_price).data('orig-value') ?
                                    parseFloat($(data[r].stock_price).data('orig-value')) : 0;

                                footer_stock_value_by_sale_price += $(data[r].stock_value_by_sale_price)
                                    .data('orig-value') ?
                                    parseFloat($(data[r].stock_value_by_sale_price).data(
                                        'orig-value')) : 0;

                                total_potential_profit += $(data[r].potential_profit).data(
                                        'orig-value') ?
                                    parseFloat($(data[r].potential_profit).data('orig-value')) : 0;

                                footer_total_mfg_stock += $(data[r].total_mfg_stock).data(
                                        'orig-value') ?
                                    parseFloat($(data[r].total_mfg_stock).data('orig-value')) : 0;
                            }

                            $('.footer_total_stock').html(__currency_trans_from_en(footer_total_stock,
                                false));
                            $('.footer_total_stock_price').html(__currency_trans_from_en(
                                total_stock_price));
                            $('.footer_total_sold').html(__currency_trans_from_en(footer_total_sold,
                                false));
                            $('.footer_total_transfered').html(__currency_trans_from_en(
                                footer_total_transfered, false));
                            $('.footer_total_adjusted').html(__currency_trans_from_en(total_adjusted,
                                false));
                            $('.footer_stock_value_by_sale_price').html(__currency_trans_from_en(
                                footer_stock_value_by_sale_price));
                            $('.footer_potential_profit').html(__currency_trans_from_en(
                                total_potential_profit));
                            if ($('th.current_stock_mfg').length) {
                                $('.footer_total_mfg_stock').html(__currency_trans_from_en(
                                    footer_total_mfg_stock, false));
                            }
                        },
                    });
                    data_table_initailized = true;
                } else {
                    stock_report_table.ajax.reload();
                }
            } else {
                product_table.ajax.reload();
            }

            // remove class from data table button
            $('.btn-default').removeClass('btn-default');
            $('.tw-dw-btn-outline').removeClass('btn');
        });

        $(document).on('click', '.update_product_location', function(e) {
            e.preventDefault();
            var selected_rows = getSelectedRows();

            if (selected_rows.length > 0) {
                $('input#selected_products').val(selected_rows);
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
                if (type == 'add') {
                    modal.find('.remove_from_location_title').addClass('hide');
                    modal.find('.add_to_location_title').removeClass('hide');
                } else if (type == 'remove') {
                    modal.find('.add_to_location_title').addClass('hide');
                    modal.find('.remove_from_location_title').removeClass('hide');
                }

                modal.modal('show');
                modal.find('#product_location').select2({
                    dropdownParent: modal
                });
                modal.find('#product_location').val('').change();
                modal.find('#update_type').val(type);
                modal.find('#products_to_update_location').val(selected_rows);
            } else {
                $('input#selected_products').val('');
                swal('@lang('lang_v1.no_row_selected')');
            }
        });

        $(document).on('submit', 'form#edit_product_location_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div#edit_product_location_modal').modal('hide');
                        toastr.success(result.msg);
                        product_table.ajax.reload();
                        $('form#edit_product_location_form')
                            .find('button[type="submit"]')
                            .attr('disabled', false);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
        
    </script>
<script>
    // Main initialization function
    function initializeProductReport() {
        try {
            initializeDateRangePicker();
            setupEventListeners();
            applySavedFilters();
        } catch (error) {
            console.error('Error initializing product report:', error);
            toastr.error('Failed to initialize report features');
        }
    }

    function initializeDateRangePicker() {
        // Date range picker configuration
        $('#product_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                const displayDate = start.format(dateFormat) + dateSeparator + end.format(dateFormat);
                $(this).val(displayDate);
                validateAndApplyDateRange(start, end);
            }
        ).on('cancel.daterangepicker', function(ev) {
            $(this).val('');
            product_table.ajax.reload(); // Reload the product table
        });
    }

    function validateAndApplyDateRange(start, end) {
        if (start.isValid() && end.isValid()) {
            product_table.ajax.reload(); // Reload the product table
        } else {
            toastr.error('Invalid date range selected');
            $(this).val('');
        }
    }

    function applySavedFilters() {
        if (!isViewMode) return;

        console.log('Applying saved filters:', filterCriteria);

        // Apply date range first
        if (filterCriteria.dateRange) {
            const [startDate, endDate] = filterCriteria.dateRange.split(dateSeparator);
            if (moment(startDate, dateFormat).isValid() && moment(endDate, dateFormat).isValid()) {
                $('#product_list_filter_date_range').val(filterCriteria.dateRange);
                $('#product_list_filter_date_range').data('daterangepicker').setStartDate(startDate);
                $('#product_list_filter_date_range').data('daterangepicker').setEndDate(endDate);
            }
        }

        // Apply other filters
        const filterMap = {
            locationId: '#product_list_filter_location_id',
            type: '#product_list_filter_type',
            categoryId: '#product_list_filter_category_id',
            unitId: '#product_list_filter_unit_id',
            taxId: '#product_list_filter_tax_id',
            brandId: '#product_list_filter_brand_id',
            activeState: '#active_state',
            notForSelling: '#not_for_selling',
            woocommerceEnabled: '#woocommerce_enabled'
        };

        Object.entries(filterMap).forEach(([key, selector]) => {
            if (filterCriteria[key] !== undefined && filterCriteria[key] !== null) {
                if ($(selector).is(':checkbox')) {
                    // Handle checkboxes
                    $(selector).prop('checked', filterCriteria[key] == 1);
                } else {
                    // Handle dropdowns and other inputs
                    $(selector).val(filterCriteria[key]).trigger('change');
                }
            }
        });
    }

    // Event listeners for filters
    function setupEventListeners() {
        // Date range change listener
        $('#product_list_filter_date_range').on('apply.daterangepicker', function(ev, picker) {
            validateAndApplyDateRange(picker.startDate, picker.endDate);
        });

        // Other filter change listeners
        $('#product_list_filter_location_id, #product_list_filter_type, #product_list_filter_category_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #product_list_filter_brand_id, #active_state').on('change', function() {
            product_table.ajax.reload(); // Reload the product table
        });

        // Checkbox change listeners
        $('#not_for_selling, #woocommerce_enabled').on('change', function() {
            product_table.ajax.reload(); // Reload the product table
        });
    }

    // Initialize the report when the document is ready
    $(document).ready(function() {
        initializeProductReport();
    });
</script>
@endsection
