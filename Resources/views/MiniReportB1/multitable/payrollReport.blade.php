@extends('minireportb1::layouts.master2')
@section('title', __('essentials::lang.payroll'))

@section('content')
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('essentials::lang.payroll')</h1>
    </section>

    @include('minireportb1::MiniReportB1.multitable.partials.dropdown')

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @include('minireportb1::MiniReportB1.multitable.partials.payroll_filters')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="payrolls_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('essentials::lang.employee')</th>
                                <th>@lang('essentials::lang.department')</th>
                                <th>@lang('essentials::lang.designation')</th>
                                <th>@lang('essentials::lang.month_year')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('sale.total_amount')</th>
                                <th>@lang('sale.payment_status')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            initializePayrollTable();
            initializeFilters();
        });

        function initializePayrollTable() {
            $('#payrolls_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'index']) }}",
                    data: function(d) {
                        d.user_id = $('#user_id_filter').val();
                        d.location_id = $('#location_id_filter').val();
                        d.month_year = $('#month_year_filter').val();
                        d.department_id = $('#department_id').val();
                        d.designation_id = $('#designation_id').val();
                    },
                },
                columns: [
                    { data: 'user', name: 'user' },
                    { data: 'department', name: 'dept.name' },
                    { data: 'designation', name: 'dsgn.name' },
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#payrolls_table'));
                },
            });
        }

        function initializeFilters() {
            $('#month_year_filter').datepicker({
                autoclose: true,
                format: 'mm/yyyy',
                minViewMode: "months"
            });

            $(document).on('change', '#user_id_filter, #location_id_filter, #department_id, #designation_id, #month_year_filter', function() {
                $('#payrolls_table').DataTable().ajax.reload();
            });
        }

    </script>
@endsection