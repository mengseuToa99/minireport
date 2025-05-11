@extends('minireportb1::layouts.master2')

@section('content')
    @include('expense.index')
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
    var tablename = "#expense_table";
    var reportName = "expenseReport";

    var filterCriteria = @json($filterCriteria ?? []);

</script>


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

        $('#location_id').val(filters.locationId || '');
        $('#expense_for').val(filters.expenseFor || '');
        $('#expense_contact_filter').val(filters.expenseContact || '');
        $('#expense_contact_id').val(filters.expenseContactId || '');
        $('#expense_sub_category_id_filter').val(filters.expenseSubCategoryId || '');
        $('#expense_category_id').val(filters.expenseCategoryId || '');
        $('#expense_date_range').val(filters.dateRange || '');
        $('#expense_payment_status').val(filters.paymentStatus || '');
        $('#audit_status').val(filters.auditStatus || '');
        
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
