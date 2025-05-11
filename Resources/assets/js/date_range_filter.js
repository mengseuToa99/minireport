            // Handle the visibility of custom date range inputs
            function updateDateRangeVisibility() {
                const dateFilter = $('#date_filter').val();
                if (dateFilter === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    // Load data automatically when changing filter (except for custom range)
                    if (dateFilter) {
                        currentPage = 1; // Reset to page 1 when changing filters
                        loadData();
                    }
                }
            }

            // Initial visibility based on default selection
            updateDateRangeVisibility();



            // Event listener for date filter changes
            $('#date_filter').on('change', updateDateRangeVisibility);

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    currentPage = 1; // Reset to page 1 when applying custom range
                    loadData();
                } else {
                    alert('Please select both start and end dates');
                }
            });

                        $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadData(); // Reload data for non-custom range selections
                }
            });

            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadData(); // Reload data for custom range
                } else {
                    alert('Please select both start and end dates');
                }
            });

            // Apply filters button
            $('#apply_filters').on('click', function() {
                loadData();
            });