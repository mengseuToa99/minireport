@extends('minireportb1::layouts.master2')
@section('title', __('purchase.purchases'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @isset($file_name)
                {{ $file_name }} <!-- Display the file name if it exists -->
            @else
                @lang('Purchase') <!-- Fall back to the default title -->
            @endisset
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
       

        @if (!isset($file_name) || empty($file_name))
            @include('minireportb1::MiniReportB1.multitable.partials.dropdown')
        @endif

        @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all'),
                ]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_supplier_id', __('purchase.supplier') . ':') !!}
                {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all'),
                ]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_status', __('purchase.purchase_status') . ':') !!}
                {!! Form::select('purchase_list_filter_status', $orderStatuses, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all'),
                ]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_payment_status', __('purchase.payment_status') . ':') !!}
                {!! Form::select(
                    'purchase_list_filter_payment_status',
                    [
                        'paid' => __('lang_v1.paid'),
                        'due' => __('lang_v1.due'),
                        'partial' => __('lang_v1.partial'),
                        'overdue' => __('lang_v1.overdue'),
                    ],
                    null,
                    ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')],
                ) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range', null, [
                    'placeholder' => __('lang_v1.select_a_date_range'),
                    'class' => 'form-control',
                    'readonly',
                ]) !!}
            </div>
        </div>
    @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
            @can('purchase.create')
                @slot('tool')
                    <div class="box-tools">
                        {{-- <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\PurchaseController::class, 'create'])}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a> --}}
                        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                            href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a>
                    </div>
                @endslot
            @endcan
            @include('purchase.partials.purchase_table')
        @endcomponent

        <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        @include('purchase.partials.update_purchase_status_modal')

    </section>

    <section id="receipt_section" class="print_section"></section>

    <!-- /.content -->
@stop
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script>
        // Constants and configuration
        const tablename = "#purchase_table";
        const visibleColumnNames = @json($visibleColumnNames ?? []);
        const isViewMode = @json(isset($file_name));
        const reportName = "purchaseReport";
        const filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format;
        const dateSeparator = ' - ';

        // Main initialization function
        function initializePurchaseReport() {
            try {
                initializeDateRangePicker();
                setupEventListeners();
                applySavedFilters();
            } catch (error) {
                console.error('Error initializing purchase report:', error);
                toastr.error('Failed to initialize report features');
            }
        }

        function initializeDateRangePicker() {
            // Date range picker configuration
            $('#purchase_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    const displayDate = start.format(dateFormat) + dateSeparator + end.format(dateFormat);
                    $(this).val(displayDate);
                    validateAndApplyDateRange(start, end);
                }
            ).on('cancel.daterangepicker', function(ev) {
                $(this).val('');
                purchase_table.ajax.reload();
            });
        }

        function validateAndApplyDateRange(start, end) {
            if (start.isValid() && end.isValid()) {
                purchase_table.ajax.reload();
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
                    $('#purchase_list_filter_date_range').val(filterCriteria.dateRange);
                    $('#purchase_list_filter_date_range').data('daterangepicker').setStartDate(startDate);
                    $('#purchase_list_filter_date_range').data('daterangepicker').setEndDate(endDate);
                }
            }

            // Apply other filters
            const filterMap = {
                locationId: '#purchase_list_filter_location_id',
                supplierId: '#purchase_list_filter_supplier_id',
                status: '#purchase_list_filter_status',
                paymentStatus: '#purchase_list_filter_payment_status'
            };

            Object.entries(filterMap).forEach(([key, selector]) => {
                if (filterCriteria[key]) {
                    $(selector).val(filterCriteria[key]).trigger('change');
                }
            });
        }

        function setupEventListeners() {
            // Update status handlers
            $(document).on('click', '.update_status', function(e) {
                e.preventDefault();
                $('#update_purchase_status_form').find('#status').val($(this).data('status'));
                $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
                $('#update_purchase_status_modal').modal('show');
            });

            // Form submission handler
            $(document).on('submit', '#update_purchase_status_form', function(e) {
                e.preventDefault();
                const form = $(this);

                $.ajax({
                    method: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    beforeSend: () => __disable_submit_button(form.find('button[type="submit"]')),
                    success: (result) => {
                        if (result.success) {
                            $('#update_purchase_status_modal').modal('hide');
                            toastr.success(result.msg);
                            purchase_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                        form.find('button[type="submit"]').prop('disabled', false);
                    },
                    error: (xhr) => {
                        toastr.error(xhr.responseJSON?.msg || 'An error occurred');
                        form.find('button[type="submit"]').prop('disabled', false);
                    }
                });
            });
        }

        // Main initialization
        $(document).ready(function() {
            initializePurchaseReport();
        });
    </script>
    <script src="{{ asset('modules/minireportb1/js/purchase.js') }}"></script>
@endsection
