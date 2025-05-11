@extends('layouts.app')
@section('title', 'Monthly Attendance Report')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">


@endsection

@section('content')


    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <form id="filterForm">
                    @include('minireportb1::MiniReportB1.components.filterdate')
                    @include('minireportb1::MiniReportB1.components.filterusername')

                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.Attendance_Report')])


    <div class="reusable-table-container">
        <table class="reusable-table" id="attendance-table">
            <thead>
                <tr>
                    <th class="col-xs">@lang('minireportb1::minireportb1.number_sign')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.username')</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <!-- Data will be loaded here via AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        const tablename = "#income-table";
        const reportname = "Monthly Expense Report";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
    <!-- JavaScript for AJAX and Filters -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize jQuery UI Datepicker
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true, // Allow month selection
                changeYear: true // Allow year selection
            });
    
            // Function to load attendance data via AJAX
            function loadAttendanceData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    username_filter: $('#username_filter').val(),
                };
    
                $.ajax({
                    url: '{{ route('sr_list_attendance_report') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // You can add a loading indicator here if needed
                        $('#attendance-table-body').html(
                            '<tr><td colspan="32" class="text-center">Loading data...</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#attendance-table-body');
                        tbody.empty();
    
                        // Clear existing table head, except for # and Username
                        const thead = $('#attendance-table thead tr');
                        thead.empty().append(
                            '<th class="col-xs">#</th><th class="col-md">Username</th>');
                        // Add days to table head
                        for (let i = 1; i <= response.daysInMonth; i++) {
                            thead.append('<th class="col-xs">' + i + '</th>');
                        }
    
                        if (response.attendance_data.length > 0) {
                            $.each(response.attendance_data, function(index, employee) {
                                let row = '<tr>';
                                    row += '<td>' + (index + 1) + '</td>';
                                row += '<td>' + employee.username + '</td>';
    
                                for (let i = 1; i <= response.daysInMonth; i++) {
                                    row += '<td class="text-center">' + (employee[i] || '❌') +
                                        '</td>';
                                }
                                row += '</tr>';
                                tbody.append(row);
                            });
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 32)
                                    .addClass('text-center')
                                    .text('No attendance data found for the selected filters.')
                                )
                            );
                        }
    
                        $('#total-employees strong').text(response.total);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#attendance-table-body').html(
                            '<tr><td colspan="32" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                });
            }
    
            // Initial load
            loadAttendanceData();
    
            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    // Set start and end dates to empty string to prevent sending them.
                    $('#start_date').val('');
                    $('#end_date').val('');
                    loadAttendanceData(); // Reload data for non-custom range selections
                }
            });
    
            // Event listener for username filter changes
            $('#username_filter').on('change', function() {
                loadAttendanceData(); // Reload data when username filter changes
            });
    
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadAttendanceData(); // Reload data for custom range
                } else {
                    alert('Please select both start and end dates.');
                }
            });
    
            // Apply filters button
            $('#apply_filters').on('click', function() {
                loadAttendanceData();
            });
        });
    </script>

@endsection
