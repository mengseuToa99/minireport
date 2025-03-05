@extends('minireportb1::layouts.master2')
@section('title', __('essentials::lang.payroll_groups'))

@section('content')

@include('minireportb1::MiniReportB1.multitable.partials.dropdown')

@component('components.widget', ['class' => 'box-solid'])
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('payroll_groups')</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="payroll_group_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('essentials::lang.name')</th>
                                <th>@lang('sale.status')</th>
                                <th>@lang('sale.payment_status')</th>
                                <th>@lang('essentials::lang.total_gross_amount')</th>
                                <th>@lang('lang_v1.added_by')</th>
                                <th>@lang('business.location')</th>
                                <th>@lang('lang_v1.created_at')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @endcomponent
@endsection

@section('javascript')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

    const tablename = "#payroll_group_table";
        const visibleColumnNames = @json($visibleColumnNames ?? []);
        const isViewMode = @json(isset($file_name));
        const reportName = "payroll_groupReport";
        const filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format;
        const dateSeparator = ' - ';

        $(document).ready(function() {
            initializePayrollGroupTable();
        });

        function initializePayrollGroupTable() {
    $('#payroll_group_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'payrollGroupDatatable']) }}",
        columns: [
            { 
                data: 'name', 
                name: 'essentials_payroll_groups.name', 
                visible: isViewMode ? visibleColumnNames.includes("Name") || visibleColumnNames.includes("Name") : true 
            },
            { 
                data: 'status', 
                name: 'essentials_payroll_groups.status', 
                visible: isViewMode ? visibleColumnNames.includes("Status") || visibleColumnNames.includes("ស្ថានភាពប្រាក់") : true 
            },
            { 
                data: 'payment_status', 
                name: 'essentials_payroll_groups.payment_status', 
                visible: isViewMode ? visibleColumnNames.includes("Payment Status") || visibleColumnNames.includes("ស្ថានភាពប្រាក់") : true 
            },
            { 
                data: 'gross_total', 
                name: 'essentials_payroll_groups.gross_total', 
                visible: isViewMode ? visibleColumnNames.includes("Total gross amount") || visibleColumnNames.includes("Total gross amount") : true 
            },
            { 
                data: 'added_by', 
                name: 'added_by', 
                visible: isViewMode ? visibleColumnNames.includes("Added By") || visibleColumnNames.includes("ទិន្នន័យ") : true 
            },
            { 
                data: 'location_name', 
                name: 'BL.name', 
                visible: isViewMode ? visibleColumnNames.includes("Location") || visibleColumnNames.includes("សាខា") : true 
            },
            { 
                data: 'created_at', 
                name: 'essentials_payroll_groups.created_at', 
                searchable: false, 
                visible: isViewMode ? visibleColumnNames.includes("Created At") || visibleColumnNames.includes("បង្កើតនៅ") : true 
            },
            { 
                data: 'action', 
                name: 'action', 
                searchable: false, 
                orderable: false, 
                visible: isViewMode ? visibleColumnNames.includes("Action") || visibleColumnNames.includes("លំអិត") : true 
            }
        ]
    });
}
    </script>
@endsection