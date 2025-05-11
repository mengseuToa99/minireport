@extends('layouts.app')
@section('title', 'Customer No Map Report')

@include('minireportb1::MiniReportB1.components.linkforinclude')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

@section('content')

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <form id="filterForm">
                    @include('minireportb1::MiniReportB1.components.filterdate')

                    <div class="report-filter">
                        <p>Location</p>
                        <div class="filter-group">
                            <select class="filter-select" name="location_id" id="location_id">
                                <option value="">All Locations</option>
                                @foreach ($business_locations as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ request()->get('location_id') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="report-filter">
                        <p>Created By</p>
                        <div class="filter-group">
                            <select class="filter-select" name="created_by_filter" id="created_by_filter">
                                <option value="">All Users</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request()->get('created_by_filter') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name ?? $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="report-filter">
                        <p>Assigned To</p>
                        <div class="filter-group">
                            <select class="filter-select" name="assigned_to_filter" id="assigned_to_filter">
                                <option value="">All Users</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request()->get('assigned_to_filter') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name ?? $user->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </form>

                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'Accounts Receivable - Unpaid',
    ])

    <div class="reusable-table-container">
        <table class="reusable-table" id="customer-no-map-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-md">Name</th>
                    <th class="col-md">Email</th>
                    <th class="col-md">Mobile</th>
                    <th class="col-md">Address 1</th>
                    <th class="col-md">Address 2</th>
                    <th class="col-xl">City</th>
                    <th class="col-xl">State</th>
                    <th class="col-xl">Country</th>
                    <th class="col-md">Created By</th>
                    <th class="col-md">Assigned To</th>
                </tr>
            </thead>
            <tbody id="customer-no-map-table-body">
                <!-- Data loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="text-right"><strong>Total Customer No Map:</strong></td>
                    <td class="number" id="total-customers">
                        <strong>0</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        const tablename = "#customer-no-map-table";
        const reportname = "Customer No Map Report";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize date filter
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadCustomerData();
                }
            });

            // Function to load customer data via AJAX
            function loadCustomerData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_id: $('#location_id').val(),
                    created_by_filter: $('#created_by_filter').val(),
                    assigned_to_filter: $('#assigned_to_filter').val()
                };

                $.ajax({
                    url: '{{ route('sr_account_receivable_unpaid') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#customer-no-map-table-body').html(
                            '<tr><td colspan="11" class="text-center">Loading data...</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#customer-no-map-table-body');
                        tbody.empty();

                        if (response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                const tr = $('<tr>').append(
                                    $('<td>').text(index + 1),
                                    $('<td>').text(row.name),
                                    $('<td>').text(row.email),
                                    $('<td>').text(row.mobile),
                                    $('<td>').text(row.address_line_1),
                                    $('<td>').text(row.address_line_2 || 'N/A'),
                                    $('<td>').text(row.city),
                                    $('<td>').text(row.state),
                                    $('<td>').text(row.country),
                                    $('<td>').text(row.created_by),
                                    $('<td>').text(row.assigned_to)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 11)
                                    .addClass('text-center')
                                    .text('No customers found for the selected filters.')
                                )
                            );
                        }

                        $('#total-customers strong').text(response.total);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#customer-no-map-table-body').html(
                            '<tr><td colspan="11" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadCustomerData();

            // Event listeners for filter changes
            $('#date_filter, #start_date, #end_date, #location_id, #created_by_filter, #assigned_to_filter').on(
                'change',
                function() {
                    loadCustomerData();
                });
        });
    </script>
@endsection
