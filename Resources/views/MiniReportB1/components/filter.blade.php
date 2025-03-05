<style>
    .report-period,
    .accounting-method {
        margin-bottom: 20px;
    }

    h3 {
        font-size: 14px;
        color: #555;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .filter-group {
        position: relative;
        max-width: 300px;
        display: flex;
        /* Enable Flexbox */
        flex-direction: row;
        /* Ensure horizontal layout (optional, as it's default) */
        gap: 12px;
        /* Optional: Add spacing between children */
    }

    .filter-select {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: white;
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
    }

    .filter-select:focus {
        border-color: #0f8800;
        outline: none;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }

    .radio-group {
        display: flex;
        gap: 16px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .radio-label input[type="radio"] {
        margin-right: 8px;
    }

    .radio-text {
        font-size: 14px;
        color: #0f8800;
    }

    .run-report-btn {
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .run-report-btn:hover {
        background-color: #0f8800;
    }

    .date-inputs {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        max-width: 300px;
    }

    .date-input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .date-input:focus {
        border-color: #0f8800;
        outline: none;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }

    .btn-container {
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: flex-end;
        padding: 10px;
        flex-wrap: wrap;
    }

    /* Style for both buttons */
    .print-button,
    .excel-button {
        display: flex;
        /* Make buttons flex containers */
        align-items: center;
        /* Center icons vertically */
        justify-content: center;
        /* Center icons horizontally, if needed */
        padding: 8px;
        /* Consistent padding */
        border: none;
        /* Optional: clean up default styles */
        cursor: pointer;
    }

    /* Icon styles */
    .print-icon,
    .excel-icon {
        width: 24px;
        /* Set a consistent size */
        height: 24px;
        /* Match height to width */
        background-size: contain;
        /* Ensure icons scale properly */
        display: inline-block;
        /* Ensure spans behave as blocks */
    }

    /* Specific icon backgrounds (adjust as needed) */
    .print-icon {
        background: url('path-to-print-icon.png') no-repeat center;
    }

    .excel-icon {
        background: url('path-to-excel-icon.png') no-repeat center;
    }

    /* Optional: Ensure buttons stay manageable on small screens */
    @media (max-width: 768px) {
        .btn-container {
            justify-content: center;
            /* Center buttons on smaller screens */
            gap: 8px;
            /* Reduce gap for tighter layout */
        }
    }
</style>

<style>
    /* [Previous styles unchanged] */

    .date-inputs {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        max-width: 300px;
    }

    .date-input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #ddd;
        /* Light gray border */
        border-radius: 4px;
        font-size: 14px;
        background-color: #fff;
        /* Explicitly white background */
        color: #333;
        /* Dark gray text for readability, not black */
    }

    .date-input:focus {
        border-color: #0f8800;
        /* Blue border on focus */
        outline: none;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }

    /* Override jQuery UI Datepicker input styles */
    .ui-datepicker .date-input {
        background-color: #fff !important;
        /* Force white background */
        color: #333 !important;
        /* Dark gray text, not black */
        border: 1px solid #ddd !important;
        /* Match your design */
    }

    /* Ensure placeholder text is light-themed */
    .date-input::placeholder {
        color: #999;
        /* Light gray placeholder */
        opacity: 1;
        /* Ensure visibility */
    }
</style>

{{-- print button animation --}}
<style>
    button.print-button {
        width: 50px;
        /* Smaller width */
        height: 50px;
        /* Smaller height */
    }

    span.print-icon,
    span.print-icon::before,
    span.print-icon::after,
    button.print-button:hover .print-icon::after {
        border: solid 2px #0f8800;
        /* Updated color */
    }

    span.print-icon::after {
        border-width: 1px;
        /* Thinner border */
    }

    button.print-button {
        margin-top: 8px;
        position: relative;
        padding: 0;
        border: 0;
        background: transparent;
    }

    span.print-icon,
    span.print-icon::before,
    span.print-icon::after,
    button.print-button:hover .print-icon::after {
        box-sizing: border-box;
        background-color: #fff;
    }

    span.print-icon {
        position: relative;
        display: inline-block;
        padding: 0;
        margin-top: 20%;

        width: 60%;
        /* Adjusted width */
        height: 35%;
        /* Adjusted height */
        background: #fff;
        border-radius: 20% 20% 0 0;
    }

    span.print-icon::before {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 12%;
        right: 12%;
        height: 110%;

        transition: height .2s .15s;
    }

    span.print-icon::after {
        content: "";
        position: absolute;
        top: 55%;
        left: 12%;
        right: 12%;
        height: 0%;
        background: #fff;
        background-repeat: no-repeat;
        background-size: 70% 90%;
        background-position: center;
        background-image: linear-gradient(to top,
                #fff 0, #fff 14%,
                #0f8800 14%, #0f8800 28%,
                /* Updated color */
                #fff 28%, #fff 42%,
                #0f8800 42%, #0f8800 56%,
                /* Updated color */
                #fff 56%, #fff 70%,
                #0f8800 70%, #0f8800 84%,
                /* Updated color */
                #fff 84%, #fff 100%);

        transition: height .2s, border-width 0s .2s, width 0s .2s;
    }

    button.print-button:hover {
        cursor: pointer;
    }

    button.print-button:hover .print-icon::before {
        height: 0px;
        transition: height .2s;
    }

    button.print-button:hover .print-icon::after {
        height: 120%;
        transition: height .2s .15s, border-width 0s .16s;
    }
</style>

<style>
    .excel-button {
        width: 40px;
        /* Match print-button width */
        height: 80px;
        /* Match print-button height */
        position: relative;
        padding: 0;
        /* Consistent with print-button */
        border: none;
        /* Consistent with print-button */
        background: transparent;
        /* Consistent with print-button */
        transition: transform 0.2s ease;
    }

    .excel-button:hover {
        cursor: pointer;
        /* Consistent with print-button */
        transform: scale(1.05);
    }

    .excel-icon {
        position: relative;
        display: inline-block;
        padding: 0;
        /* Consistent with print-icon */
        margin-top: 20%;
        /* Match print-icon margin-top */
        width: 60%;
        /* Match print-icon width */
        height: 35%;
        /* Match print-icon height */
        background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%);
        border: solid 2px #217346;
        /* Reduced from 4px to align with print-icon's 2px */
        border-radius: 3px;
        /* Slightly adjusted from print-icon's 20% for Excel aesthetic */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.16);
        /* Scaled down shadow for smaller size */
        transition: all 0.2s ease;
        /* Slightly faster to match print-button's 0.2s */
        overflow: hidden;
    }

    /* Excel top bar */
    .excel-icon::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 20%;
        /* Kept proportional to smaller height */
        background: #217346;
        border: none;
        border-radius: 2px 2px 0 0;
        transition: all 0.2s ease;
        /* Match print-button's transition timing */
    }

    /* Grid cells */
    .excel-icon::after {
        content: "";
        position: absolute;
        top: 20%;
        left: 0;
        right: 0;
        height: 80%;
        /* Kept proportional */
        background-color: #fff;
        background-image:
            linear-gradient(90deg, #217346 1px, transparent 1px),
            linear-gradient(0deg, #217346 1px, transparent 1px),
            linear-gradient(90deg, rgba(33, 115, 70, 0.2) 1px, transparent 1px),
            linear-gradient(0deg, rgba(33, 115, 70, 0.2) 1px, transparent 1px);
        background-size:
            25% 20%,
            25% 20%,
            12.5% 10%,
            12.5% 10%;
        opacity: 0;
        transform: translateY(100%);
        transition: all 0.2s ease;
        /* Match print-button's 0.2s transitions */
    }

    /* Hover states */
    .excel-button:hover .excel-icon {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        /* Scaled down shadow for smaller size */
    }

    .excel-button:hover .excel-icon::before {
        background: #1a5c37;
        /* Darker shade on hover, consistent with original */
    }

    /* Cell animation on hover */
    .excel-button:hover .excel-icon::after {
        opacity: 1;
        transform: translateY(0);
    }

    /* Cell animation on hover out */
    .excel-button:not(:hover) .excel-icon::after {
        opacity: 0;
        transform: translateY(100%);
        transition: all 0.2s ease;
    }
</style>


<style>
    @media print {
        .no-print {
            display: none;
        }
    }
</style>


<div class="report-filters">
    <form id="filterForm" method="GET">
        @unless ($hideDateFilter ?? false)
            <div class="report-period">
                <h3>Report period</h3>
                <div class="filter-group">
                    <!-- Dropdown for date filter -->
                    <select class="filter-select" name="date_filter" id="date_filter">
                        <!-- Custom option showing filtered range -->
                        @if (isset($start_date) && isset($end_date))
                            <option value="custom" selected>
                                {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
                                -
                                {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                            </option>
                        @else
                            <option value="" selected>All Dates</option>
                        @endif

                        <!-- Standard options -->
                        <option value="">All Dates</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_3_months">Last 3 Months</option>
                        <option value="last_6_months">Last 6 Months</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="last_quarter">Last Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="last_year">Last Year</option>
                        <option value="custom_month_range">Custom Month Range</option>
                    </select>


                    <!-- Custom range inputs -->
                    <div id="custom_range_inputs" style="display: none; margin-top: 8px;">
                        <div class="date-inputs">
                            <input type="text" id="start_date" name="start_date" class="date-input"
                                placeholder="Start Date" value="{{ $start_date ?? '' }}">
                            <input type="text" id="end_date" name="end_date" class="date-input" placeholder="End Date"
                                value="{{ $end_date ?? '' }}">
                            <button type="button" id="apply_custom_range" class="run-report-btn">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        @endunless
    </form>
</div>

<div class="btn-container">
    <button class="btn" id="printButton">
        <i class="fas fa-sliders-h"></i>Custom
    </button>
    <button class="print-button" id="print-button"">
        <span class="print-icon"></span>
    </button>
    <button class="excel-button" id="exportExcelButton">
        <span class="excel-icon"></span>
    </button>
</div>

<script>
    const form = document.getElementById('filterForm');
    const dateFilter = document.getElementById('date_filter');
    const customRangeInputs = document.getElementById('custom_range_inputs');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const applyCustomRangeButton = document.getElementById('apply_custom_range');

    // Show/hide custom range inputs based on dropdown selection
    dateFilter.addEventListener('change', function() {
        if (this.value === 'custom_month_range') {
            customRangeInputs.style.display = 'block';
        } else {
            customRangeInputs.style.display = 'none';
            form.submit(); // Submit form for non-custom range selections
        }
    });

    // Submit form when "OK" button is clicked for custom range
    applyCustomRangeButton.addEventListener('click', function() {
        if (startDateInput.value && endDateInput.value) {
            form.submit();
        } else {
            alert('Please select both start and end dates.');
        }
    });

    // Initialize jQuery UI Datepicker
    $(document).ready(function() {
        $("#start_date").datepicker({
            dateFormat: "yy-mm-dd",
            onSelect: function(dateText, inst) {
                console.log('Start date selected:', dateText);
            }
        });
        $("#end_date").datepicker({
            dateFormat: "yy-mm-dd",
            onSelect: function(dateText, inst) {
                console.log('End date selected:', dateText);
            }
        });

        // Set initial datepicker values if provided
        @if (isset($start_date))
            $("#start_date").datepicker("setDate", "{{ $start_date }}");
        @endif
        @if (isset($end_date))
            $("#end_date").datepicker("setDate", "{{ $end_date }}");
        @endif
    });
</script>

<script>
    $('#print-button').on('click', function() {
        window.print();
    });
</script>
