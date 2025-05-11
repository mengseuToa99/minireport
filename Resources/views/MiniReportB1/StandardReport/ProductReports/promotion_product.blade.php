@extends('minireportb1::layouts.master2')

@section('title', 'Promotion Products')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        /* General styles */
        body,
        .container,
        .content-section,
        .filter-form,
        .table-responsive,
        .report-header,
        .product-table,
        .product-table th,
        .product-table td {
            background-color: white !important;
        }

        .form-check-input {
            width: 20px !important;
            height: 20px !important;
            margin-top: 0.3rem;
        }

        .report-header {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .form-check-label {
            font-size: 16px;
            margin-left: 0.5rem;
            cursor: pointer;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .form-check-input:checked + .form-check-label {
            background-color: #e6f7ff;
            border-left: 3px solid #1890ff;
        }

        /* Table styles */
        .product-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 14px;
        }

        .product-table th,
        .product-table td {
            word-break: break-word;
            white-space: normal;
            padding: 8px;
            border: 1px solid #000;
        }
        
        /* Style for price group columns */
        .product-table th[class*="price-column-"] {
            background-color: #f8f9fa;
            border-bottom: 2px solid #f26522;
        }

        .col-no {
            width: 5% !important;
        }

        .col-image {
            width: 10% !important;
        }

        .col-product {
            width: 25% !important;
        }

        .col-price {
            width: 15% !important;
        }

        .col-end-date {
            width: 15% !important;
        }

        .table-responsive {
            overflow-x: auto;
            margin: 10px 0;
        }

        .number {
            text-align: center;
            font-family: monospace;
        }

        .icon-spacing {
            margin-right: 8px;
        }

        /* Pagination styles */
        .row-limit-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .row-limit-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            margin-left: 10px;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
        }

        .pagination-btn {
            padding: 5px 15px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .pagination-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .pagination-info {
            margin: 0 10px;
        }



        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .col-no {
                width: 7% !important;
            }

            @page {
                size: auto;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100vw;
                height: 100vh;
                background: white !important;
            }

            .container,
            .content-section,
            .tab-content {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .product-table {
                font-size: 10pt !important;
                width: 100% !important;
                border-collapse: collapse;
                page-break-inside: avoid;
            }

            .product-table th,
            .product-table td {
                padding: 8px !important;
                border: 1px solid #000 !important;
                background: white !important;
                -webkit-print-color-adjust: exact;
            }

            .report-header {
                text-align: center;
                margin-bottom: 0px;
                page-break-after: avoid;
            }

            /* Hide non-essential elements */
            .filter-form,
            #printButton,
            .no-print,
            .nav-tabs,
            .box-tools,
            a[href="/discount"] {
                display: none !important;
            }

            /* Force full width for all elements */
            * {
                box-sizing: border-box;
                float: none !important;
                position: static !important;
            }

            /* Remove shadows and backgrounds */
            .box,
            .nav-tabs-custom {
                box-shadow: none !important;
                background: transparent !important;
                border: 0 !important;
            }

            /* Ensure images fit within the table */
            .col-image img {
                max-width: 100% !important;
                height: auto !important;
            }
        }

        .container {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="content-section">
            <!-- Header Section -->
            @include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

            <div style="margin: 16px" class="no-print">
                @component('components.filters', ['title' => __('report.filters')])
                <div class="filter-container">
                    <form id="filterForm">
                        @include('minireportb1::MiniReportB1.components.filterdate')
                        
                        <div class="row">
                            <!-- Location Filter -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Location')</label>
                                    <select id="location_id" class="form-control">
                                        <option value="">All Locations</option>
                                        @foreach ($locations as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ request()->location_id == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    @include('minireportb1::MiniReportB1.components.printbutton')
                </div>
                @endcomponent

                
            </div>


            

            <!-- Add Discount Button -->
             <div  style ="display: flex; ">

                 <div class="no-print" style="margin: 16px">
                     <a href="/discount">
                         <button class="btn btn-primary w-100">
                             <i class="fa fa-plus icon-spacing"></i> @lang('Add Discount')
                         </button>
                     </a>
                 </div>
     
                 <!-- Toggle Group Price Columns Button -->
                 <div class="no-print" style="margin: 16px">
                     <button id="toggleGroupPriceColumns" class="btn btn-warning w-100" data-toggle="modal"
                         data-target="#groupPriceColumnsModal">
                         <i class="fas fa-eye"></i> Toggle Group Price Columns
                     </button>
                 </div>
             </div>

            <!-- Modal for Toggling Group Price Columns -->
            <div class="modal fade" id="groupPriceColumnsModal" tabindex="-1" role="dialog"
                aria-labelledby="groupPriceColumnsModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="groupPriceColumnsModalLabel">Toggle Group Price Columns</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="toggleColumnsForm">
                                @foreach ($group_prices as $group_price)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $group_price->id }}"
                                            id="groupPrice{{ $group_price->id }}" checked>
                                        <label class="form-check-label" for="groupPrice{{ $group_price->id }}">
                                            {{ $group_price->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                        <div class="modal-footer " style ="display: flex; ">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="applyColumnToggle">Apply</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Header and Table -->
            @component('components.widget', ['class' => 'box-solid'])
                <div class="report-header">
                    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
                        @if (request()->date_filter)
                            @php
                                $dateFilterText = "";
                                switch (request()->date_filter) {
                                    case 'today':
                                        $dateFilterText = 'Today';
                                        break;
                                    case 'yesterday':
                                        $dateFilterText = 'Yesterday';
                                        break;
                                    case 'this_week':
                                        $dateFilterText = 'This Week';
                                        break;
                                    case 'last_week':
                                        $dateFilterText = 'Last Week';
                                        break;
                                    case 'this_month':
                                        $dateFilterText = 'This Month';
                                        break;
                                    case 'last_month':
                                        $dateFilterText = 'Last Month';
                                        break;
                                    case 'this_quarter':
                                        $dateFilterText = 'This Quarter';
                                        break;
                                    case 'last_quarter':
                                        $dateFilterText = 'Last Quarter';
                                        break;
                                    case 'this_year':
                                        $dateFilterText = 'This Year';
                                        break;
                                    case 'last_year':
                                        $dateFilterText = 'Last Year';
                                        break;
                                    case 'custom_month_range':
                                        $dateFilterText = 'Custom Range';
                                        break;
                                    default:
                                        $dateFilterText = request()->date_filter;
                                }
                            @endphp
                            Promotion Products Report - {{ $dateFilterText }}
                            @if (request()->start_date && request()->end_date)
                                <br />
                                <small class="text-muted">Date: {{ request()->start_date }} to {{ request()->end_date }}</small>
                            @endif
                        @else
                            Promotion Products Report
                        @endif
                    </h1>

                    <!-- Display Location Name -->
                    <div class="location-name">
                        <p style="font-size: 12px;" id="selected-location">All Locations</p>
                    </div>
                </div>

                <!-- Pagination Controls -->
                <div class="row-limit-controls no-print" style="width: fit-content; justify-self: end;">
                    <label for="row-limit">ចំនួនជួរដែលត្រូវបង្ហាញ:</label>
                    <select id="row-limit" class="row-limit-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="10000">All - ទាំងអស់</option>
                    </select>
                    
                    <div class="pagination-controls">
                        <button id="prev-page" class="pagination-btn" disabled>&laquo; មុន</button>
                        <span class="pagination-info">ទំព័រ <span id="current-page">1</span> នៃ <span id="total-pages">1</span></span>
                        <button id="next-page" class="pagination-btn">បន្ទាប់ &raquo;</button>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="product-table">
                        <thead class="thead-dark">
                            <tr>
                                <th class="col-no">#</th>
                                <th class="col-image">រូបភាព</th>
                                <th class="col-product">ឈ្មោះផលិតផល</th>
                                {{-- <th class="col-price">Price Before</th> --}}
                                <th class="col-price">ចំនួនទឹកប្រាក់បញ្ចុះតម្លៃ</th>
                                {{-- <th class="col-price">Price After</th> --}}
                                <th class="col-end-date">កាលបរិច្ឆេទបញ្ចប់</th>
                                @foreach ($group_prices as $group_price)
                                    <th class="col-price price-column-{{ $group_price->id }}">{{ $group_price->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="promotion-table-body">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // Pagination variables
            let currentPage = 1;
            let totalPages = 1;
            let rowLimit = 10;
            let allData = []; // Store all data for client-side pagination

            // Initialize date picker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            // Handle the visibility of custom date range inputs
            function updateDateRangeVisibility() {
                const dateFilter = $('#date_filter');
                if (!dateFilter.length) return;

                const selectedValue = dateFilter.val();
                const customRangeInputs = $('#custom_range_inputs');
                
                if (selectedValue === 'custom_month_range') {
                    customRangeInputs.show();
                } else {
                    customRangeInputs.hide();
                    if (selectedValue) {
                        currentPage = 1;
                        loadPromotionData();
                    }
                }
            }

            // Initial date range visibility based on default selection
            updateDateRangeVisibility();

            // Event listener for date filter changes
            $('#date_filter').on('change', updateDateRangeVisibility);

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                
                if (startDate && endDate) {
                    currentPage = 1;
                    loadPromotionData();
                } else {
                    alert('Please select both start and end dates');
                }
            });

            // Date picker change events for automatic updates
            $('#start_date, #end_date').on('change', function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                
                if (startDate && endDate) {
                    currentPage = 1;
                    loadPromotionData();
                }
            });

            // Auto-apply location filter when changed
            $('#location_id').on('change', function() {
                currentPage = 1;
                loadPromotionData();
            });

            // Function to load promotion product data via AJAX
            function loadPromotionData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    location_id: $('#location_id').val()
                };

                if (formData.date_filter === 'custom_month_range') {
                    formData.start_date = $('#start_date').val();
                    formData.end_date = $('#end_date').val();
                }

                const locationSelect = $('#location_id');
                const selectedLocation = locationSelect.length ? locationSelect.find('option:selected').text() : 'All Locations';
                $('#selected-location').text('Location: ' + selectedLocation);

                const tableBody = $('#promotion-table-body');
                if (!tableBody.length) return;

                tableBody.html('<tr><td colspan="' + (5 + {{ count($group_prices) }}) + '" class="text-center">Loading data...</td></tr>');

                $.ajax({
                    url: '{{ route("sr_promotion_product") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        if (response && response.formatted_discounts && Array.isArray(response.formatted_discounts) && response.formatted_discounts.length > 0) {
                            processData(response.formatted_discounts);
                        } else {
                            tableBody.html('<tr><td colspan="' + (5 + {{ count($group_prices) }}) + '" class="text-center">No products found with the selected filters.</td></tr>');
                            $('#total-pages').text('1');
                            $('#current-page').text('1');
                            $('#prev-page').prop('disabled', true);
                            $('#next-page').prop('disabled', true);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        tableBody.html('<tr><td colspan="' + (5 + {{ count($group_prices) }}) + '" class="text-center text-danger">Error loading data. Please try again.</td></tr>');
                    }
                });
            }

            // Process the data for pagination
            function processData(formatted_discounts) {
                if (!Array.isArray(formatted_discounts)) {
                    console.error('Invalid data format received');
                    return;
                }

                allData = [];
                let rowNumber = 1;
                
                formatted_discounts.forEach(function(promotion) {
                    if (promotion && Array.isArray(promotion.products)) {
                        promotion.products.forEach(function(product) {
                            if (product) {
                                allData.push({
                                    rowNumber: rowNumber++,
                                    endDate: promotion.end_date || '',
                                    product: product
                                });
                            }
                        });
                    }
                });

                updatePagination();
                displayCurrentPage();
            }

            // Function to update pagination controls
            function updatePagination() {
                totalPages = Math.ceil(allData.length / rowLimit) || 1;
                $('#total-pages').text(totalPages);
                $('#current-page').text(currentPage);
                
                // Update pagination buttons
                $('#prev-page').prop('disabled', currentPage <= 1);
                $('#next-page').prop('disabled', currentPage >= totalPages);
            }

            // Function to display current page of data
            function displayCurrentPage() {
                const tbody = $('#promotion-table-body');
                tbody.empty();
                
                if (allData.length > 0) {
                    // Calculate start and end indices for the current page
                    const startIndex = (currentPage - 1) * rowLimit;
                    const endIndex = Math.min(startIndex + rowLimit, allData.length);
                    
                    // Display data for current page
                    for (let i = startIndex; i < endIndex; i++) {
                        const item = allData[i];
                        const product = item.product;
                        
                        const tr = $('<tr>');
                        
                        // Add cells
                        tr.append($('<td>').addClass('col-no').text(item.rowNumber));
                        
                        // Image cell
                        const imageCell = $('<td>').addClass('col-image');
                        if (product.product_image) {
                            imageCell.append(
                                $('<img>')
                                    .attr('src', product.product_image)
                                    .attr('alt', product.product_name)
                                    .addClass('img-thumbnail')
                                    .css({
                                        'max-width': '60px',
                                        'height': '60px',
                                        'object-fit': 'contain'
                                    })
                            );
                        } else {
                            imageCell.append(
                                $('<div>').addClass('no-image-placeholder').append(
                                    $('<i>').addClass('fas fa-image fa-lg text-muted')
                                )
                            );
                        }
                        tr.append(imageCell);
                        
                        // Product name
                        tr.append($('<td>').addClass('col-product').text(product.product_name));
                        
                        // Discount amount
                        const discountCell = $('<td>').addClass('col-price number');
                        if (product.discount_amount && product.discount_amount.includes('%')) {
                            discountCell.text(product.discount_amount);
                        } else if (product.discount_amount) {
                            // For fixed price discounts, just display as is since it's already formatted in the controller
                            discountCell.text(product.discount_amount);
                        } else {
                            discountCell.text('1.1.ទូទៅ: 0.00');
                        }
                        tr.append(discountCell);
                        
                        // End date
                        tr.append($('<td>').addClass('col-end-date').text(item.endDate));
                        
                        // Group prices
                        @foreach ($group_prices as $group_price)
                            const priceCell{{ $group_price->id }} = $('<td>').addClass('col-price number price-column-{{ $group_price->id }}');
                            if (product.group_prices && product.group_prices[{{ $group_price->id }}]) {
                                if (typeof product.group_prices[{{ $group_price->id }}] === 'object') {
                                    priceCell{{ $group_price->id }}.append(
                                        $('<span>').addClass('discounted-price').text(product.group_prices[{{ $group_price->id }}].discounted)
                                    ).append(
                                        $('<span>').addClass('original-price').text('(' + product.group_prices[{{ $group_price->id }}].original + ')')
                                    );
                                } else {
                                    priceCell{{ $group_price->id }}.text(product.group_prices[{{ $group_price->id }}]);
                                }
                            } else {
                                priceCell{{ $group_price->id }}.text('-');
                            }
                            tr.append(priceCell{{ $group_price->id }});
                        @endforeach
                        
                        tbody.append(tr);
                    }
                } else {
                    tbody.append(
                        $('<tr>').append(
                            $('<td>').attr('colspan', 5 + {{ count($group_prices) }}).addClass('text-center')
                                .text('No data found for the selected filters.')
                        )
                    );
                }

                // Apply column visibility state
                applyColumnVisibility();
            }

            // Function to apply column visibility based on checkboxes
            function applyColumnVisibility() {
                const form = $('#toggleColumnsForm');
                if (!form.length) return;

                const checkboxes = form.find('input[type="checkbox"]');
                checkboxes.each(function() {
                    const groupPriceId = $(this).val();
                    // Target both header and data cells with the same class
                    const columns = $('.price-column-' + groupPriceId);
                    
                    if (this.checked) {
                        columns.show();
                    } else {
                        columns.hide();
                    }
                });
            }

            // Initial load
            loadPromotionData();

            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                updatePagination();
                displayCurrentPage();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                    displayCurrentPage();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                    displayCurrentPage();
                }
            });

            // Print functionality
            $('#printButton').on('click', function() {
                window.print();
            });

            // Toggle Group Price Columns
            $('#toggleGroupPriceColumns').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#groupPriceColumnsModal').modal('show');
            });

            // Apply column toggling
            $('#applyColumnToggle').on('click', function() {
                applyColumnVisibility();
                $('#groupPriceColumnsModal').modal('hide');
            });
        });
    </script>
    
    <script>
        const tablename = "#promotion-table";
        const reportname = "Promotion Products Report";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection