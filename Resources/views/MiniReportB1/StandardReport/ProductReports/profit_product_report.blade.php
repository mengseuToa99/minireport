@extends('layouts.app')

@section('title', 'Product Profit Report')

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="filter-container">
            <form id="filterForm">
                <div class="form-group">
                    <label for="category_filter">ក្រុមទំនិញ:</label>
                    <select id="category_filter" class="form-control">
                        <option value="">ទាំងអស់</option>
                        <!-- Categories will be loaded via AJAX -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="type_filter">ប្រភេទទំនិញ:</label>
                    <select id="type_filter" class="form-control">
                        <option value="">ទាំងអស់</option>
                        <!-- Types will be loaded via AJAX -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="price_group_filter">ក្រុមតម្លៃ:</label>
                    <select id="price_group_filter" class="form-control">
                        <option value="">តម្លៃលក់ធម្មតា</option>
                        <!-- Price groups will be loaded via AJAX -->
                    </select>
                </div>
            </form>
            <div class="button-group">
                @include('minireportb1::MiniReportB1.components.printbutton')
                <button type="button" class="btn btn-default" id="toggleColumnsBtn">
                    <i class="fa fa-eye-slash"></i> លាក់/បង្ហាញជួរឈរ
                </button>
            </div>
        </div>
        @endcomponent
    </div>

    <div class="dropdown-menu dropdown-menu-right" id="columnToggleMenu" style="display: none; position: absolute; z-index: 1000;">
        <div class="dropdown-header">Select columns to show/hide</div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-item">
            <label class="checkbox-container">#
                <input type="checkbox" class="column-toggle" data-column="0" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ឈ្មោះទំនិញ
                <input type="checkbox" class="column-toggle" data-column="1" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ក្រុមទំនិញ
                <input type="checkbox" class="column-toggle" data-column="2" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ឯកតា
                <input type="checkbox" class="column-toggle" data-column="3" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ប្រភេទ
                <input type="checkbox" class="column-toggle" data-column="4" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">តម្លៃដើម
                <input type="checkbox" class="column-toggle" data-column="5" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">តម្លៃលក់
                <input type="checkbox" class="column-toggle" data-column="6" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ក្រុមតម្លៃ
                <input type="checkbox" class="column-toggle" data-column="7" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">មិនរួមពន្ធ
                <input type="checkbox" class="column-toggle" data-column="8" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">រួមបញ្ជូលពន្ធ
                <input type="checkbox" class="column-toggle" data-column="9" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ចំណេញ
                <input type="checkbox" class="column-toggle" data-column="10" checked>
                <span class="checkmark"></span>
            </label>
        </div>
        <div class="dropdown-item">
            <label class="checkbox-container">ភាគរយចំណេញ
                <input type="checkbox" class="column-toggle" data-column="11" checked>
                <span class="checkmark"><A/span>
            </label>
        </div>
    </div>

    @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => 'តារាងផលិតផលចំណេញជាភាគរយ'])
    @include('minireportb1::MiniReportB1.components.pagination')

    <div class="reusable-table-container">
        <table class="reusable-table" id="profit-product-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-lg">ឈ្មោះទំនិញ</th>
                    <th class="col-sm">ក្រុមទំនិញ</th>
                    <th class="col-xs">ឯកតា</th>
                    <th class="col-sm">ប្រភេទ</th>
                    <th class="col-sm">តម្លៃដើម</th>
                    <th class="col-sm">តម្លៃលក់</th>
                    <th class="col-sm">ក្រុមតម្លៃ</th>
                    <th class="col-sm">មិនរួមពន្ធ</th>
                    <th class="col-sm">រួមបញ្ជូលពន្ធ</th>
                    <th class="col-sm">ចំណេញ</th>
                    <th class="col-sm">ភាគរយចំណេញ</th>
                </tr>
            </thead>
            <tbody id="profit-product-table-body">
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
        
        <!-- Add pagination controls here -->
    </div>

    <script>
        const tablename = "#profit-product-table";
        const reportname = "Product Profit Report";
    </script>

    <script>
        $(document).ready(function () {
            // Pagination variables
            let currentPage = 1;
            let rowsPerPage = 25;
            let totalPages = 1;
            let allProducts = [];
            
            // Function to load product data via AJAX
            function loadProductData() {
                const formData = {
                    category_filter: $('#category_filter').val(),
                    type_filter: $('#type_filter').val(),
                    price_group_filter: $('#price_group_filter').val()
                };

                $.ajax({
                    url: '{{ route('sr_profit_product') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function () {
                        $('#profit-product-table-body').html(
                            '<tr><td colspan="12" class="text-center">Loading data...</td></tr>');
                    },
                    success: function (response) {
                        if (response.products && response.products.length > 0) {
                            // Process all products and save them
                            processProducts(response);
                            
                            // Update filters
                            updateFilters(response);
                            
                            // Update pagination
                            updatePagination();
                            
                            // Display current page
                            displayProductPage();
                        } else {
                            $('#profit-product-table-body').html(
                                '<tr><td colspan="12" class="text-center">No products found</td></tr>'
                            );
                            resetPagination();
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#profit-product-table-body').html(
                            '<tr><td colspan="12" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                        resetPagination();
                    }
                });
            }
            
            // Process product data into a flat array for pagination
            function processProducts(response) {
                allProducts = [];
                let counter = 1;
                
                $.each(response.products, function (index, product) {
                    if (product.variations && product.variations.length) {
                        $.each(product.variations, function (vIndex, variation) {
                            // Create a flat record for each product variation
                            const tax_rate = parseFloat(product.tax_rate) || 0;
                            const purchasePrice = parseFloat(variation.purchase_price) || 0;
                            const sellPrice = parseFloat(variation.sell_price) || 0;
                            const profit = parseFloat(variation.profit) || 0;
                            const profitPercent = parseFloat(variation.profit_percent) || 0;

                            const price_without_tax = sellPrice;
                            const price_with_tax = tax_rate > 0
                                ? price_without_tax * (1 + (tax_rate / 100))
                                : price_without_tax;
                                
                            allProducts.push({
                                index: counter++,
                                product_name: product.product_name,
                                variation_name: variation.variation_name,
                                category_name: product.category_name,
                                category_id: product.category_id,
                                unit_name: product.unit_name,
                                type: product.type,
                                purchase_price: purchasePrice,
                                sell_price: sellPrice,
                                group_price_name: variation.group_price_name,
                                price_without_tax: price_without_tax,
                                price_with_tax: price_with_tax,
                                profit: profit,
                                profit_percent: profitPercent
                            });
                        });
                    }
                });
            }
            
            // Update pagination controls
            function updatePagination() {
                // Calculate total pages
                totalPages = Math.ceil(allProducts.length / rowsPerPage);
                if (totalPages === 0) totalPages = 1;
                
                // Update UI
                $('#current-page').text(currentPage);
                $('#total-pages').text(totalPages);
                
                // Enable/disable pagination buttons
                $('#prev-page').prop('disabled', currentPage === 1);
                $('#next-page').prop('disabled', currentPage === totalPages);
                
                // Set the correct row limit in dropdown
                $('#row-limit').val(rowsPerPage);
            }
            
            // Reset pagination to default
            function resetPagination() {
                currentPage = 1;
                totalPages = 1;
                $('#current-page').text(1);
                $('#total-pages').text(1);
                $('#prev-page').prop('disabled', true);
                $('#next-page').prop('disabled', true);
            }
            
            // Display the current page of products
            function displayProductPage() {
                const tbody = $('#profit-product-table-body');
                tbody.empty();
                
                if (allProducts.length === 0) {
                    tbody.append('<tr><td colspan="12" class="text-center">No products found</td></tr>');
                    return;
                }
                
                // Calculate start and end index for current page
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = Math.min(startIndex + rowsPerPage, allProducts.length);
                
                // Display products for current page
                for (let i = startIndex; i < endIndex; i++) {
                    const product = allProducts[i];
                    
                    const row = `
                        <tr data-category-id="${product.category_id || ''}" data-type="${product.type || ''}">
                            <td>${product.index}</td>
                            <td>${product.product_name} ${product.variation_name != 'DUMMY' ? ' - ' + product.variation_name : ''}</td>
                            <td>${product.category_name}</td>
                            <td>${product.unit_name}</td>
                            <td>${product.type}</td>
                            <td class="number">${product.purchase_price.toFixed(2)}$</td>
                            <td class="number">${product.sell_price.toFixed(2)}$</td>
                            <td>${product.group_price_name}</td>
                            <td class="number">${product.price_without_tax.toFixed(2)}$</td>
                            <td class="number">${product.price_with_tax.toFixed(2)}$</td>
                            <td class="number ${product.profit >= 0 ? 'profit-positive' : 'profit-negative'}">
                                ${product.profit.toFixed(2)}$
                            </td>
                            <td class="number ${product.profit_percent >= 0 ? 'profit-positive' : 'profit-negative'}">
                                ${product.profit_percent.toFixed(2)}%
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                }
                
                // Apply column visibility to newly added rows
                applyColumnVisibility();
            }
            
            // Update filter dropdowns
            function updateFilters(response) {
                // Populate category filter
                if (response.categories) {
                    let categoryOptions = '<option value="">ទាំងអស់</option>';
                    $.each(response.categories, function (id, name) {
                        const selected = id == $('#category_filter').val() ? 'selected' : '';
                        categoryOptions += `<option value="${id}" ${selected}>${name}</option>`;
                    });
                    $('#category_filter').html(categoryOptions);
                }
                
                // Populate type filter
                if (response.types) {
                    let typeOptions = '<option value="">ទាំងអស់</option>';
                    $.each(response.types, function (id, name) {
                        const selected = id == $('#type_filter').val() ? 'selected' : '';
                        typeOptions += `<option value="${id}" ${selected}>${name}</option>`;
                    });
                    $('#type_filter').html(typeOptions);
                }
                
                // Populate price group filter
                if (response.price_groups) {
                    let priceGroupOptions = '<option value="">តម្លៃលក់ធម្មតា</option>';
                    $.each(response.price_groups, function (index, group) {
                        const selected = group.id == $('#price_group_filter').val() ? 'selected' : '';
                        priceGroupOptions += `<option value="${group.id}" ${selected}>${group.name}</option>`;
                    });
                    $('#price_group_filter').html(priceGroupOptions);
                }
            }
            
            // Apply column visibility based on checkboxes
            function applyColumnVisibility() {
                $('.column-toggle').each(function() {
                    const columnIndex = $(this).data('column');
                    const isVisible = $(this).is(':checked');
                    
                    $(tablename + ' th:nth-child(' + (columnIndex + 1) + ')').toggle(isVisible);
                    $(tablename + ' td:nth-child(' + (columnIndex + 1) + ')').toggle(isVisible);
                });
            }

            // Column visibility toggle functionality
            // Toggle column menu visibility
            $('#toggleColumnsBtn').click(function(e) {
                e.stopPropagation();
                const btnOffset = $(this).offset();
                $('#columnToggleMenu').css({
                    'top': btnOffset.top + $(this).outerHeight(),
                    'left': btnOffset.left
                }).toggle();
            });

            // Close menu when clicking elsewhere
            $(document).click(function() {
                $('#columnToggleMenu').hide();
            });

            // Prevent menu from closing when clicking inside it
            $('#columnToggleMenu').click(function(e) {
                e.stopPropagation();
            });

            // Handle column toggle
            $('.column-toggle').change(function() {
                const columnIndex = $(this).data('column');
                const isVisible = $(this).is(':checked');
                
                // Toggle column visibility
                $(tablename + ' th:nth-child(' + (columnIndex + 1) + ')').toggle(isVisible);
                $(tablename + ' td:nth-child(' + (columnIndex + 1) + ')').toggle(isVisible);
                
                // Save state to localStorage
                saveColumnState(columnIndex, isVisible);
            });

            // Load saved column states
            function loadColumnStates() {
                for (let i = 0; i < 12; i++) {
                    const isVisible = localStorage.getItem('column_' + i) !== 'false';
                    $('.column-toggle[data-column="' + i + '"]').prop('checked', isVisible);
                    $(tablename + ' th:nth-child(' + (i + 1) + ')').toggle(isVisible);
                    $(tablename + ' td:nth-child(' + (i + 1) + ')').toggle(isVisible);
                }
            }

            // Save column state
            function saveColumnState(columnIndex, isVisible) {
                localStorage.setItem('column_' + columnIndex, isVisible);
            }

            // Initialize column states
            loadColumnStates();
            
            // Pagination controls event handlers
            $('#prev-page').click(function() {
                if (currentPage > 1) {
                    currentPage--;
                    displayProductPage();
                    updatePagination();
                }
            });
            
            $('#next-page').click(function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayProductPage();
                    updatePagination();
                }
            });
            
            // Rows per page change handler
            $('#row-limit').change(function() {
                rowsPerPage = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                updatePagination();
                displayProductPage();
            });

            // Initial load
            loadProductData();

            // Event listener for filter changes
            $('#category_filter, #type_filter, #price_group_filter').on('change', function () {
                currentPage = 1; // Reset to first page when changing filters
                loadProductData();
            });
        });
    </script>

 
@endsection
