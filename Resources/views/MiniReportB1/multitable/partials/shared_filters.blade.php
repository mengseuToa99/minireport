
@component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid', 'closed' => true])
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('location_id_filter', __('purchase.business_location') . ':') !!}
            {!! Form::select('location_id_filter', $locations, null, [
                'class' => 'form-control select2',
                'style' => 'width:100%',
                'placeholder' => __('lang_v1.all'),
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('month_year_filter', __('essentials::lang.month_year') . ':') !!}
            <div class="input-group">
                {!! Form::text('month_year_filter', null, [
                    'class' => 'form-control',
                    'placeholder' => __('essentials::lang.month_year'),
                ]) !!}
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
@endcomponent