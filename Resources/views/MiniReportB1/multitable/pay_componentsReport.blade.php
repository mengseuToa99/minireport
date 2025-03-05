@extends('minireportb1::layouts.master2')
@section('title', __('essentials::lang.pay_components'))

@section('content')

    @include('minireportb1::MiniReportB1.multitable.partials.dropdown')


    @component('components.widget', ['class' => 'box-solid'])
        <section class="content-header">
            <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('essentials::lang.pay_components')</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="ad_pc_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('lang_v1.description')</th>
                                    <th>@lang('lang_v1.type')</th>
                                    <th>@lang('sale.amount')</th>
                                    <th>@lang('essentials::lang.applicable_date')</th>
                                    <th>@lang('essentials::lang.employee')</th>
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
        const tablename = "#ad_pc_table";
        const visibleColumnNames = @json($visibleColumnNames ?? []);
        const isViewMode = @json(isset($file_name));
        const reportName = "pay_componentsReport";
        const filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format;
        const dateSeparator = ' - ';


        $(document).ready(function() {
            initializePayComponentsTable();
        });

        function initializePayComponentsTable() {
            $('#ad_pc_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ action([\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController::class, 'index']) }}",
                columns: [{
                        data: 'description',
                        name: 'description',
                        visible: isViewMode ? visibleColumnNames.includes("Description") || visibleColumnNames
                            .includes("ការបរិយាយ") : true
                    },
                    {
                        data: 'type',
                        name: 'type',
                        visible: isViewMode ? visibleColumnNames.includes("Type") || visibleColumnNames
                            .includes("ប្រភេទ") : true
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        visible: isViewMode ? visibleColumnNames.includes("Amount") || visibleColumnNames
                            .includes("ទឹកប្រាក់") : true
                    },
                    {
                        data: 'applicable_date',
                        name: 'applicable_date',
                        visible: isViewMode ? visibleColumnNames.includes("Applicable Date") ||
                            visibleColumnNames.includes("Applicable Date") : true
                    },
                    {
                        data: 'employees',
                        searchable: false,
                        orderable: false,
                        visible: isViewMode ? visibleColumnNames.includes("Employee") || visibleColumnNames
                            .includes("Employee") : true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        visible: isViewMode ? visibleColumnNames.includes("Action") || visibleColumnNames
                            .includes("លំអិត") : true
                    }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#ad_pc_table'));
                },
            });
        }

        
    </script>
@endsection
