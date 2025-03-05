@extends('minireportb1::layouts.master2')
@section('title', __('expense.expenses'))

@section('content')

    <section class="content-header no-print">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @isset($file_name)
                {{ $file_name }} <!-- Display the file name if it exists -->
            @else
                @lang('expense.expenses') <!-- Fall back to the default title -->
            @endisset
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
                    @if (auth()->user()->can('all_expense.access'))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('expense_for', __('expense.expense_for') . ':') !!}
                                {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('expense_contact_filter', __('contact.contact') . ':') !!}
                                {!! Form::select('expense_contact_filter', $contacts, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_category_id', __('expense.expense_category') . ':') !!}
                            {!! Form::select('expense_category_id', $categories, null, [
                                'placeholder' => __('report.all'),
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'expense_category_id',
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_sub_category_id_filter', __('product.sub_category') . ':') !!}
                            {!! Form::select('expense_sub_category_id_filter', $sub_categories, null, [
                                'placeholder' => __('report.all'),
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'expense_sub_category_id_filter',
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'id' => 'expense_date_range',
                                'readonly',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_payment_status', __('purchase.payment_status') . ':') !!}
                            {!! Form::select(
                                'expense_payment_status',
                                ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial')],
                                null,
                                ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')],
                            ) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('audit_status', __('lang_v1.audit_status') . ':') !!}
                            {!! Form::select('audit_status', $audit_statuses, null, [
                                'class' => 'form-control select2',
                                'id' => 'audit_status',
                                'style' => 'width:100%',
                                'placeholder' => __('lang_v1.all'),
                            ]) !!}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>





        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('expense.all_expenses')])
                    @can('expense.add')
                        @slot('tool')
                            <div class="box-tools">
                                {{-- <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\ExpenseController::class, 'create'])}}">
                            <i class="fa fa-plus"></i> @lang('messages.add')</a> --}}
                            </div>
                        @endslot
                    @endcan
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expense_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.action')</th>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('purchase.ref_no')</th>
                                    <th>@lang('lang_v1.recur_details')</th>
                                    <th>@lang('expense.expense_category')</th>
                                    <th>@lang('product.sub_category')</th>
                                    <th>@lang('business.location')</th>
                                    <th>@lang('sale.audit_status')</th>
                                    <th>@lang('sale.payment_status')</th>
                                    <th>@lang('product.tax')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('purchase.payment_due')
                                    <th>@lang('expense.expense_for')</th>
                                    <th>@lang('contact.contact')</th>
                                    <th>@lang('expense.expense_note')</th>
                                    <th>@lang('lang_v1.added_by')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="7"><strong>@lang('sale.total'):</strong></td>
                                    <td class="footer_payment_status_count"></td>
                                    <td></td>
                                    <td class="footer_expense_total"></td>
                                    <td class="footer_total_due"></td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>

    </section>
    <!-- /.content -->
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
@stop

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('javascript')


    </script>
    <script src="{{ asset('modules/minireportb1/js/payment.js') }}"></script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

    <script>
        const tablename = "#expense_table";
        const visibleColumnNames = @json($visibleColumnNames ?? []);
        const isViewMode = @json(isset($file_name));
        const reportName = "exspenseReport";
        const filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format;
        const dateSeparator = ' - ';

        $(document).ready(function() {
    // Delay execution by 500ms (adjust as needed)
    setTimeout(function() {
        if (typeof expense_table !== 'undefined') {
            // Hide all columns
            var columnCount = expense_table.columns().count();
            for (var i = 0; i < columnCount; i++) {
                expense_table.column(i).visible(false);
            }

            // Show only the columns you want
            expense_table.column(0).visible(true);  // Show "Action" column
            expense_table.column(1).visible(true);  // Show "Date" column

            // Redraw the table
            expense_table.draw();
        } else {
            console.error("expense_table is not defined. Ensure the DataTable is initialized before this script runs.");
        }
    }, 500); // 500ms delay
});



@endsection
