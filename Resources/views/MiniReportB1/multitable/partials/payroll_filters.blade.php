@component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid', 'closed' => true])
    @can('essentials.view_all_payroll')
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('user_id_filter', __('essentials::lang.employee') . ':') !!}
                {!! Form::select('user_id_filter', $employees, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all'),
                ]) !!}
            </div>
        </div>
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
                {!! Form::label('department_id', __('essentials::lang.department') . ':') !!}
                {!! Form::select('department_id', $departments, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all'),
                ]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('designation_id', __('essentials::lang.designation') . ':') !!}
                {!! Form::select('designation_id', $designations, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all'),
                ]) !!}
            </div>
        </div>
    @endcan
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