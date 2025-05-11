@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.monthly_tax_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')



@section('content')
    <div style="margin: 16px" class="no-print">
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")


        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <form id="filterForm">
                    @include('minireportb1::MiniReportB1.components.filterdate')
                    
                    <div class="report-filter">
                        <p>@lang('business.location')</p>
                        <div class="filter-group">
                            <select class="filter-select" name="location_filter" id="location_filter">
                                <option value="">@lang('messages.all')</option>
                                @foreach($locations as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent

    </div>

    <div id="report-header-container">
        @include('minireportb1::MiniReportB1.components.reportheader', [
            'business_name' => $business->name ?? 'Company Name',
            'report_name' => __('minireportb1::minireportb1.monthly_tax_report') . ' ' . $formatted_month_year
        ])
    </div>

    <div class="reusable-table-container">
        <div id="no-data-message" class="alert alert-warning text-center khmer-text" style="display: none;">
            <i class="fa fa-exclamation-triangle"></i> @lang('minireportb1::minireportb1.no_data_available')
        </div>
        
        <table class="reusable-table" id="report-table">
            <thead>
                <tr class="khmer-text">
                    <th class="col-xs">@lang('minireportb1::minireportb1.no')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.tax_id')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.id_proof')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.employee_name')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.nationality')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.employee_type')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.position')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.spouse')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.dependent_children')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.gross_salary')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.benefits')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.salary_tax')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.benefits_tax')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.non_taxable_salary')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.tax_status')</th>
                </tr>
            </thead>
            <tbody id="report-table-body">
                <!-- Data will be loaded via AJAX -->
                <tr>
                    <td colspan="15" class="text-center">@lang('minireportb1::minireportb1.loading')</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row khmer-text">
                    <td colspan="7" class="text-right"><strong>@lang('minireportb1::minireportb1.total')</strong></td>
                    <td class="center-text" id="total-spouse"><strong>0</strong></td>
                    <td class="center-text" id="total-children"><strong>0</strong></td>
                    <td class="number" id="total-salary"><strong>@lang('minireportb1::minireportb1.total_salary_khr')</strong></td>
                    <td class="number" id="total-benefits"><strong>@lang('minireportb1::minireportb1.total_benefits')</strong></td>
                    <td class="number" id="total-salary-tax"><strong>@lang('minireportb1::minireportb1.total_salary_tax')</strong></td>
                    <td class="number" id="total-benefits-tax"><strong>@lang('minireportb1::minireportb1.total_benefits_tax')</strong></td>
                    <td class="number" id="total-non-taxable"><strong>@lang('minireportb1::minireportb1.total_non_taxable')</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

    <script>
        const tablename = "#report-table";
        const reportname = "@lang('minireportb1::minireportb1.monthly_tax_report')";
        
        $(document).ready(function() {
            // Initialize jQuery UI Datepicker
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
            });

            // Initialize location filter if present
            if ($('#location_filter').length) {
                $('#location_filter').on('change', function() {
                    loadReportData();
                });
            }

            // Initial load
            loadReportData();
            
            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadReportData(); // Reload data for non-custom range selections
                }
            });

            // Apply custom range button
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadReportData(); // Reload data for custom range
                } else {
                    alert('@lang('minireportb1::minireportb1.please_select_dates')');
                }
            });
            
            // Function to load data via AJAX
            function loadReportData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_filter: $('#location_filter').val()
                };
                
                $.ajax({
                    url: '{{ route("minireportb1.standardReport.humanResource.monthly_tax_report") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#report-table-body').html(
                            '<tr><td colspan="15" class="text-center">@lang('minireportb1::minireportb1.loading')</td></tr>'
                        );
                    },
                    success: function(response) {
                        const tbody = $('#report-table-body');
                        tbody.empty();

                        // Check if we have salary data
                        if (!response.has_salary_data) {
                            $('#no-data-message').show();
                        } else {
                            $('#no-data-message').hide();
                        }

                        // Update the report header with the new month/year from the response
                        if (response.report_month) {
                            // Just update the report name text rather than rebuilding the entire header
                            const reportTitle = '@lang('minireportb1::minireportb1.monthly_tax_report') ' + response.report_month;
                            $('#report-header h2:last-child').text(reportTitle);
                        }

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                const tr = $('<tr>').append(
                                    $('<td>').text(row.id),
                                    $('<td>').text(row.tax_id),
                                    $('<td>').text(row.id_proof),
                                    $('<td>').text(row.employee_name),
                                    $('<td>').text(row.nationality),
                                    $('<td>').text(row.employee_type),
                                    $('<td>').text(row.position),
                                    $('<td class="center-text">').text(row.spouse),
                                    $('<td class="center-text">').text(row.children),
                                    $('<td class="number">').text(row.gross_salary + ' ៛'),
                                    $('<td class="number">').text(row.benefits),
                                    $('<td class="number">').text(row.salary_tax + ' ៛'),
                                    $('<td class="number">').text(row.benefits_tax),
                                    $('<td class="number">').text(row.non_taxable_salary),
                                    $('<td>').text(row.tax_status)
                                );
                                tbody.append(tr);
                            });

                            // Update totals
                            if (response.totals) {
                                $('#total-spouse strong').text(response.totals.spouse);
                                $('#total-children strong').text(response.totals.children);
                                $('#total-salary strong').text(response.totals.gross_salary);
                                $('#total-benefits strong').text(response.totals.benefits);
                                $('#total-salary-tax strong').text(response.totals.salary_tax);
                                $('#total-benefits-tax strong').text(response.totals.benefits_tax);
                                $('#total-non-taxable strong').text(response.totals.non_taxable_salary);
                            }
                        } else {
                            tbody.append(
                                '<tr><td colspan="15" class="text-center">@lang('minireportb1::minireportb1.no_data_available')</td></tr>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#report-table-body').html(
                            '<tr><td colspan="15" class="text-center text-danger">@lang('minireportb1::minireportb1.error_loading_data')</td></tr>'
                        );
                    }
                });
            }
        });
    </script>
@endsection 
