$(document).ready(function() {
    if (typeof expense_table !== 'undefined') {
        // Hide all columns
        var columnCount = expense_table.columns().count();
        for (var i = 0; i < columnCount; i++) {
            expense_table.column(i).visible(false);
        }

        // Show only the "Action" and "Date" columns
        expense_table.column(0).visible(true);  // Show "Action" column
        expense_table.column(1).visible(true);  // Show "Date" column

        // Redraw the table
        expense_table.draw();
    } else {
        console.error("expense_table is not defined. Ensure the DataTable is initialized before this script runs.");
    }
});

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
            swal('@extends('minireportb1::layouts.master2')');
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
            swal('@lang('sale.products')');
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
            swal('@lang('lang_v1.manage_products')');
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
                swal('@lang('lang_v1.not_for_selling')');
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


$(document).ready(function () {
    $(document).on('click', '.add_payment_modal', function (e) {
        e.preventDefault();
        var container = $('.payment_modal');

        $.ajax({
            url: $(this).attr('href'),
            dataType: 'json',
            success: function (result) {
                if (result.status == 'due') {
                    container.html(result.view).modal('show');
                    __currency_convert_recursively(container);
                    $('#paid_on').datetimepicker({
                        format: moment_date_format + ' ' + moment_time_format,
                        ignoreReadonly: true,
                    });
                    container.find('form#transaction_payment_add_form').validate();
                    set_default_payment_account();

                    $('.payment_modal')
                        .find('input[type="checkbox"].input-icheck')
                        .each(function () {
                            $(this).iCheck({
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                            });
                        });
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    $(document).on('click', '.edit_payment', function (e) {
        e.preventDefault();
        var container = $('.edit_payment_modal');

        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function (result) {
                container.html(result).modal('show');
                __currency_convert_recursively(container);
                $('#paid_on').datetimepicker({
                    format: moment_date_format + ' ' + moment_time_format,
                    ignoreReadonly: true,
                });
                container.find('form#transaction_payment_add_form').validate();
            },
        });
    });

    $(document).on('click', '.view_payment_modal', function (e) {
        e.preventDefault();
        var container = $('.payment_modal');

        $.ajax({
            url: $(this).attr('href'),
            dataType: 'html',
            success: function (result) {
                $(container).html(result).modal('show');
                __currency_convert_recursively(container);
            },
        });
    });
    $(document).on('click', '.delete_payment', function (e) {
        swal({
            title: LANG.sure,
            text: LANG.confirm_delete_payment,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: $(this).data('href'),
                    method: 'delete',
                    dataType: 'json',
                    success: function (result) {
                        if (result.success === true) {
                            $('div.payment_modal').modal('hide');
                            $('div.edit_payment_modal').modal('hide');
                            toastr.success(result.msg);
                            if (typeof purchase_table != 'undefined') {
                                purchase_table.ajax.reload();
                            }
                            if (typeof sell_table != 'undefined') {
                                sell_table.ajax.reload();
                            }
                            if (typeof expense_table != 'undefined') {
                                expense_table.ajax.reload();
                            }
                            if (typeof ob_payment_table != 'undefined') {
                                ob_payment_table.ajax.reload();
                            }
                            // project Module
                            if (typeof project_invoice_datatable != 'undefined') {
                                project_invoice_datatable.ajax.reload();
                            }

                            if ($('#contact_payments_table').length) {
                                get_contact_payments();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    //view single payment
    $(document).on('click', '.view_payment', function () {
        var url = $(this).data('href');
        var container = $('.view_modal');
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'html',
            success: function (result) {
                $(container).html(result).modal('show');
                __currency_convert_recursively(container);
            },
        });
    });
});

$(document).on('change', '#transaction_payment_add_form .payment_types_dropdown', function (e) {
    set_default_payment_account();
});

function set_default_payment_account() {
    var default_accounts = {};

    if (!_.isUndefined($('#transaction_payment_add_form #default_payment_accounts').val())) {
        default_accounts = JSON.parse(
            $('#transaction_payment_add_form #default_payment_accounts').val()
        );
    }

    var payment_type = $('#transaction_payment_add_form .payment_types_dropdown').val();
    if (payment_type && payment_type != 'advance') {
        var default_account =
            !_.isEmpty(default_accounts) && default_accounts[payment_type]['account']
                ? default_accounts[payment_type]['account']
                : '';
        $('#transaction_payment_add_form #account_id').val(default_account);
        $('#transaction_payment_add_form #account_id').change();
    }
}

$(document).on('change', '.payment_types_dropdown', function (e) {
    var payment_type = $('#transaction_payment_add_form .payment_types_dropdown').val();
    account_dropdown = $('#transaction_payment_add_form #account_id');
    if (payment_type == 'advance') {
        if (account_dropdown) {
            account_dropdown.prop('disabled', true);
            account_dropdown.closest('.form-group').addClass('hide');
        }
    } else {
        if (account_dropdown) {
            account_dropdown.prop('disabled', false);
            account_dropdown.closest('.form-group').removeClass('hide');
        }
    }
});

$(document).on('submit', 'form#transaction_payment_add_form', function (e) {
    var is_valid = true;
    var payment_type = $('#transaction_payment_add_form .payment_types_dropdown').val();
    var denomination_for_payment_types = JSON.parse(
        $('#transaction_payment_add_form .enable_cash_denomination_for_payment_methods').val()
    );
    if (
        denomination_for_payment_types.includes(payment_type) &&
        $('#transaction_payment_add_form .is_strict').length &&
        $('#transaction_payment_add_form .is_strict').val() === '1'
    ) {
        var payment_amount = __read_number($('#transaction_payment_add_form .payment_amount'));
        var total_denomination = $('#transaction_payment_add_form')
            .find('input.denomination_total_amount')
            .val();
        if (payment_amount != total_denomination) {
            is_valid = false;
        }
    }

    $('#transaction_payment_add_form').find('button[type="submit"]').attr('disabled', false);

    if (!is_valid) {
        $('#transaction_payment_add_form').find('.cash_denomination_error').removeClass('hide');
        e.preventDefault();
        return false;
    } else {
        $('#transaction_payment_add_form').find('.cash_denomination_error').addClass('hide');
    }
});
