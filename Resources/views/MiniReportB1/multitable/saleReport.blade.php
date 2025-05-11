@extends('minireportb1::layouts.master2')

@section('content')
    @include('sell.index')
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
    var tablename = "#sell_table";
    var reportName = "saleReport";

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

        $('#sell_list_filter_date_range').val(filters.dateRange);
        $('#sell_list_filter_location_id').val(filters.locationId);
        $('#sell_list_filter_customer_id').val(filters.customerId);
        $('#sell_list_filter_payment_status').val(filters.paymentStatus);
        $('#created_by').val(filters.createdBy);
        $('#sales_cmsn_agnt').val(filters.salesCommissionAgent);
        $('#service_staffs').val(filters.serviceStaff);
        $('#shipping_status').val(filters.shippingStatus);
        $('#sell_list_filter_source').val(filters.source);
        $('#payment_method').val(ilters.paymentMethod);
        $('#only_subscriptions').prop('checked', filters.onlySubscriptions == 1);

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
