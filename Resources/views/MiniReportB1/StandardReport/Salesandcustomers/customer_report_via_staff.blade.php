@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.customer_report_via_staff'))

@include('minireportb1::MiniReportB1.components.linkforinclude')


@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    {{-- Add clearer meta tags for business information --}}
    @php
        // Get business info directly from the database for reliability
        $business_id = session('user.business_id');
        $business = \App\Business::find($business_id);
        $businessName = $business->name ?? $business_name ?? '';
        $directLogoPath = $business && $business->logo ? '/uploads/business_logos/' . $business->logo : '';
    @endphp
    
    <meta name="business-name" content="{{ $businessName }}">
    <meta name="business-logo" content="{{ asset($directLogoPath) }}">
    
    {{-- Add a dedicated logo-test div for the print function to find --}}
    <div class="logo-test" style="display: none;">
        <img src="{{ asset($directLogoPath) }}" alt="{{ $businessName }} Logo">
    </div>

    <div style="margin: 16px" class="no-print">

        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">

                <form id="filterForm">
                    <div class="form-group mt-3">
                        {!! Form::label('user_id', __('User') . ':') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent

    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => __('minireportb1::minireportb1.customer_report_via_staff'),
        'business_name' => $businessName,
        'business_logo' => $directLogoPath
    ])

    <div class="reusable-table-container">
        <!-- Row limit controls -->
        @include('minireportb1::MiniReportB1.components.pagination')
        
        <table class="reusable-table wide-table sticky-first-col" id="staff-customer-table">
            <thead>
                <tr>
                    <th class="col-xs">{{ __('minireportb1::minireportb1.no') }}</th>
                    <th class="col-md">{{ __('minireportb1::minireportb1.employee_name') }}</th>
                    <th class="col-sm">{{ __('minireportb1::minireportb1.customer_count') }}</th>
                </tr>
            </thead>
            <tbody id="staff-customer-table-body">
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        const tablename = "#staff-customer-table";
        const reportname = "{{ __('minireportb1::minireportb1.customer_report_via_staff') }}";
    </script>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
    <!-- JavaScript for AJAX and Filters -->
    <script>
        $(document).ready(function() {
            // Pagination variables
            let currentPage = 1;
            let totalPages = 1;
            let rowLimit = 10;

            // Function to load data via AJAX
            function loadStaffCustomerData() {
                const formData = {
                    user_id: $('#user_id').val(),
                    page: currentPage,
                    limit: rowLimit
                };

                $.ajax({
                    url: '{{ action('\Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController@customerReportViaStaff') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        // Loading indicator
                        $('#staff-customer-table-body').html(
                            '<tr><td colspan="3" class="text-center">{{ __("minireportb1::minireportb1.loading") }}</td></tr>');
                    },
                    success: function(response) {
                        const tbody = $('#staff-customer-table-body');
                        tbody.empty();

                        // Update pagination variables
                        totalPages = response.total_pages || 1;
                        $('#total-pages').text(totalPages);
                        $('#current-page').text(currentPage);
                        
                        // Update pagination buttons
                        $('#prev-page').prop('disabled', currentPage <= 1);
                        $('#next-page').prop('disabled', currentPage >= totalPages);

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, row) {
                                // Calculate the global row index across all pages
                                const rowIndex = (currentPage - 1) * rowLimit + index + 1;
                                
                                // Create a link to the user detail page
                                const userUrl = '{{ url("/users") }}/' + row.id;
                                
                                const tr = $('<tr>')
                                    .attr('data-id', row.id)
                                    .append(
                                        $('<td>').text(rowIndex),
                                        $('<td>').html(function() {
                                            return `<a href="${userUrl}" target="_blank" class="user-link">${row.employee_name}</a>`;
                                        }),
                                        $('<td>').text(row.contact_count)
                                    );
                                tbody.append(tr);
                            });
                        } else {
                            tbody.html(
                                '<tr><td colspan="3" class="text-center">{{ __("minireportb1::minireportb1.no_data_available") }}</td></tr>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#staff-customer-table-body').html(
                            '<tr><td colspan="3" class="text-center text-danger">{{ __("minireportb1::minireportb1.error_loading_data") }}</td></tr>'
                        );
                    }
                });
            }

            // Initial load
            loadStaffCustomerData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadStaffCustomerData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadStaffCustomerData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadStaffCustomerData();
                }
            });

            // Event listeners for user filter changes
            $('#user_id').on('change', function() {
                currentPage = 1; // Reset to first page when changing filters
                loadStaffCustomerData();
            });

            // Handle user link clicks
            $(document).on('click', '.user-link', function(e) {
                // Prevent the row selection
                e.stopPropagation();
            });
        });
    </script>

@endsection 