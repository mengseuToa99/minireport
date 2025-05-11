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
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'Customer Report – Loan',
    ])

    <div class="reusable-table-container">
        <table class="reusable-table" id="customer-no-map-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-md">Name</th>
                    <th class="col-md">Email</th>
                    <th class="col-md">Mobile</th>
                    <th class="col-md">Final Total</th> <!-- Fixed label -->
                    <th class="col-md">Pay Term Number</th>
                </tr>
            </thead>
            <tbody id="customer-no-map-table-body">
                <!-- Data loaded via AJAX -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Customers:</strong></td>
                    <td class="number" id="total-customers"><strong>0</strong></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Loan:</strong></td>
                    <td class="number" id="total-loan"><strong>0</strong></td>
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
            // Function to load customer data via AJAX

            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
            });


            function loadCustomerData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    created_by_filter: $('#created_by_filter').val(),
                    assigned_to_filter: $('#assigned_to_filter').val(),
                    customer_group_filter: $('#customer_group_filter').val()
                };

                $.ajax({
                    url: '{{ route('sr_customer_loan') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData, // Ensure formData is defined elsewhere
                    beforeSend: function() {
                        $('#customer-no-map-table-body').html(
                            '<tr><td colspan="6" class="text-center">Loading data...</td></tr>'
                        );
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
                                    $('<td>').text(row.final_total), 
                                    $('<td>').text(row.pay_term_number)
                                );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append(
                                $('<tr>').append(
                                    $('<td>')
                                    .attr('colspan', 6)
                                    .addClass('text-center')
                                    .text('No customers found for the selected filters.')
                                )
                            );
                        }

                        $('#total-customers strong').text(response.total);
                        $('#total-loan strong').text(response.total_loan);

                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#customer-no-map-table-body').html(
                            '<tr><td colspan="6" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadCustomerData();

            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadCustomerData(); // Reload data for non-custom range selections
                }
            });

            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadCustomerData(); // Reload data for custom range
                } else {
                    alert('Please select both start and end dates.');
                }
            });


            // Event listeners for filter changes
            $('#created_by_filter, #assigned_to_filter, #customer_group_filter').on('change', function() {
                loadCustomerData();
            });
        });
    </script>
@endsection
