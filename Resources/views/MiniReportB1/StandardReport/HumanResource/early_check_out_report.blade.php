@extends('layouts.app')
@section('title', 'Fast Check Out Report')


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

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.fast_check_out_report')])

    <div class="reusable-table-container">
        <table class="reusable-table" id="attendance-table">
            <thead>
                <tr>
                    <th class="col-xs">@lang('minireportb1::minireportb1.number_sign')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.date')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.username')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.shift')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.clock_out_time')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.clock_out_location')</th>
                    <th class="col-xl">@lang('minireportb1::minireportb1.early_time')</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <!-- Data loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>@lang('minireportb1::minireportb1.total_early_check_outs')</strong></td>
                    <td class="number" id="total-early-checkouts">
                        <strong>0</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#attendance-table";
        const reportname = "Fast Check Out Report";
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
                    url: '{{ route('sr_early_check_out_report') }}', // Update route name
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#attendance-table-body').html(
                            '<tr><td colspan="7" class="text-center">Loading data...</td></tr>');
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
                                    $('<td>').text(row.clock_out_time),
                                    $('<td>').text(row.clock_out_location),
                                    $('<td>').text(row.early_time)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 6)
                                    .addClass('text-center')
                                    .text('No early check-outs found for the selected filters.')
                                )
                            );
                        }

                        $('#total-early-checkouts strong').text(response.total);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#attendance-table-body').html(
                            '<tr><td colspan="6" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadAttendanceData();

            // Event listener for date filter changes
            $('#f').on('change', function() {
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
                    alert('Please select both start and end dates.');
                }
            });
        });
    </script>
@endsection
