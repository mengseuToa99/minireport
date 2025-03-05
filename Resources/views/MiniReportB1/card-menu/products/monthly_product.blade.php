@extends('layouts.app')
@section('title', 'Monthly Stock Report')

@section('css')
    <!-- Keep existing styles, add date filter styles -->
    <style>
        .date-filter-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .date-filter-group {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-filter-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .date-filter-label {
            font-weight: 500;
            color: #333;
        }

        .date-filter-select,
        .date-filter-input {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 200px;
        }

        .form-check-input.category-checkbox {
            width: 25px;
            /* Adjust size as needed */
            height: 25px;
            /* Adjust size as needed */
            margin-top: 0.3rem;
            /* Align with label */
        }

        /* Date Filter Container */
        .date-filter-container {
            display: flex;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Date Filter Group */
        .date-filter-group {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            /* Allow wrapping on smaller screens */
        }

        /* Date Filter Item */
        .date-filter-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Date Filter Label */
        .date-filter-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0;
        }

        /* Date Filter Select and Input */
        .date-filter-select,
        .date-filter-input {
            padding: 8px 12px;
            border: 1px solid #ced4da !important;
            /* Override conflicting border styles */
            border-radius: 4px !important;
            /* Override conflicting border-radius */
            width: 200px;
            font-size: 14px;
            background-color: #fff !important;
            /* Ensure white background */
            color: #333 !important;
            /* Ensure dark text color */
            transition: border-color 0.3s ease;
        }


        .date-filter-select:focus,
        .date-filter-input:focus {
            border-color: #80bdff !important;
            /* Override conflicting focus styles */
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }

        /* Date Filter Buttons */
        .date-filter-item .btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .date-filter-item .btn:hover {
            transform: translateY(-1px);
        }

        .date-filter-item .btn:active {
            transform: translateY(0);
        }

        /* Add these styles to the target code's CSS section */
        .product-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        .product-table th,
        .product-table td {
            word-break: break-word;
            white-space: normal;
            overflow-wrap: break-word;
            padding: 8px;
            border: 1px solid #000;
            box-sizing: border-box;
        }

        .col-no {
        width: 6% !important; /* Slightly increase the width */
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
        font-family: monospace;
    }

        .col-product {
            width: 30% !important;
        }

        .col-category {
            width: 20% !important;
        }

        .col-selling-price {
            width: 15% !important;
        }

        .col-sku {
            width: 15% !important;
        }

        .col-group-price {
            width: 20% !important;
        }

        .number {
            text-align: center;
            /* Center horizontally */
            vertical-align: middle;
            /* Center vertically */
            font-family: monospace;
        }

        /* Responsive table container */
        .table-responsive {
            overflow-x: auto;
            margin: 20px 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .date-filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .date-filter-select,
            .date-filter-input {
                width: 100%;
            }

            .date-filter-item .btn {
                width: 100%;
                text-align: center;
            }
        }

        @media print {
            .col-no {
        width: 6% !important; /* Slightly increase the width */
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
        font-family: monospace;
    }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Report Header --}}
    <div class="report-header">
        <div>
            <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
                របាយការណ៏ស្តុកប្រចាំខែ
            </h2>
        </div>
    </div>

    {{-- Date Filter Section --}}
    <div class="date-filter-container">
        <form method="GET" action="{{ route('products.monthly-stock') }}">
            <div class="date-filter-group">
                <div class="date-filter-item">
                    <label class="date-filter-label" for="month">ខែ:</label>
                    <select class="date-filter-select" name="month" id="month">
                        <option value="">-- ជ្រើសរើសខែ --</option>
                        @foreach ($months as $key => $name)
                            <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="date-filter-item">
                    <label class="date-filter-label" for="year">ឆ្នាំ:</label>
                    <input type="number" class="date-filter-input" name="year" id="year" min="2020"
                        max="{{ date('Y') + 1 }}" value="{{ request('year', date('Y')) }}">
                </div>

                <div class="date-filter-item">
                    <label class="date-filter-label" for="location_id">ទីតាំង:</label>
                    <select class="date-filter-select" name="location_id" id="location_id">
                        <option value="all">-- ទីតាំងទាំងអស់ --</option>
                        @foreach ($business_locations as $id => $name)
                            <option value="{{ $id }}" {{ request('location_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="date-filter-item">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> ត្រង
                    </button>
                    <a href="{{ route('products.monthly-stock') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> សម្រួលឡើងវិញ
                    </a>
                </div>
            </div>
        </form>

        <button id="selectCategoryButton" class="btn btn-warning" data-toggle="modal" data-target="#categoryModal"
            style="width: 160px; height: 40px; margin: 16px">
            <i class="fas fa-filter"></i> Select Categories
        </button>

         <div >
            <button id="printButton" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
            <button id="exportExcelButton" class="btn btn-info"><i class="fas fa-file-excel"></i> Export to Excel</button>
        </div>
    </div>


    {{-- Category Modal --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Added modal-lg for larger modal -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Select Categories to Display</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Select All / Unselect All Buttons --}}

                    {{-- Category Checkboxes --}}
                    @foreach ($categories as $id => $name)
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox" value="{{ $id }}"
                                id="category{{ $id }}" checked>
                            <label class="form-check-label" for="category{{ $id }}">
                                {{ $name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer" style="display: flex">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllCategories">Select
                        All</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="unselectAllCategories">Unselect
                        All</button>
                    <button type="button" class="btn btn-primary" id="applyCategoryFilter">Apply</button>
                </div>
            </div>
        </div>
    </div>

{{-- Blade Template --}}
<div class="table-responsive">
    <table class="product-table" id="productTable">
        <thead>
            <tr>
                <th class="col-no">ល.រ</th>
                <th class="col-product">ឈ្មោះផលិតផល</th>
                <th class="col-category">ប្រភេទផលិតផល</th>
                <th class="col-sku">លេខកូដ</th>
                <th class="col-selling-price">តម្លៃលក់</th>
                <th class="col-group-price">ដើមគ្រា</th>
                <th class="col-group-price">ទិញចូល</th>
                <th class="col-group-price">លក់ចេញ</th>
                <th class="col-group-price">កែតម្រូវ</th>
                <th class="col-group-price">ផ្ទេរចូល</th>
                <th class="col-group-price">ផ្ទេរចេញ</th>
                <th class="col-group-price">ចុងគ្រា</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products_by_category as $category)
                @foreach($category['products'] as $index => $product)
                    <tr data-category-id="{{ $category['category_id'] }}">
                        <td class="col-no">{{ $index + 1 }}</td>
                       <td>
                            <!-- Make the product name clickable -->
                            @if ($product['product_link'])
                                <a href="{{ $product['product_link'] }}">{{ $product['product_name'] }}</a>
                            @else
                                {{ $product['product_name'] }}
                            @endif
                        </td>
                        <td>{{ $category['category_name'] }}</td>
                        <td>{{ $product['sku'] }}</td>
                        <td class="number">{{ number_format($product['selling_price'], 2) }}</td>
                        <td class="number">{{ number_format($product['opening_stock']) }}</td>
                        <td class="number">{{ number_format($product['total_purchases']) }}</td>
                        <td class="number">{{ number_format($product['total_sales']) }}</td>
                        <td class="number">{{ number_format($product['total_adjustments']) }}</td>
                        <td class="number">{{ number_format($product['total_transfers_in']) }}</td>
                        <td class="number">{{ number_format($product['total_transfers_out']) }}</td>
                        <td class="number">{{ number_format($product['final_stock']) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" class="text-center">No products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('javascript')
    <!-- Keep existing JavaScript, add date filter handling -->
    <script>
        const tablename = "#productTable"
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date inputs
            const yearInput = document.getElementById('year');
            if (yearInput) {
                yearInput.addEventListener('change', function(e) {
                    if (this.value < 2020) this.value = 2020;
                    if (this.value > new Date().getFullYear() + 1) {
                        this.value = new Date().getFullYear();
                    }
                });
            }

            // Update report title with date range
            const updateReportTitle = () => {
                const month = document.getElementById('month').value;
                const year = document.getElementById('year').value;
                const title = document.querySelector('.normal-view-title');

                if (month && year) {
                    title.textContent = `របាយការណ៏ស្តុកប្រចាំខែ ${month}/${year}`;
                } else {
                    title.textContent = 'របាយការណ៏ស្តុកសរុប';
                }
            };

            document.getElementById('month')?.addEventListener('change', updateReportTitle);
            document.getElementById('year')?.addEventListener('change', updateReportTitle);
            updateReportTitle();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide products when a category checkbox is clicked
            document.querySelectorAll('.category-checkbox').forEach(categoryCheckbox => {
                categoryCheckbox.addEventListener('change', function() {
                    const categoryId = this.value;
                    const productGroup = document.getElementById(
                        `productsForCategory${categoryId}`);
                    if (this.checked) {
                        productGroup.style.display = 'block';
                    } else {
                        productGroup.style.display = 'none';
                        // Uncheck all product checkboxes under this category
                        document.querySelectorAll(
                            `.product-checkbox[data-category-id="${categoryId}"]`).forEach(
                            productCheckbox => {
                                productCheckbox.checked = false;
                            });
                    }
                });
            });

            // Select all categories and products
            document.getElementById('selectAllCategories')?.addEventListener('click', function() {
                document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                    // Show all product groups
                    const categoryId = checkbox.value;
                    const productGroup = document.getElementById(
                        `productsForCategory${categoryId}`);
                    if (productGroup) {
                        productGroup.style.display = 'block';
                    }
                });
            });

            // Unselect all categories and products
            document.getElementById('unselectAllCategories')?.addEventListener('click', function() {
                document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    // Hide all product groups
                    const categoryId = checkbox.value;
                    const productGroup = document.getElementById(
                        `productsForCategory${categoryId}`);
                    if (productGroup) {
                        productGroup.style.display = 'none';
                    }
                });
            });

            // Apply filters
            document.getElementById('applyCategoryFilter')?.addEventListener('click', function() {
                const selectedCategories = Array.from(document.querySelectorAll(
                    '.category-checkbox:checked')).map(
                    checkbox => checkbox.value
                );
                const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                    .map(
                        checkbox => checkbox.value
                    );

                // Filter the table rows based on selected categories and products
                document.querySelectorAll('.product-table tbody tr').forEach(row => {
                    const categoryId = row.getAttribute('data-category-id');
                    const productName = row.getAttribute('data-product-name');

                    if (
                        (selectedCategories.length === 0 || selectedCategories.includes(
                            categoryId)) &&
                        (selectedProducts.length === 0 || selectedProducts.includes(productName))
                    ) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Close the modal
                $('#categoryModal').modal('hide');
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print functionality
          
            // Export to Excel
            document.getElementById('exportExcelButton')?.addEventListener('click', function() {
                const table = document.querySelector('.product-table');
                const rows = table.querySelectorAll('tr');
                let csvContent = "data:text/csv;charset=utf-8,";
                rows.forEach(function(row) {
                    let rowData = [];
                    row.querySelectorAll('th, td').forEach(function(cell) {
                        rowData.push(cell.innerText);
                    });
                    csvContent += rowData.join(",") + "\r\n";
                });
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "products_by_group_price.csv");
                document.body.appendChild(link);
                link.click();
            });

            // Toggle Group Price Columns
            document.getElementById('toggleGroupPriceColumns')?.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default behavior
                e.stopPropagation(); // Prevent event propagation
                $('#groupPriceColumnsModal').modal('show');
            });

            // Handle Group Price Column Selection
            document.getElementById('applyGroupPriceColumns')?.addEventListener('click', function() {
                const selectedGroupPrices = Array.from(document.querySelectorAll(
                    '.group-price-checkbox:checked')).map(
                    checkbox => checkbox.value
                );

                // Loop through each group price column and its corresponding data cells
                document.querySelectorAll('.col-group-price').forEach(column => {
                    const groupPriceId = column.getAttribute('data-group-price-id');
                    const columnIndex = Array.from(column.parentElement.children).indexOf(column) +
                        1; // Get column index

                    if (selectedGroupPrices.includes(groupPriceId)) {
                        // Show the column header
                        column.style.display = 'table-cell';

                        // Show the corresponding data cells in all rows
                        document.querySelectorAll('.product-table tbody tr').forEach(row => {
                            const dataCell = row.querySelector(
                                `td:nth-child(${columnIndex})`);
                            if (dataCell) {
                                dataCell.style.display = 'table-cell';
                            }
                        });
                    } else {
                        // Hide the column header
                        column.style.display = 'none';

                        // Hide the corresponding data cells in all rows
                        document.querySelectorAll('.product-table tbody tr').forEach(row => {
                            const dataCell = row.querySelector(
                                `td:nth-child(${columnIndex})`);
                            if (dataCell) {
                                dataCell.style.display = 'none';
                            }
                        });
                    }
                });

                // Close the modal
                $('#groupPriceColumnsModal').modal('hide');
            });

            // Select All Group Prices
            document.getElementById('selectAllGroupPrices')?.addEventListener('click', function() {
                document.querySelectorAll('.group-price-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
            });

            // Unselect All Group Prices
            document.getElementById('unselectAllGroupPrices')?.addEventListener('click', function() {
                document.querySelectorAll('.group-price-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
            });

            // Unselect all checkboxes by default
            document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Show/hide products when a category checkbox is clicked
            document.querySelectorAll('.category-checkbox').forEach(categoryCheckbox => {
                categoryCheckbox.addEventListener('change', function() {
                    const categoryId = this.value;
                    const productGroup = document.getElementById(
                        `productsForCategory${categoryId}`);
                    if (this.checked) {
                        productGroup.style.display = 'block';
                    } else {
                        productGroup.style.display = 'none';
                        // Uncheck all product checkboxes under this category
                        document.querySelectorAll(
                            `.product-checkbox[data-category-id="${categoryId}"]`).forEach(
                            productCheckbox => {
                                productCheckbox.checked = false;
                            });
                    }
                });
            });

            // Select all categories and products
            document.getElementById('selectAllCategories')?.addEventListener('click', function() {
                document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                    // Show all product groups
                    const categoryId = checkbox.value;
                    const productGroup = document.getElementById(
                        `productsForCategory${categoryId}`);
                    if (productGroup) {
                        productGroup.style.display = 'block';
                    }
                });
            });

            // Unselect all categories and products
            document.getElementById('unselectAllCategories')?.addEventListener('click', function() {
                document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    // Hide all product groups
                    const categoryId = checkbox.value;
                    const productGroup = document.getElementById(
                        `productsForCategory${categoryId}`);
                    if (productGroup) {
                        productGroup.style.display = 'none';
                    }
                });
            });

            // Apply filters
            document.getElementById('applyCategoryFilter')?.addEventListener('click', function() {
                const selectedCategories = Array.from(document.querySelectorAll(
                    '.category-checkbox:checked')).map(
                    checkbox => checkbox.value
                );
                const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                    .map(
                        checkbox => checkbox.value
                    );

                // Filter the table rows based on selected categories and products
                document.querySelectorAll('.product-table tbody tr').forEach(row => {
                    const categoryId = row.getAttribute('data-category-id');
                    const productName = row.getAttribute('data-product-name');

                    if (
                        (selectedCategories.length === 0 || selectedCategories.includes(
                            categoryId)) &&
                        (selectedProducts.length === 0 || selectedProducts.includes(productName))
                    ) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Close the modal
                $('#categoryModal').modal('hide');
            });

            // Row Count Selector
            document.getElementById('rowCountSelector')?.addEventListener('change', function() {
                const rowCount = this.value;
                const rows = document.querySelectorAll('.product-table tbody tr');
                rows.forEach((row, index) => {
                    if (rowCount === 'all' || index < rowCount) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
