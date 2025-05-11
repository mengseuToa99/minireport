@extends('layouts.app')
@section('title', __('minireportb1::minireportb1.late_checkin_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

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

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.late_checkin_report')])



    <div class="reusable-table-container">
        <table class="reusable-table" id="attendance-table">
            <thead>
                <tr>
                    <th class="col-xs">@lang('minireportb1::minireportb1.number_sign')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.date')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.username')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.shift')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.clock_in_time')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.clock_in_location')</th>
                    <th class="col-xl">@lang('minireportb1::minireportb1.late_time')</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <!-- Data loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>@lang('minireportb1::minireportb1.total_late_check_ins'):</strong></td>
                    <td class="number" id="total-late-checkins">
                        <strong>0</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#income-table";
        const reportname = "@lang('minireportb1::minireportb1.late_checkin_report')";
    </script>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
    <!-- JavaScript for AJAX and Filters -->
    <script>
        $(document).ready(function() {
            // Initialize jQuery UI Datepicker
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
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
                    url: '{{ route('sr_late_check_in_report') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // You can add a loading indicator here if needed
                        $('#attendance-table-body').html(
                            '<tr><td colspan="7 class="text-center">@lang('messages.loading')</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#attendance-table-body');
                        tbody.empty();

                        if (response.attendance_data.length > 0) {
                            $.each(response.attendance_data, function(index, row) {
                                const tr = $('<tr>').append(
                                    $('<td>').text(index + 1),
                                    $('<td>').text(row.date),
                                    $('<td>').text(row.user),
                                    $('<td>').text(row.shift),
                                    $('<td>').text(row.clock_in_time),
                                    $('<td>').text(row.clock_in_location),
                                    $('<td>').text(row.late_time)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 6)
                                    .addClass('text-center')
                                    .text("@lang('minireportb1::minireportb1.no_late_check_ins')")
                                )
                            );
                        }

                        $('#total-late-checkins strong').text(response.total);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#attendance-table-body').html(
                            '<tr><td colspan="6" class="text-center text-danger">@lang('messages.error_loading_data')</td></tr>'
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
                    alert('@lang('minireportb1::minireportb1.please_select_dates')');
                }
            });

            // Apply filters button
            $('#apply_filters').on('click', function() {
                loadAttendanceData();
            });
        });
    </script>

@endsection
