<div class="report-period">
    <p>{{ trans('minireportb1::minireportb1.report_period') }}</p>
    <div class="filter-group">
        <!-- Dropdown for date filter -->
        <select class="filter-select" name="date_filter" id="date_filter">
            <!-- Default option -->
            @if (isset($start_date) && isset($end_date))
                @if(request('date_filter') === 'custom_month_range')
                    <option value="custom_month_range" selected>
                        {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
                        -
                        {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                    </option>
                @endif
            @else
                <option value="" selected>{{ trans('minireportb1::minireportb1.all_dates') }}</option>
            @endif

            <!-- Standard options -->
            <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.today') }}</option>
            <option value="this_month" {{ (!request('date_filter') || request('date_filter') === 'this_month') && !request('start_date') ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.this_month') }}</option>
            <option value="last_month" {{ request('date_filter') === 'last_month' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.last_month') }}</option>
            <option value="last_3_months" {{ request('date_filter') === 'last_3_months' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.last_3_months') }}</option>
            <option value="last_6_months" {{ request('date_filter') === 'last_6_months' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.last_6_months') }}</option>
            <option value="this_quarter" {{ request('date_filter') === 'this_quarter' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.this_quarter') }}</option>
            <option value="last_quarter" {{ request('date_filter') === 'last_quarter' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.last_quarter') }}</option>
            <option value="this_year" {{ request('date_filter') === 'this_year' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.this_year') }}</option>
            <option value="last_year" {{ request('date_filter') === 'last_year' ? 'selected' : '' }}>{{ trans('minireportb1::minireportb1.last_year') }}</option>
            <option value="custom_month_range">{{ trans('minireportb1::minireportb1.custom_month_range') }}</option>
        </select>

        <!-- Custom range inputs -->
        <div id="custom_range_inputs" style="{{ request('date_filter') === 'custom_month_range' ? '' : 'display: none;' }} margin-top: 8px;">
            <div class="date-inputs">
                <input type="text" id="start_date" name="start_date" class="date-input"
                    placeholder="{{ trans('minireportb1::minireportb1.start_date') }}" value="{{ request('start_date', isset($start_date) ? $start_date->format('Y-m-d') : '') }}">
                <input type="text" id="end_date" name="end_date" class="date-input" 
                    placeholder="{{ trans('minireportb1::minireportb1.end_date') }}"
                    value="{{ request('end_date', isset($end_date) ? $end_date->format('Y-m-d') : '') }}">
                <button type="button" id="apply_custom_range" class="run-report-btn">{{ trans('minireportb1::minireportb1.apply') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('date_filter');
    const customRangeInputs = document.getElementById('custom_range_inputs');
    
    // Toggle custom date range visibility
    dateFilter.addEventListener('change', function() {
        if (this.value === 'custom_month_range') {
            customRangeInputs.style.display = 'block';
        } else {
            customRangeInputs.style.display = 'none';
            
            // If not custom range, submit the form immediately
            if (this.form) {
                this.form.submit();
            }
        }
    });
    
    // Initialize date pickers (assuming you're using a library like jQuery UI)
    if (typeof $.fn.datepicker !== 'undefined') {
        $('#start_date, #end_date').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    }
    
    // Apply custom range button
    const applyCustomRange = document.getElementById('apply_custom_range');
    if (applyCustomRange) {
        applyCustomRange.addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                // Update the dropdown to show the selected range
                const customOption = dateFilter.querySelector('option[value="custom_month_range"]');
                if (customOption) {
                    const startFormatted = new Date(startDate).toLocaleDateString('en-GB');
                    const endFormatted = new Date(endDate).toLocaleDateString('en-GB');
                    customOption.textContent = `${startFormatted} - ${endFormatted}`;
                    customOption.selected = true;
                }
                
                // Submit the form
                if (dateFilter.form) {
                    dateFilter.form.submit();
                }
            }
        });
    }
});
</script>