@extends('layouts.app')

@section('title', 'Products by Group Price')

<link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
@include('minireportb1::MiniReportB1.components.linkforinclude')


@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Base styles */
        body,
        table {
            font-family: 'Hanuman', serif;
        }

        .col-group-price {
            display: table-cell;
        }

        .col-no {
            width: 5% !important;
        }

        /* Checkbox styles for modals */
        .form-check-input.category-checkbox {
            width: 20px;
            height: 20px;
            margin-top: 0.3rem;
        }

        .form-check-label {
            font-size: 16px;
            margin-left: 0.5rem;
        }

        /* Button group */
        .button-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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
        }

        /* Modal styles */
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 15px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: bold;
        }

        .modal-body {
            padding: 15px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 15px;
            border-top: 1px solid #ddd;
        }

        .form-check {
            margin-bottom: 10px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .button-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")


    <div style="margin: 16px;" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div id="filter-container" class="filter-container"> 
                <div class="button-group">
                    <button id="selectCategoryButton" class="btn" data-toggle="modal" data-target="#categoryModal">
                        <i class="fas fa-filter"></i> ជ្រើសរើសក្រុម
                    </button>
                    <button id="toggleGroupPriceColumns" class="btn" data-toggle="modal" data-target="#groupPriceColumnsModal">
                        <i class="fas fa-eye"></i> តម្លៃតាមក្រុម
                    </button>
                </div>
                @include('minireportb1::MiniReportB1.components.printbutton')

            </div>
        @endcomponent
    </div>

    <div class="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            តារាងតម្លៃរួមតាមក្រុមថ្លៃ
        </h2>
    </div>

    <div class="reusable-table-container" style="margin: 16px">
        <table id="productgrouppriceall" class="reusable-table">
            <thead>
                <tr>
                    <th class="col-xs">#</th>
                    <th class="col-sm">ឈ្មោះទំនិញ</th>
                    <th class="col-category">ក្រុមទំនិញ</th>
                    <th class="col-selling-price">តម្លៃដើម</th> <!-- Add class here -->
                    <th class="col-sku">SKU</th>
                    @foreach ($group_prices as $group_price)
                        <th class="col-group-price" data-group-price-id="{{ $group_price->id }}">{{ $group_price->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($grouped_products as $product)
                    <tr data-category-id="{{ $product['category_id'] }}" data-product-name="{{ $product['product_name'] }}">
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>{{ $product['product_name'] }}</td>
                        <td>{{ $product['category_name'] }}</td>
                        <td class="col-selling-price number">{{ number_format($product['max_purchase_price'], 2) }}</td> <!-- Add class here -->
                        <td>{{ $product['sku'] }}</td>
                        @foreach ($group_prices as $group_price)
                            <td class="number">
                                {{ isset($product['group_prices'][$group_price->id]) ? number_format($product['group_prices'][$group_price->id], 2) : 'N/A' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 5 + count($group_prices) }}" class="text-center">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Category Modal --}}
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
                    @foreach ($products_by_category as $category_id => $products)
                        @php
                            $category_name = $categories[$category_id] ?? 'Uncategorized';
                        @endphp
                        <div class="category-group mb-4">
                            <div class="form-check">
                                <input class="form-check-input category-checkbox" type="checkbox" value="{{ $category_id }}" id="category{{ $category_id }}">
                                <label class="form-check-label font-weight-bold" for="category{{ $category_id }}">
                                    {{ $category_name }}
                                </label>
                            </div>
                            <div class="product-group pl-4" id="productsForCategory{{ $category_id }}" style="display: none;">
                                @foreach ($products as $product)
                                    <div class="form-check" style="margin-left: 32px">
                                        <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product['product_name'] }}"
                                            id="product{{ $product['id'] }}" data-category-id="{{ $category_id }}" style="transform: scale(2.5);">
                                        <label class="form-check-label" for="product{{ $product['id'] }}" style="margin-left: 8px;">
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

    <!-- Group Price Columns Modal -->
<!-- Group Price Columns Modal -->
<div class="modal fade" id="groupPriceColumnsModal" tabindex="-1" role="dialog" aria-labelledby="groupPriceColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupPriceColumnsModalLabel">Select Columns to Display</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Checkbox for "តម្លៃដើម" -->
                <div class="form-check">
                    <input class="form-check-input group-price-checkbox" type="checkbox" value="original_price"
                        id="originalPriceColumn" checked style="transform: scale(3);">
                    <label class="form-check-label" for="originalPriceColumn" style="margin-left: 16px; margin-top:8px">
                        តម្លៃដើម
                    </label>
                </div>

                <!-- Checkboxes for Group Prices -->
                @foreach ($group_prices as $group_price)
                    <div class="form-check">
                        <input class="form-check-input group-price-checkbox" type="checkbox" value="{{ $group_price->id }}"
                            id="groupPrice{{ $group_price->id }}" checked style="transform: scale(3);">
                        <label class="form-check-label" for="groupPrice{{ $group_price->id }}" style="margin-left: 16px; margin-top:8px">
                            {{ $group_price->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllGroupPrices">Select All</button>
                <button type="button" class="btn btn-sm btn-outline-danger" id="unselectAllGroupPrices">Unselect All</button>
                <button type="button" class="btn btn-primary" id="applyGroupPriceColumns">Apply</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script>
        const tablename = "#productgrouppriceall";
        const reportname = " តារាងតម្លៃរួមតាមក្រុមថ្លៃ";
        
        // Function to update row numbers
        function updateRowNumbers() {
            let counter = 1;
            document.querySelectorAll('#productgrouppriceall tbody tr').forEach(row => {
                if (row.style.display !== 'none') {
                    row.cells[0].textContent = counter++;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
    // Toggle Group Price Columns
    document.getElementById('toggleGroupPriceColumns')?.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#groupPriceColumnsModal').modal('show');
    });

    // Handle Column Selection
    document.getElementById('applyGroupPriceColumns')?.addEventListener('click', function () {
        // Toggle "តម្លៃដើម" column
        const showOriginalPrice = document.getElementById('originalPriceColumn').checked;
        document.querySelectorAll('.col-selling-price').forEach(column => {
            column.style.display = showOriginalPrice ? 'table-cell' : 'none';
        });

        // Toggle Group Price Columns
        const selectedGroupPrices = Array.from(document.querySelectorAll('.group-price-checkbox:checked')).map(
            checkbox => checkbox.value
        );

        document.querySelectorAll('.col-group-price').forEach(column => {
            const groupPriceId = column.getAttribute('data-group-price-id');
            const columnIndex = Array.from(column.parentElement.children).indexOf(column) + 1;

            if (selectedGroupPrices.includes(groupPriceId)) {
                column.style.display = 'table-cell';
                document.querySelectorAll('#productgrouppriceall tbody tr').forEach(row => {
                    const dataCell = row.querySelector(`td:nth-child(${columnIndex})`);
                    if (dataCell) dataCell.style.display = 'table-cell';
                });
            } else {
                column.style.display = 'none';
                document.querySelectorAll('#productgrouppriceall tbody tr').forEach(row => {
                    const dataCell = row.querySelector(`td:nth-child(${columnIndex})`);
                    if (dataCell) dataCell.style.display = 'none';
                });
            }
        });
        
        // Update row numbers after changing column visibility
        updateRowNumbers();

        $('#groupPriceColumnsModal').modal('hide');
    });

    // Select All Group Prices and Original Price
    document.getElementById('selectAllGroupPrices')?.addEventListener('click', function () {
        document.querySelectorAll('.group-price-checkbox, #originalPriceColumn').forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // Unselect All Group Prices and Original Price
    document.getElementById('unselectAllGroupPrices')?.addEventListener('click', function () {
        document.querySelectorAll('.group-price-checkbox, #originalPriceColumn').forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // Unselect all checkboxes by default
    document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Show/hide products when a category checkbox is clicked
    document.querySelectorAll('.category-checkbox').forEach(categoryCheckbox => {
        categoryCheckbox.addEventListener('change', function () {
            const categoryId = this.value;
            const productGroup = document.getElementById(`productsForCategory${categoryId}`);
            if (this.checked) {
                productGroup.style.display = 'block';
            } else {
                productGroup.style.display = 'none';
                document.querySelectorAll(`.product-checkbox[data-category-id="${categoryId}"]`).forEach(
                    productCheckbox => {
                        productCheckbox.checked = false;
                    });
            }
        });
    });

    // Select all categories and products
    document.getElementById('selectAllCategories')?.addEventListener('click', function () {
        document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
            checkbox.checked = true;
            const categoryId = checkbox.value;
            const productGroup = document.getElementById(`productsForCategory${categoryId}`);
            if (productGroup) productGroup.style.display = 'block';
        });
    });

    // Unselect all categories and products
    document.getElementById('unselectAllCategories')?.addEventListener('click', function () {
        document.querySelectorAll('.category-checkbox, .product-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            const categoryId = checkbox.value;
            const productGroup = document.getElementById(`productzsForCategory${categoryId}`);
            if (productGroup) productGroup.style.display = 'none';
        });
    });

    // Apply filters
    document.getElementById('applyCategoryFilter')?.addEventListener('click', function () {
        const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(
            checkbox => checkbox.value
        );
        const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(
            checkbox => checkbox.value
        );

        document.querySelectorAll('#productgrouppriceall tbody tr').forEach(row => {
            const categoryId = row.getAttribute('data-category-id');
            const productName = row.getAttribute('data-product-name');

            if (
                (selectedCategories.length === 0 || selectedCategories.includes(categoryId)) &&
                (selectedProducts.length === 0 || selectedProducts.includes(productName))
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updateRowNumbers();

        $('#categoryModal').modal('hide');
    });

    // Run initially to ensure correct numbering
    updateRowNumbers();
    
    // Handle printing - make sure row numbers are correct
    const printButton = document.getElementById('print-button');
    if (printButton) {
        printButton.addEventListener('click', function() {
            // Ensure row numbers are correctly set before printing
            updateRowNumbers();
        }, true); // Using capture phase to run before other handlers
    }
});
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
