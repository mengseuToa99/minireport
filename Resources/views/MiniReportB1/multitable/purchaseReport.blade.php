@extends('minireportb1::layouts.master2')

@section('content')
    @include('purchase.index')
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


    var visibleColumnNames = @json($visibleColumnNames ?? []); // This is correct
    var isViewMode = @json(isset($file_name));
    var tablename = "#purchase_table";
    var reportName = "purchaseReport";
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

        $('#purchase_list_filter_date_range').val(filters.dateRange || '');
        $('#purchase_list_filter_location_id').val(filters.locationId || '');
        $('#purchase_list_filter_supplier_id').val(filters.supplierId || '');
        $('#purchase_list_filter_status').val(filters.status || '');
        $('#purchase_list_filter_payment_status').val(filters.paymentStatus || '');
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
