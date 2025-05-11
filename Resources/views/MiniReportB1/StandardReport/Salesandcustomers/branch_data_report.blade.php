@extends('layouts.app')
@section('title', 'Income for The Month')

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')

@include('minireportb1::MiniReportB1.components.back_to_dashboard_button')

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

    <div class="report-header" id="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-light tw-text-center normal-view-title" style="font-size: 20px;">
            {{ $business_name }}
        </h2>
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            តារាងព័ត៌មានសាខា
        </h2>
    </div>

    <div class="reusable-table-container">
        <table class="reusable-table" id="branch-data-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-md">Contact</th>
                    <th class="col-md">Phone Number</th>
                    <th class="col-xl">Address</th>
                    <th class="col-md">Description</th>
                </tr>
            </thead>
            <tbody id="branch-table-body">
                <!-- Data loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">
                        <strong>Total Records:</strong> <span id="total-records">0</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#branch-data-table";
        const reportname = "Branch Data Report";
    </script>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

    <!-- JavaScript for AJAX and Filters -->
    <script>
        $(document).ready(function() {
            // Initialize jQuery UI Datepicker
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
            });

            // Function to load branch data via AJAX
            function loadBranchData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    username_filter: $('#username_filter').val(),
                };

                $.ajax({
                    url: '{{ route('sr_branchDataReport') }}',
                    type: 'GET',
                    dataType: 'json', // Ensure this is set to 'json'
                    data: formData,
                    beforeSend: function() {
                        $('#branch-table-body').html(
                            '<tr><td colspan="5" class="text-center">Loading data...</td></tr>'
                        );
                    },
                    success: function(response) {
                        const tbody = $('#branch-table-body');
                        tbody.empty();

                        if (response.success && response.branch_data && response.branch_data.length >
                            0) {
                            $.each(response.branch_data, function(index, row) {
                                const tr = $('<tr>').append(
                                    $('<td>').text(index + 1),
                                    $('<td>').text(row.contact || ''),
                                    $('<td>').text(row.phone_number || ''),
                                    $('<td>').text(row.address || ''),
                                    $('<td>').html(row.description ||
                                    '') // Use .html() for description with HTML tags
                                );
                                tbody.append(tr);
                            });
                            $('#total-records').text(response.total);
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 5)
                                    .addClass('text-center')
                                    .text(response.message ||
                                        'No data available for the selected period.')
                                )
                            );
                            $('#total-records').text('0');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        $('#branch-table-body').html(
                            '<tr><td colspan="5" class="text-center text-danger">Error loading data: ' +
                            (xhr.responseJSON?.message || 'Please try again.') +
                            '</td></tr>'
                        );
                        $('#total-records').text('0');
                    }
                });
            }

            // Initial load
            loadBranchData();

            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadBranchData();
                }
            });

            // Event listener for username filter changes
            $('#username_filter').on('change', function() {
                loadBranchData();
            });

            // Event listener for custom range apply button
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadBranchData();
                } else {
                    alert('Please select both start and end dates.');
                }
            });

            // Apply filters button
            $('#apply_filters').on('click', function() {
                loadBranchData();
            });
        });
    </script>

@endsection
