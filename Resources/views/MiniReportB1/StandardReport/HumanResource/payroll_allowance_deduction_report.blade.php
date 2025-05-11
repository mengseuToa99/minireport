@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.payroll_allowance_deduction_report'))
@include('minireportb1::MiniReportB1.components.linkforinclude')

<style>
    /* Pagination Styles */
    .row-limit-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0;
        padding: 10px;
        background-color: #f9f9f9;
        border-radius: 4px;
    }
    
    .row-limit-select {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-left: 10px;
    }
    
    .pagination-controls {
        display: flex;
        align-items: center;
    }
    
    .pagination-btn {
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin: 0 5px;
    }
    
    .pagination-btn:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
    
    .pagination-info {
        margin: 0 10px;
    }
</style>

@section('content')

@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

<section class="content" style="background-color: #f7f8fa">
    <div class="filters-container no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <div class="filter-section">
                    @include("minireportb1::MiniReportB1.components.filterdate")
                </div>
                
                <div class="filter-section">
                    <div class="form-group">
                        <label for="username_filter">@lang('minireportb1::minireportb1.employee')</label>
                        <select class="form-control" id="username_filter" name="username_filter">
                            <option value="">@lang('minireportb1::minireportb1.all_employees')</option>
                            @if(isset($users))
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ request()->get('username_filter') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                
                <div class="filter-section">
                    <div class="form-group">
                        <label>@lang('minireportb1::minireportb1.include')</label>
                        <div class="checkbox-container">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="show_allowances" id="show_allowances" value="1" 
                                        {{ request()->get('show_allowances', '1') == '1' ? 'checked' : '' }}>
                                    @lang('minireportb1::minireportb1.allowances')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="show_deductions" id="show_deductions" value="1" 
                                        {{ request()->get('show_deductions', '1') == '1' ? 'checked' : '' }}>
                                    @lang('minireportb1::minireportb1.deductions')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="filter-section" style="align-self: flex-end;">
                    @include("minireportb1::MiniReportB1.components.printbutton")
                </div>
            </div>
        @endcomponent
    </div>
    <br><br>
    
    <div class="col-md-12" style="background-color: #ffffff;">
        <div class="box" style="background-color: #ffffff;">
            <div class="box-header with-border text-center">
                <h4 class="box-title report-title">{{ Session::get('business.name') }}</h4>
                <br><br>
                <h5 class="box-title report-subtitle">
                    <b>@lang('minireportb1::minireportb1.payroll_allowance_deduction_report')</b>
                </h5>
                <p class="report-date">{{ @format_date($start_date) }} - {{ @format_date($end_date) }}</p>
            </div>

            <div class="reusable-table" id="report-data">
                <div class="text-center">
                    <i class="fa fa-spin fa-spinner fa-2x"></i>
                    <p>@lang('minireportb1::minireportb1.loading')</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initial load
        loadReportData();
        
        // Handle date filter changes
        $('#date_filter').on('change', function() {
            if ($(this).val() === 'custom_month_range') {
                $('#custom_range_inputs').show();
            } else {
                $('#custom_range_inputs').hide();
                loadReportData();
            }
        });
        
        // Apply custom date range
        $('#apply_custom_range').on('click', function() {
            loadReportData();
        });
        
        // Apply other filters
        $('#username_filter, #show_allowances, #show_deductions').on('change', function() {
            loadReportData();
        });
        
        // Function to load report data via AJAX
        function loadReportData() {
            var dateFilter = $('#date_filter').val();
            var startDate = '';
            var endDate = '';
            
            if (dateFilter === 'custom_month_range') {
                startDate = $('#start_date').val();
                endDate = $('#end_date').val();
            }
            
            $('#report-data').html('<div class="text-center"><i class="fa fa-spin fa-spinner fa-2x"></i><p>@lang("minireportb1::minireportb1.loading")</p></div>');
            
            $.ajax({
                url: "{{ route('minireportb1.standardReport.humanResource.payroll_allowance_deduction_report') }}",
                type: 'GET',
                dataType: 'html',
                data: {
                    date_filter: dateFilter,
                    start_date: startDate,
                    end_date: endDate,
                    username_filter: $('#username_filter').val(),
                    show_allowances: $('#show_allowances').is(':checked') ? '1' : '0',
                    show_deductions: $('#show_deductions').is(':checked') ? '1' : '0',
                    load_data: 1
                },
                success: function(data) {
                    $('#report-data').html(data);
                    initializePagination();
                    
                },
                error: function() {
                    $('#report-data').html('<div class="alert alert-danger">@lang("minireportb1::minireportb1.error_loading_data")</div>');
                }
            });
        }
        
      
        // Initialize pagination after table is loaded
        function initializePagination() {
            let currentPage = 1;
            let rowsPerPage = parseInt($('#row-limit').val() || 10);
            let table = document.querySelector('#table-container table');
            
            if (!table) return;
            
            let tbody = table.querySelector('tbody');
            let rows = tbody.querySelectorAll('tr');
            let totalRows = rows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage);
            
            // Update pagination info
            $('#current-page').text(currentPage);
            $('#total-pages').text(totalPages);
            
            // Show rows for current page only
            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                
                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
                
                // Update button states
                $('#prev-page').prop('disabled', page === 1);
                $('#next-page').prop('disabled', page === totalPages || totalPages === 0);
                $('#current-page').text(page);
            }
            
            // Initialize first page
            showPage(currentPage);
            
            // Handle row limit change
            $('#row-limit').on('change', function() {
                rowsPerPage = parseInt($(this).val());
                totalPages = Math.ceil(totalRows / rowsPerPage);
                currentPage = 1;
                $('#total-pages').text(totalPages);
                showPage(currentPage);
            });
            
            // Handle pagination buttons
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });
            
            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });
            
            // Show all rows if "All" is selected
            if (rowsPerPage >= 10000) {
                rows.forEach(row => {
                    row.style.display = '';
                });
                
                $('#prev-page').prop('disabled', true);
                $('#next-page').prop('disabled', true);
            }
        }
        
 
    });
</script>
@endsection 