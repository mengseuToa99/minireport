@extends('layouts.app')
@section('title', 'តារាងតុល្យការ (Balance Sheet)')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">តារាងតុល្យការ (Balance Sheet)
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">គ្រប់គ្រងគណនីរបស់អ្នក (Manage Your Accounts)</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @if (!empty($not_linked_payments))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger">
                <ul>
                    <li>{!! __('account.payments_not_linked_with_account', ['payments' => $not_linked_payments]) !!} 
                        <a href="{{ action([\App\Http\Controllers\AccountReportsController::class, 'paymentAccountReport']) }}">មើលពត៌មានលម្អិត (View Details)</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    @can('account.access')
    <div class="row">
        @component('components.widget')
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <!-- Other Accounts Tab -->
                    <div class="tab-pane active" id="other_accounts">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    {!! Form::select(
                                        'account_status',
                                        ['active' => 'សកម្ម (Active)', 'closed' => 'បានបិទ (Closed)'],
                                        null,
                                        ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_status'],
                                    ) !!}
                                </div>
                                <div class="col-md-8">
                                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                                        data-container=".account_model"
                                        data-href="{{ action([\App\Http\Controllers\AccountController::class, 'create']) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg> បន្ថែម (Add)
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="other_account_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>លេខគណនី (Account Number)</th>
                                                <th>ឈ្មោះ (Name)</th>
                                                <th>ប្រភេទគណនីលម្អិត (Account Detail Type)</th>
                                                <th>ប្រភេទគណនីរង (Account Sub Type)</th>
                                                <th>ប្រភេទគណនី (Account Type)</th>
                                                <th>ការពិពណ៌នា (Description)</th>
                                                <th>សមតុល្យ (Balance)</th>
                                                <th>ព័ត៌មានលម្អិតគណនី (Account Details)</th>
                                                <th>បន្ថែមដោយ (Added By)</th>
                                                <th>សកម្មភាព (Action)</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr class="bg-gray font-17 footer-total text-center">
                                                <td colspan="5"><strong>សរុប (Total):</strong></td>
                                                <td class="footer_total_balance"></td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        @endcomponent
    </div>
    @endcan

    <!-- Modals -->
    <div class="modal fade account_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="account_type_modal">
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        let other_account_table;
        // Initialize Other Account DataTable
        other_account_table = $('#other_account_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            ajax: {
                url: '/account/account?account_type=other',
                data: function(d) {
                    d.account_status = $('#account_status').val();
                }
            },
            columnDefs: [{ "targets": [6, 8], "orderable": false, "searchable": false }],
            columns: [
                {
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'account_number', name: 'accounts.account_number' },
                { data: 'name', name: 'accounts.name' },
                { data: 'account_detail_type', name: 'account_detail_type' },
                { data: 'account_sub_type_name', name: 'account_sub_type_name' },
                { data: 'account_type', name: 'account_type' },
                { data: 'description', name: 'accounts.description'},
                { data: 'balance', name: 'balance', searchable: false },
                { data: 'account_details', name: 'account_details' },
                { data: 'added_by', name: 'u.first_name' },
                { data: 'action', name: 'action' }
            ],
            language: {
                emptyTable: `
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td colspan="10" class="text-center">
                                <h3>មិនមានគណនីទេ (No Accounts)</h3>
                                <p>ជំនួយក្នុងការបន្ថែមគណនីលំនាំដើម (Add Default Accounts Help)</p>
                                <a href="{{ route('account.create-default-accounts') }}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent">
                                    បន្ថែមគណនីលំនាំដើម (Add Default Accounts) <i class="fas fa-file-import"></i>
                                </a>
                            </td>
                        </tr>
                    </table>
                `
            },
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#other_account_table'));
            },
            footerCallback: function(row, data, start, end, display) {
                let footer_total_balance = 0;
                for (let r in data) {
                    footer_total_balance += $(data[r].balance).data('orig-value') ? parseFloat($(data[r].balance).data('orig-value')) : 0;
                }
                $('.footer_total_balance').html(__currency_trans_from_en(footer_total_balance));
            }
        });

        // Close Account Confirmation
        $(document).on('click', 'button.close_account', function() {
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    const url = $(this).data('url');
                    $.ajax({
                        method: "GET",
                        url: url,
                        dataType: "json",
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                capital_account_table.ajax.reload();
                                other_account_table.ajax.reload();
                                table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'មានកំហុសកើតឡើង (An error occurred).');
                        }
                    });
                }
            });
        });

        // Account Status Change Event
        $(document).on('change', '#account_status', function() {
            other_account_table.ajax.reload();
        });

        // Delete Account Confirmation
        $(document).on('click', 'button.delete_account_button', function() {
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    const href = $(this).data('href');
                    const data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                other_account_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        // On Modal Show Add/Edit Account
        $('.account_model').on('shown.bs.modal', function(e) {
            $('form#add_account').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const data = form.serialize();
                $.ajax({
                    method: form.attr('method'),
                    url: form.attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success) {
                            $('div.account_model').modal('hide');
                            toastr.success(result.msg);
                            other_account_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });
        });
    });
</script>
@endsection
