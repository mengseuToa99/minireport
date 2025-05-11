@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.monthly_nssf_tax_report'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <style>
        .report-header h2 {
            margin-bottom: 0;
        }
        .khmer-text {
            font-family: 'Khmer OS', 'Khmer OS System', sans-serif;
        }
        .center-text {
            text-align: center;
        }
        .number {
            text-align: right;
        }
        th.vertical-header {
            white-space: nowrap;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            vertical-align: middle;
            height: 150px;
            padding: 8px 0;
        }
    </style>
@endsection

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <div style="margin: 16px" class="no-print">

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

    @include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => __('minireportb1::minireportb1.monthly_nssf_tax_report') . ' ' . $formatted_month_year
    ])

       <div class="exchange-rate-info mt-3 text-right" style="margin-right: 16px;">
            <span class="khmer-text font-weight-bold">@lang('minireportb1::minireportb1.exchange_rate'):</span> <span id="exchange-rate">4059 ៛</span>
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
                    <th class="col-md">@lang('minireportb1::minireportb1.employee_name')</th>
                    <th class="col-md">@lang('minireportb1::minireportb1.position')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.salary_usd')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.gross_salary')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.taxable_salary')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.salary_khr')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.pension_2percent_employee')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.net_salary_khr')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.spouse')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.dependent_children')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.taxable_salary')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.rate')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.salary_tax')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.salary')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.contribution_nssf')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.contribution_health')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.pension_2percent_employee')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.pension_2percent_company')</th>
                    <th class="col-sm">@lang('minireportb1::minireportb1.contribution_amount')</th>
                </tr>
            </thead>
            <tbody id="report-table-body">
                <!-- Data will be loaded via AJAX -->
                <tr>
                    <td colspan="21" class="text-center">@lang('minireportb1::minireportb1.loading')</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row khmer-text">
                    <td colspan="4" class="text-right"><strong>@lang('minireportb1::minireportb1.total')</strong></td>
                    <td class="number" id="total-salary-usd"><strong>@lang('minireportb1::minireportb1.total_salary_usd')</strong></td>
                    <td class="number" id="total-gross-salary-khr"><strong>@lang('minireportb1::minireportb1.total_gross_salary_khr')</strong></td>
                    <td class="number" id="total-tax-usd"><strong>@lang('minireportb1::minireportb1.total_tax_usd')</strong></td>
                    <td class="number" id="total-gross-salary-khr-2"><strong>@lang('minireportb1::minireportb1.total_gross_salary_khr')</strong></td>
                    <td class="number" id="total-pension-employee"><strong>@lang('minireportb1::minireportb1.total_pension_employee')</strong></td>
                    <td class="number" id="total-net-salary"><strong>@lang('minireportb1::minireportb1.total_net_salary')</strong></td>
                    <td class="center-text" id="total-spouse"><strong>0</strong></td>
                    <td class="center-text" id="total-children"><strong>0</strong></td>
                    <td class="number" id="total-taxable-salary"><strong>@lang('minireportb1::minireportb1.total_taxable_salary')</strong></td>
                    <td class="center-text" id="total-tax-rate"><strong></strong></td>
                    <td class="number" id="total-salary-tax"><strong>@lang('minireportb1::minireportb1.total_salary_tax')</strong></td>
                    <td class="number" id="total-salary-khr"><strong>@lang('minireportb1::minireportb1.total_salary_khr')</strong></td>
                    <td class="number" id="total-nssf"><strong>@lang('minireportb1::minireportb1.total_nssf')</strong></td>
                    <td class="number" id="total-health"><strong>@lang('minireportb1::minireportb1.total_health')</strong></td>
                    <td class="number" id="total-pension-employee-2"><strong>@lang('minireportb1::minireportb1.total_pension_employee')</strong></td>
                    <td class="number" id="total-pension-employer"><strong>@lang('minireportb1::minireportb1.total_pension_employer')</strong></td>
                    <td class="number" id="total-contributions"><strong>@lang('minireportb1::minireportb1.total_contributions')</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>

    <script>
        const tablename = "#report-table";
        const reportname = "@lang('minireportb1::minireportb1.monthly_nssf_tax_report')";
        
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
                    url: '{{ route("minireportb1.standardReport.humanResource.monthly_nssf_tax_report") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#report-table-body').html(
                            '<tr><td colspan="21" class="text-center">@lang('minireportb1::minireportb1.loading')</td></tr>'
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

                        // Update exchange rate display
                        if (response.exchange_rate) {
                            $('#exchange-rate').text(response.exchange_rate + ' ៛');
                        }

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                const tr = $('<tr>').append(
                                    $('<td>').text(row.id),
                                    $('<td>').text(row.tax_id),
                                    $('<td>').text(row.employee_name),
                                    $('<td>').text(row.position),
                                    $('<td class="number">').text(row.salary_usd),
                                    $('<td class="number">').text(row.gross_salary_khr),
                                    $('<td class="number">').text(row.taxable_salary_usd),
                                    $('<td class="number">').text(row.gross_salary_khr_display),
                                    $('<td class="number">').text(row.pension_employee),
                                    $('<td class="number">').text(row.net_salary),
                                    $('<td class="center-text">').text(row.spouse),
                                    $('<td class="center-text">').text(row.children),
                                    $('<td class="number">').text(row.taxable_salary),
                                    $('<td class="center-text">').text(row.tax_rate),
                                    $('<td class="number">').text(row.salary_tax),
                                    $('<td class="number">').text(row.salary_khr),
                                    $('<td class="number">').text(row.nssf_contribution),
                                    $('<td class="number">').text(row.health_contribution),
                                    $('<td class="number">').text(row.pension_employee_display),
                                    $('<td class="number">').text(row.pension_employer),
                                    $('<td class="number">').text(row.total_contributions)
                                );
                                tbody.append(tr);
                            });

                            // Update totals
                            if (response.totals) {
                                $('#total-salary-usd strong').text(response.totals.salary_usd);
                                $('#total-gross-salary-khr strong').text(response.totals.gross_salary_khr);
                                $('#total-tax-usd strong').text(response.totals.taxable_salary_usd);
                                $('#total-gross-salary-khr-2 strong').text(response.totals.salary_khr);
                                $('#total-pension-employee strong').text(response.totals.pension_employee);
                                $('#total-net-salary strong').text(response.totals.net_salary);
                                $('#total-spouse strong').text(response.totals.spouse);
                                $('#total-children strong').text(response.totals.children);
                                $('#total-taxable-salary strong').text(response.totals.taxable_salary);
                                $('#total-salary-tax strong').text(response.totals.salary_tax);
                                $('#total-salary-khr strong').text(response.totals.salary_khr);
                                $('#total-nssf strong').text(response.totals.nssf_contribution);
                                $('#total-health strong').text(response.totals.health_contribution);
                                $('#total-pension-employee-2 strong').text(response.totals.pension_employee);
                                $('#total-pension-employer strong').text(response.totals.pension_employer);
                                $('#total-contributions strong').text(response.totals.total_contributions);
                            }
                        } else {
                            tbody.append(
                                '<tr><td colspan="21" class="text-center">@lang('minireportb1::minireportb1.no_data_available')</td></tr>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#report-table-body').html(
                            '<tr><td colspan="21" class="text-center text-danger">@lang('minireportb1::minireportb1.error_loading_data')</td></tr>'
                        );
                    }
                });
            }
        });
    </script>
@endsection 
