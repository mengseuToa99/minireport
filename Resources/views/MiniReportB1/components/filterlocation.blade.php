<div class="report-filter">
    <p>@lang('business.location')</p>
    <div class="filter-group">
        <select class="filter-select" name="location_filter" id="location_filter">
            <option value="">@lang('messages.all')</option>
            @foreach($locations as $id => $name)
                <option value="{{ $id }}" {{ request()->get('location_filter') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>