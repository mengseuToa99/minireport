@extends('minireportb1::layouts.master2')
@section('title', __('lang_v1.' . $type . 's'))

@section('content')

    @include('contact.index')
@endsection

@include('minireportb1::MiniReportB1.multitable.components.includelink')

<script>
    // Hide nav bar and add button
    document.addEventListener('DOMContentLoaded', function() {
      
        const addButton = document.querySelector('.box-tools');

        if (addButton) { // Check both exist
            // navMenu.style.display = 'none';
            addButton.style.display = 'none';
        }
    });

    // Declare variables
    var visibleColumnNames = @json($visibleColumnNames ?? []); // This is correct
    var isViewMode = @json(isset($file_name));
    var tablename = "#contact_table";
    var reportName = "customerReport";

    var filterCriteria = @json($filterCriteria ?? []);
    const dateFormat = 'YYYY-MM-DD'; // Define your date format here
    const dateSeparator = ' - ';
</script>

<!-- For visible column -->


<script>
    console.log("filterCriteria", filterCriteria);
    console.log("isViewMode", isViewMode);

    $(document).ready(function() {
        if (!isViewMode) {
            console.log("Exiting early because isViewMode is false or undefined.");
            return;
        }

        // Extract only the filterCriteria object
        var filters = filterCriteria;
        console.log("filters", filters);

        // Apply filter criteria to the UI
        $('#has_sell_due').prop('checked', filters.hasSellDue == 1);
        $('#has_sell_return').prop('checked', filters.hasSellReturn == 1);
        $('#has_study_date').prop('checked', filters.hasStudyDate == 1);
        $('#has_expired_date').prop('checked', filters.hasExpiredDate == 1);
        $('#has_register_date').prop('checked', filters.hasRegisterDate == 1);
        $('#has_purchase_due').prop('checked', filters.hasPurchaseDue == 1);
        $('#has_purchase_return').prop('checked', filters.hasPurchaseReturn == 1);
        $('#has_advance_balance').prop('checked', filters.hasAdvanceBalance == 1);
        $('#has_opening_balance').prop('checked', filters.hasOpeningBalance == 1);
        $('#has_no_sell_from').val(filters.hasNoSellFrom);
        $('#cg_filter').val(filters.customerGroup);
        $('#assigned_to').val(filters.assignedTo);
        $('#status_filter').val(filters.status);
        $('#search_keyword').val(filters.searchKeyword);

        // Parse and initialize the date range picker
        if (filters.dateRange) {
            var dateRangeParts = filters.dateRange.split(' - ');
            var startDate = moment(dateRangeParts[0], 'DD-MM-YYYY').format('YYYY-MM-DD');
            var endDate = moment(dateRangeParts[1], 'DD-MM-YYYY').format('YYYY-MM-DD');
        } else {
            var startDate = moment().startOf('month').format('YYYY-MM-DD');
            var endDate = moment().endOf('month').format('YYYY-MM-DD');
        }

        console.log("Initializing date range picker with:", startDate, endDate);
        $('#contact_date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: startDate,
            endDate: endDate
        });

        // Trigger DataTable reload to apply the initial filters
        console.log("Reloading DataTable...");
        contact_table.ajax.reload(function(json) {
            console.log("DataTable reloaded with filters:", json);
        });
    });
</script>
