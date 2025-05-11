@extends('layouts.app')

@section('title', 'របាយការណ៍ការលក់ផលិតផល')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">
    <style>
        /* Ensure proper spacing in filter container */
        .filter-container {
            margin-bottom: 20px;
        }
        
        #filterForm {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        /* Better spacing for buttons */
        .button-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            background-color: #0f8800;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #0a6600;
            transform: translateY(-1px);
        }
        
        /* Category modal styling */
        .form-check {
            margin-bottom: 10px;
        }
        
        .form-check-input.category-checkbox {
            width: 20px;
            height: 20px;
            margin-top: 0.3rem;
        }
        
        .form-check-label {
            font-size: 16px;
            margin-left: 0.5rem;
        }
        
        .product-group {
            transition: all 0.3s ease;
            margin-top: 5px;
            padding-left: 15px !important;
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
                
                <!-- Add location filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="location_id">@lang('business.business_location'):</label>
                        <select class="form-control select2" id="location_id" name="location_id">
                            <option value="">@lang('report.all_locations')</option>
                            @foreach($locations as $key => $value)
                                <option value="{{ $key }}" {{ $location_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
            <div class="button-group">
                <button id="selectCategoryButton" class="btn" data-toggle="modal" data-target="#categoryModal">
                    <i class="fas fa-filter"></i> ជ្រើសរើសក្រុម
                </button>
                <!-- Add export button -->
               <button id="exportExcelButton" class="btn" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
            </div>
            @include('minireportb1::MiniReportB1.components.printbutton')
        </div>
        @endcomponent
    </div>

    <div class="report-header" id="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-light tw-text-center normal-view-title" style="font-size: 20px;">
            {{ $business->name }}
        </h2>
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            របាយការណ៍ការលក់ផលិតផល
        </h2>
    </div>

    <div class="reusable-table-container">
        <table class="reusable-table" id="product-sales-table">
            <thead>
                <tr>
                    <th class="col-xs">ល.រ</th>
                    <th class="col-md">ឈ្មោះផលិតផល</th>
                    <th class="col-sm">លេខកូដ</th>
                    <th class="col-md">ប្រភេទ</th>
                    <th class="col-sm">ចំនួនដែលបានលក់ & ប្រើប្រាស់</th>
                </tr>
            </thead>
            <tbody id="product-sales-table-body">
                @if(count($formatted_data) > 0)
                    @foreach($formatted_data as $index => $row)
                        <tr data-category-id="{{ $row['category_id'] ?? 'no_category' }}" data-product-name="{{ $row['product_name'] }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['product_name'] }}</td>
                            <td>{{ $row['sku'] }}</td>
                            <td>{{ $row['description'] }}</td>
                            <td class="text-right">{{ (int) $row['quantity'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">មិនមានទិន្នន័យសម្រាប់រយៈពេលដែលបានជ្រើសរើស។</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Select Categories and Products to Display</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach ($products_by_category as $category_id => $category_data)
                        @php
                            $category_name = $categories[$category_id] ?? ($category_id === 'no_category' ? 'No Category' : 'Uncategorized');
                        @endphp
                        <div class="category-group mb-4">
                            <div class="form-check">
                                <input class="form-check-input category-checkbox" type="checkbox" value="{{ $category_id }}" id="category_{{ $category_id }}">
                                <label class="form-check-label font-weight-bold" for="category_{{ $category_id }}">
                                    {{ $category_name }}
                                </label>
                            </div>
                            <div class="product-group pl-4" id="productsForCategory_{{ $category_id }}" style="display: none;">
                                @foreach ($category_data['products'] as $product)
                                    <div class="form-check" style="margin-left: 32px">
                                        <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product['product_name'] }}"
                                            id="product_{{ $product['product_id'] }}" data-category-id="{{ $category_id }}" style="transform: scale(2.5);">
                                        <label class="form-check-label" for="product_{{ $product['product_id'] }}" style="margin-left: 8px;">
                                            {{ $product['product_name'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllCategories">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="unselectAllCategories">Unselect All</button>
                    <button type="button" class="btn btn-primary" id="applyCategoryFilter">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Debug any business logo img tags
            console.log('Checking for business logo images...');
            const logoImages = document.querySelectorAll('img[src*="business_logos"]');
            console.log('Found business logo images:', logoImages.length);
            
            if (logoImages.length > 0) {
                logoImages.forEach((img, index) => {
                    console.log(`Logo ${index + 1} src:`, img.src);
                    
                    // Add error handler to prevent 404 errors
                    img.onerror = function() {
                        console.log('Logo image failed to load:', this.src);
                        this.style.display = 'none'; // Hide broken image
                        // Or replace with a placeholder
                        // this.src = '{{ asset('modules/minireportb1/image/placeholder.png') }}';
                    };
                });
            }
            
            // Set up event handlers with defensive checks
            
            // Initialize datepicker if it exists
            if ($("#start_date").length && $("#end_date").length) {
                $("#start_date, #end_date").datepicker({
                    dateFormat: "yy-mm-dd"
                });
            }

            // Initialize Select2 with defensive check
            if ($('.select2').length) {
                $('.select2').select2();
            }

            // Add event handlers for category checkboxes
            $(document).on('change', '.category-checkbox', function() {
                const categoryId = this.value;
                const productGroup = document.getElementById(`productsForCategory_${categoryId}`);
                if (productGroup) {
                    if (this.checked) {
                        productGroup.style.display = 'block';
                    } else {
                        productGroup.style.display = 'none';
                        // Use defensive check before querying for product checkboxes
                        const productCheckboxes = $(`.product-checkbox[data-category-id="${categoryId}"]`);
                        if (productCheckboxes.length) {
                            productCheckboxes.prop('checked', false);
                        }
                    }
                } else {
                    console.warn(`Product group not found for category: ${categoryId}`);
                }
            });

            // Select All Categories button
            $('#selectAllCategories').on('click', function() {
                $('.category-checkbox, .product-checkbox').prop('checked', true);
                $('.product-group').show();
            });

            // Unselect All Categories button
            $('#unselectAllCategories').on('click', function() {
                $('.category-checkbox, .product-checkbox').prop('checked', false);
                $('.product-group').hide();
            });

            // Apply Category Filter button
            $('#applyCategoryFilter').on('click', function() {
                const selectedCategories = $('.category-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                const selectedProducts = $('.product-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                // Filter the table rows
                $('#product-sales-table tbody tr').each(function() {
                    const categoryId = $(this).attr('data-category-id');
                    const productName = $(this).attr('data-product-name');

                    if (
                        (selectedCategories.length === 0 || selectedCategories.includes(categoryId)) &&
                        (selectedProducts.length === 0 || selectedProducts.includes(productName))
                    ) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                $('#categoryModal').modal('hide');
            });

            // Input change handler with defensive check
            if ($('#location_id').length && $('#date_filter').length && $('#start_date').length && $('#end_date').length) {
                $('#location_id, #date_filter, #start_date, #end_date').on('change', function () {
                loadProductSalesData();
            });
            }

         function loadProductSalesData() {
    const formData = {
        date_filter: $('#date_filter').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        location_id: $('#location_id').val(),
    };

    $.ajax({
        url: '{{ route('sr_product_sale_report') }}',
        type: 'GET',
        dataType: 'json',
        data: formData,
        beforeSend: function () {
            $('#product-sales-table-body').html(
                '<tr><td colspan="5" class="text-center">កំពុងផ្ទុកទិន្នន័យ...</td></tr>'
            );
        },
        success: function (response) {
            const tbody = $('#product-sales-table-body');
            tbody.empty();

            if (response.data.length > 0) {
                // Rebuild the products_by_category object from the response data
                let categories = {};
                let products_by_category = {};
                
                $.each(response.data, function (index, row) {
                    // Use a safe default for category_id if it's not present
                    const categoryId = row.category_id || 'no_category';
                    const categoryName = row.category_name || 'Uncategorized';
                    
                    // Add to categories object
                    if (!categories[categoryId]) {
                        categories[categoryId] = categoryName;
                    }
                    
                    // Add to products_by_category
                    if (!products_by_category[categoryId]) {
                        products_by_category[categoryId] = {
                            category_name: categoryName,
                            products: []
                        };
                    }
                    
                    // Add product to the category
                    products_by_category[categoryId].products.push({
                        product_id: row.product_id || index,
                        product_name: row.product_name
                    });
                    
                    // Create table row
                    const tr = $('<tr>').append(
                        $('<td>').text(index + 1),
                        $('<td>').text(row.product_name),
                        $('<td>').text(row.sku),
                        $('<td>').text(row.description),
                        $('<td class="text-right">').text(row.quantity)
                    );
                    tr.attr('data-category-id', categoryId);
                    tr.attr('data-product-name', row.product_name);
                    tbody.append(tr);
                });
                
                // Rebuild category modal content
                rebuildCategoryModal(categories, products_by_category);
                } else {
                tbody.append(
                    '<tr><td colspan="5" class="text-center">មិនមានទិន្នន័យសម្រាប់រយៈពេលដែលបានជ្រើសរើស។</td></tr>'
                );
            }
        },
        error: function (xhr) {
            console.error('កំហុស៖', xhr.responseText);
            $('#product-sales-table-body').html(
                '<tr><td colspan="5" class="text-center text-danger">កំហុសក្នុងការផ្ទុកទិន្នន័យ។ សូមព្យាយាមម្តងទៀត។</td></tr>'
            );
        }
    });
}

// Function to rebuild category modal content
function rebuildCategoryModal(categories, products_by_category) {
    const modalBody = $('#categoryModal .modal-body');
    modalBody.empty();
    
    if (Object.keys(products_by_category).length > 0) {
        $.each(products_by_category, function(categoryId, categoryData) {
            const categoryName = categories[categoryId] || 'Uncategorized';
            
            const categoryGroup = $('<div>').addClass('category-group mb-4');
            
            const categoryCheck = $('<div>').addClass('form-check').append(
                $('<input>').addClass('form-check-input category-checkbox')
                    .attr({
                        type: 'checkbox',
                        value: categoryId,
                        id: 'category_' + categoryId
                    }),
                $('<label>').addClass('form-check-label font-weight-bold')
                    .attr('for', 'category_' + categoryId)
                    .text(categoryName + ' (' + (categoryData.products ? categoryData.products.length : 0) + ')')
            );
            
            const productGroup = $('<div>').addClass('product-group pl-4')
                .attr('id', 'productsForCategory_' + categoryId)
                .css('display', 'none');
                
            if (categoryData.products && categoryData.products.length > 0) {
                // Create a grid layout for products if there are many
                const useGrid = categoryData.products.length > 5;
                const productContainer = useGrid ? 
                    $('<div>').addClass('product-grid').css({
                        'display': 'grid',
                        'grid-template-columns': 'repeat(auto-fill, minmax(200px, 1fr))',
                        'gap': '10px'
                    }) : 
                    $('<div>');
                
                $.each(categoryData.products, function(_, product) {
                    const productCheck = $('<div>').addClass('form-check')
                        .css({
                            'margin-left': useGrid ? '0' : '32px',
                            'padding': '5px'
                        })
                        .append(
                            $('<input>').addClass('form-check-input product-checkbox')
                                .attr({
                                    type: 'checkbox',
                                    value: product.product_name,
                                    id: 'product_' + product.product_id,
                                    'data-category-id': categoryId
                                })
                                .css('transform', 'scale(1.2)'),
                            $('<label>').addClass('form-check-label')
                                .attr('for', 'product_' + product.product_id)
                                .css({
                                    'margin-left': '5px',
                                    'font-size': '14px',
                                    'white-space': 'nowrap',
                                    'overflow': 'hidden',
                                    'text-overflow': 'ellipsis',
                                    'max-width': useGrid ? '180px' : '300px',
                                    'display': 'block'
                                })
                                .text(product.product_name)
                        );
                    
                    productContainer.append(productCheck);
                });
                
                productGroup.append(productContainer);
            }
            
            categoryGroup.append(categoryCheck, productGroup);
            modalBody.append(categoryGroup);
        });
        
        // Reattach event handler
        $('.category-checkbox').on('change', function() {
            const categoryId = this.value;
            const productGroup = document.getElementById(`productsForCategory_${categoryId}`);
            if (productGroup) {
                if (this.checked) {
                    $(productGroup).slideDown(300);
                } else {
                    $(productGroup).slideUp(200);
                    // Use defensive check before querying for product checkboxes
                    const productCheckboxes = $(`.product-checkbox[data-category-id="${categoryId}"]`);
                    if (productCheckboxes.length) {
                        productCheckboxes.prop('checked', false);
                    }
                }
            }
        });
    } else {
        modalBody.append(
            $('<div>').addClass('alert alert-info').append(
                $('<p>').text('No categories or products available for filtering. Please check if data is properly loaded from the database.')
            )
        );
    }
}

                 function exportToExcel() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    location_id: $('#location_id').val(),
                    export_excel: true
                };

                const url = new URL('{{ route('sr_product_sale_report') }}');
                Object.keys(formData).forEach(key => url.searchParams.append(key, formData[key]));

                window.open(url, '_blank');
            }

            // Make exportToExcel available globally
            window.exportToExcel = exportToExcel;

            // Initial load
            loadProductSalesData();


                // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_month_range') {
                    $('#custom_range_inputs').fadeIn(300); // Use smooth animation
                } else {
                    $('#custom_range_inputs').fadeOut(200, function() {
                        loadProductSalesData(); // Reload data after animation is complete
                    });
                }
            });



            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadProductSalesData(); // Reload data for custom range
                } else {
                    // Show validation message
                    alert('Please select both start and end dates.');
                }
            });
        });
    </script>
@endsection