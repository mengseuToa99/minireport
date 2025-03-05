<script>
    // Global variables
    var tablename = "#payrolls_table"; // Default value
    var reportName = "payrollReport"; // Default value
    const dateFormat = moment_date_format;
    const dateSeparator = ' - ';
    var visibleColumnNames = @json($visibleColumnNames ?? []);
    var isViewMode = @json(isset($file_name));
    var filterCriteria = @json($filterCriteria ?? []);

    // Initialize on page load
    $(document).ready(function() {
        // Set initial values based on active tab
        const initialTab = $('.nav-tabs .active a').attr('href');
        updateGlobalVariables(initialTab);

        // Initialize DataTables
        initializePayrollTable();
        initializePayrollGroupTable();
        initializePayComponentsTable();

        // Initialize filters and event handlers
        initializeMonthYearPicker();
        applySavedFilters();

        // Shared event handlers
        $(document).on('change', '#user_id_filter, #location_id_filter, #department_id, #designation_id, #month_year_filter', function() {
            payrolls_table.ajax.reload();
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
            const target = $(e.currentTarget).attr("href");
            updateGlobalVariables(target);
        });
    });

    // Function to update global variables based on active tab
    function updateGlobalVariables(target) {
        switch (target) {
            case "#payroll_group_tab":
                tablename = "#payroll_group_table";
                break;
            case "#pay_component_tab":
                tablename = "#ad_pc_table";
                break;
            default:
                tablename = "#payrolls_table";
        }
    }

    // Initialize Payroll Table
    function initializePayrollTable() {
        if ($('#payrolls_table').length) {
            payrolls_table = $('#payrolls_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'index']) }}",
                    data: function(d) {
                        if ($('#user_id_filter').length) {
                            d.user_id = $('#user_id_filter').val();
                        }
                        if ($('#location_id_filter').length) {
                            d.location_id = $('#location_id_filter').val();
                        }
                        d.month_year = $('#month_year_filter').val();
                        if ($('#department_id').length) {
                            d.department_id = $('#department_id').val();
                        }
                        if ($('#designation_id').length) {
                            d.designation_id = $('#designation_id').val();
                        }
                    },
                },
                columnDefs: [{
                    targets: 7,
                    orderable: false,
                    searchable: false,
                }],
                aaSorting: [[4, 'desc']],
                columns: [
                    { data: 'user', name: 'user', visible: isViewMode ? visibleColumnNames.includes("Employee") : true },
                    { data: 'department', name: 'dept.name', visible: isViewMode ? visibleColumnNames.includes("Department") : true },
                    { data: 'designation', name: 'dsgn.name', visible: isViewMode ? visibleColumnNames.includes("Designation") : true },
                    { data: 'transaction_date', name: 'transaction_date', visible: isViewMode ? visibleColumnNames.includes("Month/Year") : true },
                    { data: 'ref_no', name: 'ref_no', visible: isViewMode ? visibleColumnNames.includes("Reference No") : true },
                    { data: 'final_total', name: 'final_total', visible: isViewMode ? visibleColumnNames.includes("Total amount") : true },
                    { data: 'payment_status', name: 'payment_status', visible: isViewMode ? visibleColumnNames.includes("Payment Status") : true },
                    { data: 'action', name: 'action', visible: isViewMode ? visibleColumnNames.includes("Detail") : true }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#payrolls_table'));
                },
            });
        }
    }

    // Initialize Payroll Group Table
    function initializePayrollGroupTable() {
        if ($('#payroll_group_table').length) {
            payroll_group_table = $('#payroll_group_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'payrollGroupDatatable']) }}",
                aaSorting: [[6, 'desc']],
                columns: [
                    { data: 'name', name: 'essentials_payroll_groups.name', visible: isViewMode ? visibleColumnNames.includes("Name") : true },
                    { data: 'status', name: 'essentials_payroll_groups.status', visible: isViewMode ? visibleColumnNames.includes("Status") : true },
                    { data: 'payment_status', name: 'essentials_payroll_groups.payment_status', visible: isViewMode ? visibleColumnNames.includes("Payment Status") : true },
                    { data: 'gross_total', name: 'essentials_payroll_groups.gross_total', visible: isViewMode ? visibleColumnNames.includes("Total gross amount") : true },
                    { data: 'added_by', name: 'added_by', visible: isViewMode ? visibleColumnNames.includes("Added By") : true },
                    { data: 'location_name', name: 'BL.name', visible: isViewMode ? visibleColumnNames.includes("Location") : true },
                    { data: 'created_at', name: 'essentials_payroll_groups.created_at', searchable: false, visible: isViewMode ? visibleColumnNames.includes("Created At") : true },
                    { data: 'action', name: 'action', searchable: false, orderable: false, visible: isViewMode ? visibleColumnNames.includes("Detail") : true }
                ]
            });
        }
    }

    // Initialize Pay Components Table
    function initializePayComponentsTable() {
        if ($('#ad_pc_table').length) {
            ad_pc_table = $('#ad_pc_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ action([\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController::class, 'index']) }}",
                columns: [
                    { data: 'description', name: 'description', visible: isViewMode ? visibleColumnNames.includes("Description") : true },
                    { data: 'type', name: 'type', visible: isViewMode ? visibleColumnNames.includes("Type") : true },
                    { data: 'amount', name: 'amount', visible: isViewMode ? visibleColumnNames.includes("Amount") : true },
                    { data: 'applicable_date', name: 'applicable_date', visible: isViewMode ? visibleColumnNames.includes("Applicable Date") : true },
                    { data: 'employees', searchable: false, orderable: false, visible: isViewMode ? visibleColumnNames.includes("Employee") : true },
                    { data: 'action', name: 'action', visible: isViewMode ? visibleColumnNames.includes("Detail") : true }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#ad_pc_table'));
                },
            });
        }
    }

    // Apply saved filters
    function applySavedFilters() {
        if (!isViewMode) return;

        if (filterCriteria.dateRange) {
            $('#month_year_filter').val(filterCriteria.dateRange);
        }

        const filterMap = {
            locationId: '#location_id_filter',
            userId: '#user_id_filter',
            departmentId: '#department_id',
            designationId: '#designation_id'
        };

        Object.entries(filterMap).forEach(([key, selector]) => {
            if (filterCriteria[key]) $(selector).val(filterCriteria[key]).trigger('change');
        });

        payrolls_table.ajax.reload();
    }

    // Initialize month/year picker
    function initializeMonthYearPicker() {
        $('#month_year_filter').datepicker({
            autoclose: true,
            format: 'mm/yyyy',
            minViewMode: "months"
        }).on('changeDate', function() {
            payrolls_table.ajax.reload();
        });
    }
</script>

<!-- External Libraries -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>