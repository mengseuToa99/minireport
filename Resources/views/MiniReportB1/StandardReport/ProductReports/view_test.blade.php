@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.product_stock_history')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['title' => $product->name])
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('product_id', __('sale.product') . ':') !!}
                        {!! Form::select('product_id', [$product->id => $product->name . ' - ' . $product->sku], $product->id, ['class' => 'form-control', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, request()->input('location_id', null), ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                @if($product->type == 'variable')
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="variation_id">@lang('product.variations'):</label>
                            <select class="select2 form-control" name="variation_id" id="variation_id">
                                @foreach($product->variations as $variation)
                                    <option value="{{ $variation->id }}"
                                        @if(request()->input('variation_id', null) == $variation->id) selected @endif>
                                        {{ $variation->product_variation->name }} - {{ $variation->name }} ({{ $variation->sub_sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <input type="hidden" id="variation_id" name="variation_id" value="{{ $product->variations->first()->id }}">
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('filter_date', __('lang_v1.filter_date_range') . ':') !!}
                        {!! Form::text('filter_date', request()->input('filter_date', null), ['class' => 'form-control daterange', 'id' => 'filter_date', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent

            @component('components.widget')
                <div id="product_stock_history" style="display: none;"></div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function () {
        // Load stock history on page load
        loadStockHistory();

        // Initialize Select2 for product selection
        $('#product_id').select2({
            ajax: {
                url: '/products/list-no-variation',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
            minimumInputLength: 1,
            escapeMarkup: function (m) {
                return m;
            },
        }).on('select2:select', function (e) {
            var data = e.params.data;
            window.location.href = "{{ url('/') }}/quickbooks/test123/" + data.id;
        });

        // Initialize date range picker
        $('.daterange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: "@lang('lang_v1.apply')",
                cancelLabel: "@lang('lang_v1.cancel')",
                customRangeLabel: "@lang('lang_v1.custom_range')",
            },
            ranges: {
                "@lang('lang_v1.today')": [moment(), moment()],
                "@lang('lang_v1.yesterday')": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "@lang('lang_v1.last_7_days')": [moment().subtract(6, 'days'), moment()],
                "@lang('lang_v1.last_30_days')": [moment().subtract(29, 'days'), moment()],
                "@lang('lang_v1.this_month')": [moment().startOf('month'), moment().endOf('month')],
                "@lang('lang_v1.last_month')": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                "@lang('lang_v1.this_year')": [moment().startOf('year'), moment().endOf('year')],
                "@lang('lang_v1.last_year')": [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            },
            autoUpdateInput: false,
            showDropdowns: false,
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            loadStockHistory();
        });

        // Reload stock history when other filters change
        $('#variation_id, #location_id').change(function () {
            loadStockHistory();
        });
    });

    // Function to load stock history via AJAX
    function loadStockHistory() {
        $('#product_stock_history').fadeOut();

        var filter_date = $('#filter_date').val();
        var start_date = null, end_date = null;

        if (filter_date) {
            var dates = filter_date.split(' - ');
            start_date = dates[0];
            end_date = dates[1];
        }

        if (!$('#variation_id').val() || !$('#location_id').val()) {
            console.warn('Variation ID or Location ID is missing.');
            return;
        }

        $.ajax({
            url: '/products/stock-history/' + $('#variation_id').val(),
            data: {
                location_id: $('#location_id').val(),
                start_date: start_date,
                end_date: end_date,
            },
            dataType: 'html',
            success: function (result) {
                $('#product_stock_history').html(result).fadeIn();
                __currency_convert_recursively($('#product_stock_history'));

                $('#stock_history_table').DataTable({
                    searching: false,
                    fixedHeader: false,
                    ordering: false,
                });
            },
            error: function (xhr) {
                console.error('Error loading stock history:', xhr.responseText);
            },
        });
    }
</script>
@endsection