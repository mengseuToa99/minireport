@extends('layouts.app')
@section('title', 'Shift Schedule Report')

@include('minireportb1::MiniReportB1.components.linkforinclude')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

@section('content')

    <div style="margin: 16px" class="no-print">

        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <form id="filterForm">
                    @include('minireportb1::MiniReportB1.components.filterusername')

                    <div>
                        <label for="shift_filter">Shift Time</label>
                        <select name="shift_filter" id="shift_filter" class="form-control">
                            <option value="all">All Shifts</option>
                            <option value="08:00-17:00">8:00 - 17:00</option>
                            <option value="08:30-17:30">8:30 - 17:30</option>
                        </select>
                    </div>

                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent

    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => __('minireportb1::minireportb1.Time_Shift_Report')])


    <div class="reusable-table-container">
        <table class="reusable-table" id="shift-table">
            <thead>
                <tr>
                    <th class="col-xs">@lang('minireportb1::minireportb1.number_sign')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.username')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.shift')</th>
                </tr>
            </thead>
            <tbody id="shift-table-body">
                <!-- Data loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><strong>Total Users:</strong></td>
                    <td class="number" id="total-users">
                        <strong>0</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#shift-table";
        const reportname = "Shift Schedule Report";
    </script>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
    <!-- JavaScript for AJAX and Filters -->
    <script>
        $(document).ready(function() {
            // Function to load shift data via AJAX
            function loadShiftData() {
                const formData = {
                    user_id_filter: $('#username_filter').val(),
                    shift_filter: $('#shift_filter').val()
                };

                $.ajax({
                    url: '{{ route('sr_shift_schedule_report') }}', // Update to your actual route name
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#shift-table-body').html(
                            '<tr><td colspan="3" class="text-center">Loading data...</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#shift-table-body');
                        tbody.empty();

                        if (response.shift_data.length > 0) {
                            $.each(response.shift_data, function(index, row) {
                                const tr = $('<tr>').append(
                                    $('<td>').text(index + 1),
                                    $('<td>').text(row.user),
                                    $('<td>').text(row.shift)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 3)
                                    .addClass('text-center')
                                    .text('No shift records found for the selected filters.')
                                )
                            );
                        }

                        $('#total-users strong').text(response.total);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#shift-table-body').html(
                            '<tr><td colspan="3" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadShiftData();

            // Event listener for filter changes
            $('#username_filter, #shift_filter').on('change', function() {
                loadShiftData(); // Reload data when any filter changes
            });

            // Apply filters button (if present in filterusername component)
            $('#apply_filters').on('click', function() {
                loadShiftData();
            });
        });
    </script>

@endsection
