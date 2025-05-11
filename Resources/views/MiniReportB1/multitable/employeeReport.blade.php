@section('content')
    @extends('minireportb1::layouts.master2')
@section('title', __('user.users'))
@include('manage_user.index')
@endsection

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include SweetAlert2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Hide nav bar and add button
    document.addEventListener('DOMContentLoaded', function() {

        const addButton = document.querySelector('.box-tools');

        if (addButton) { // Check both exist
            addButton.style.display = 'none';
        }
    });

    // Declare variables
    var visibleColumnNames = @json($visibleColumnNames ?? []); // This is correct
    var isViewMode = @json(isset($file_name));
    var tablename = "#users_table";
    var reportName = "employeeReport";

    var filterCriteria = @json($filterCriteria ?? []);
    const dateFormat = 'YYYY-MM-DD'; // Define your date format here
    const dateSeparator = ' - ';
</script>

<!-- For visible column -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (!isViewMode) {
                return;
            }

            // Function to hide all columns except specified ones
            function showOnlySpecificColumns(tableSelector, columnsToShow) {
                try {
                    var table = $(tableSelector);
                    if (table.length === 0) {
                        console.error('Table not found with selector: ' + tableSelector);
                        return;
                    }

                    // Check if the table is a DataTable
                    if (!$.fn.DataTable || !$.fn.DataTable.isDataTable(tableSelector)) {
                        console.error(
                            'The table is not a DataTable. Ensure DataTables is initialized.');
                        return;
                    }

                    var dataTable = $(tableSelector).DataTable();

                    // Get total columns
                    var totalColumns = dataTable.columns().count();
                    console.log('Total columns detected: ' + totalColumns);

                    // Create an array of all column indexes
                    var allColumns = [];
                    for (var i = 0; i < totalColumns; i++) {
                        allColumns.push(i);
                    }

                    // Log column mapping for reference
                    console.log('Column mapping:');
                    dataTable.columns().every(function(index) {
                        var header = $(this.header());
                        console.log('Column ' + index + ': ' + header.text().trim());
                    });

                    // Filter out the columns we want to show
                    var columnsToHide = allColumns.filter(function(index) {
                        return !columnsToShow.includes(index);
                    });

                    console.log('Columns to show:', columnsToShow);
                    console.log('Columns to hide:', columnsToHide);

                    // Hide columns using DataTables API
                    columnsToHide.forEach(function(index) {
                        dataTable.column(index).visible(false);
                    });

                    console.log('DataTables columns hidden successfully');
                } catch (error) {
                    console.error('Error in showOnlySpecificColumns: ' + error.message);
                }
            }

            // Define columns to show (zero-based indexes)
            var columnsToShow = visibleColumnNames; // Example: Show only these columns

            // Call the function
            showOnlySpecificColumns(tablename, columnsToShow);
        }, 500);
    });
</script>


<script>
    console.log("filter", filterCriteria);
    $(document).ready(function() {
        // Extract only the filterCriteria object
        var filters = filterCriteria;

        // Apply filter criteria to the UI
        $('#allow_login').prop('checked', filters.allowLogin == 1);
        $('#not_allow_login').prop('checked', filters.notAllowLogin == 1);
        $('#user').val(filters.user);
        $('#role').val(filters.role);

        // Trigger DataTable reload to apply the initial filters
        users_table.ajax.reload();
    });
</script>
