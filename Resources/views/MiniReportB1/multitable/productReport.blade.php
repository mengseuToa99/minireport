@extends('minireportb1::layouts.master2')

@section('content')
    @include('product.index')
@endsection


@include('minireportb1::MiniReportB1.multitable.components.includelink')


<script>
    var visibleColumnNames = @json($visibleColumnNames ?? []);
    var isViewMode = @json(isset($file_name));
    var tablename = '#product_table';
    var reportName = 'productReport';

    // Use an object to store state globally
    const reportState = {
        tablename: '#product_table',
        reportName: 'productReport'
    };

    // Function to update table and report names
    function setTableAndReportNames(target) {
        switch(target) {
            case '#product_list_tab':
                reportState.tablename = '#product_table';
                reportState.reportName = 'productReport';
                break;
            case '#product_stock_report':
                reportState.tablename = '#product_stock_report';
                reportState.reportName = 'stockReport';
                break;
            default:
                reportState.tablename = '#product_table';
                reportState.reportName = 'productReport';
        }
        // Update UI or trigger actions based on new values
        updateTableDisplay();
        console.log('Updated reportState:', reportState);
    }

    // Example function to test the values
    function updateTableDisplay() {
        // This could be where you use tablename and reportName
        // For debugging, we'll just log it
        console.log('Current table:', reportState.tablename);
        console.log('Current report:', reportState.reportName);
        
        // Example: If you're using DataTables, you might reinitialize here
        // if ($.fn.DataTable.isDataTable(reportState.tablename)) {
        //     $(reportState.tablename).DataTable().destroy();
        // }
        // $(reportState.tablename).DataTable({ ... });
    }

    // Initialize on page load
    $(document).ready(function() {
        const initialTab = $('.nav-tabs li.active a').attr('href') || '#product_list_tab';
        console.log('Initial tab detected:', initialTab);
        setTableAndReportNames(initialTab);
    });

    // Handle tab switching
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
        const target = $(e.target).attr('href');
        if (target) {
            setTableAndReportNames(target);
        } else {
            console.warn('No target found for tab switch');
        }
    });

    var filterCriteria = @json($filterCriteria ?? []);

    // Test function to check values on demand
    window.checkReportState = function() {
        console.log('Current state:', reportState);
        return reportState;
    };
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

        $('#month_year_filter').val(filters.dateRange || '');
        $('#location_id_filter').val(filters.locationId || '');
        $('#product_list_filter_type').val(filters.type || '');
        $('#product_list_filter_category_id').val(filters.categoryId || '');
        $('#product_list_filter_unit_id').val(filters.unitId || '');
        $('#product_list_filter_tax_id').val(filters.taxId || '');
        $('#brand_id').val(filters.brandId || '');
        $('#active_state').val(filters.activeState || '');
        $('#not_for_selling').prop('checked', filters.notForSelling == 1);
        $('#woocommerce_enabled').prop('checked', filters.woocommerceEnabled == 1);


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
