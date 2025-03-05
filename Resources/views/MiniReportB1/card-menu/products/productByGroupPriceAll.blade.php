@extends('layouts.app')

@section('title', 'Products by Group Price')


    @section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Base styles for the body and table */
        body,
        table {
            font-family: 'Hanuman', serif;
        }

        .col-group-price {
            display: table-cell;
            /* Ensure columns are visible by default */
        }

        .col-no {
            width: 5% !important;
        }   

        /* Larger checkboxes */
        .form-check-input.category-checkbox {
            width: 20px;
            height: 20px;
            margin-top: 0.3rem;
        }

        /* Larger labels for better alignment */
        .form-check-label {
            font-size: 16px;
            margin-left: 0.5rem;
        }

        /* Print-specific styles */
        @media print {

            .col-no {
            width: 10% !important;
        }   

        .report-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
        page-break-inside: avoid; /* Prevent header from being split across pages */
    }

    .report-header h2 {
        font-size: 18pt !important; /* Larger font size for the header */
        font-weight: bold;
        margin: 0;
        padding: 10px 0;
        border-bottom: 2px solid #000; /* Add a border for better visibility */
    }


            body,
            table {
                font-family: 'Hanuman', serif;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .product-table {
                font-size: 10pt !important;
                width: 100% !important;
                margin: 0 !important;
                border-collapse: collapse;
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

            .product-table th,
            .product-table td {
                padding: 8px !important;
                white-space: normal !important;
                page-break-inside: avoid !important;
                border: 1px solid #ddd !important;
            }

            .product-table th {
                background-color: #f8f9fa !important;
                font-weight: bold;
                text-align: left;
            }

            .product-table td {
                text-align: left;
            }

            .number {
                font-family: monospace !important;
                white-space: nowrap !important;
                font-size: 0.95em !important;
            }

            .print-only-logo {
                display: block !important;
                text-align: center;
                margin-bottom: 20px !important;
                page-break-inside: avoid !important;
            }

            .print-only-logo img {
                max-height: 80px;
                max-width: 200px;
                height: auto;
            }

            .company-name {
                display: block !important;
                text-align: center;
                font-size: 18pt !important;
                font-weight: bold;
                margin-bottom: 10px !important;
                page-break-inside: avoid !important;
            }

            #filter-container,
            #print-button,
            .filtered-view-button-container,
            .dropdown,
            .btn,
            .print-controls {
                display: none !important;
            }
        }

        /* Table styles */
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

        .col-product,
        .col-category,
        .col-selling-price,
        .col-sku,
        .col-group-price {
            width: auto;
            max-width: 100%;
        }

        .number {
            text-align: right;
            font-family: monospace;
        }

        /* Responsive table container */
        .table-responsive {
            overflow-x: auto;
            margin: 20px 0;
        }

        /* Filter container */
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
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
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        /* Row count selector */
        .row-count-selector {
            width: 120px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            background-color: #fff;
            transition: border-color 0.3s ease;
        }

        .row-count-selector:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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

        .form-check-label {
            margin-left: 8px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .button-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn,
            .row-count-selector {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Logo and Company Name --}}
    @if (!empty($business->logo))
        <div class="print-only-logo">
            <img src="{{ asset('/uploads/business_logos/' . $business->logo) }}" alt="{{ $business->name }}"
                style="max-height: 100px;">
        </div>
    @endif

    <div class="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            តារាងតម្លៃរួមតាមក្រុមថ្លៃ
        </h2>
    </div>

    {{-- Filter Container --}}
    <div id="filter-container" class="filter-container">
        <div class="button-group">
            <select id="printOrientation" class="form-control row-count-selector" style="width: auto">
                <option value="portrait">Portrait</option>
                <option value="landscape">Landscape</option>
            </select>
            <button id="printButton" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
            {{-- <button id="exportButton" class="btn btn-success"><i class="fas fa-file-pdf"></i> Export to PDF</button> --}}
            <button id="exportExcelButton" class="btn btn-info"><i class="fas fa-file-excel"></i> Export to Excel</button>
            <button id="selectCategoryButton" class="btn btn-warning" data-toggle="modal" data-target="#categoryModal">
                <i class="fas fa-filter"></i> Select Categories
            </button>
            <select id="rowCountSelector" class="form-control row-count-selector" style="width: auto">
                <option value="10">Show 10 Rows</option>
                <option value="25">Show 25 Rows</option>
                <option value="50">Show 50 Rows</option>
                <option value="100">Show 100 Rows</option>
                <option value="all">Show All Rows</option>
            </select>
        </div>

        <!-- Add this button inside the filter container -->
        <button id="toggleGroupPriceColumns" class="btn btn-warning" data-toggle="modal"
            data-target="#groupPriceColumnsModal">
            <i class="fas fa-eye"></i> Toggle Group Price Columns
        </button>
    </div>

    <div class="table-responsive" style="margin: 16px">
        <table id="productgrouppriceall" class="product-table">
            <thead>
                <tr>
                    <th class="col-no">No</th> <!-- Added "No" column -->
                    <th class="col-product">ឈ្មោះទំនិញ</th>
                    <th class="col-category">ក្រុមទំនិញ</th>
                    <th class="col-selling-price">តម្លៃដើម</th>
                    <th class="col-sku">SKU</th>
                    @foreach ($group_prices as $group_price)
                        <th class="col-group-price" data-group-price-id="{{ $group_price->id }}">{{ $group_price->name }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($grouped_products as $product)
                    <tr data-category-id="{{ $product['category_id'] }}" data-product-name="{{ $product['product_name'] }}">
                        <td class="col-no">{{ $loop->iteration }}</td> <!-- Display row number -->
                        <td>{{ $product['product_name'] }}</td>
                        <td>{{ $product['category_name'] }}</td>
                        <td class="number">{{ number_format($product['max_purchase_price'], 2) }}</td>
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
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Select Categories and Products to Display</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Loop through categories -->
                    @foreach ($products_by_category as $category_id => $products)
                        @php
                            $category_name = $categories[$category_id] ?? 'Uncategorized';
                        @endphp
                        <div class="category-group mb-4">
                            <!-- Category Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input category-checkbox" type="checkbox"
                                    value="{{ $category_id }}" id="category{{ $category_id }}">
                                <label class="form-check-label font-weight-bold" for="category{{ $category_id }}">
                                    {{ $category_name }}
                                </label>
                            </div>
                            <!-- Loop through products under this category -->
                            <div class="product-group pl-4" id="productsForCategory{{ $category_id }}"
                                style="display: none;">
                                @foreach ($products as $product)
                                    <div class="form-check" style="margin-left: 32px">
                                        <input class="form-check-input product-checkbox" type="checkbox"
                                            value="{{ $product['product_name'] }}" id="product{{ $product['id'] }}"
                                            data-category-id="{{ $category_id }}" style="transform: scale(2.5);">
                                        <!-- Increase size -->
                                        <label class="form-check-label" for="product{{ $product['id'] }}"
                                            style="margin-left: 8px;"> <!-- Adjust label spacing -->
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
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllCategories">Select
                        All</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="unselectAllCategories">Unselect
                        All</button>
                    <button type="button" class="btn btn-primary" id="applyCategoryFilter">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Price Columns Modal -->
    <div class="modal fade" id="groupPriceColumnsModal" tabindex="-1" role="dialog"
        aria-labelledby="groupPriceColumnsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupPriceColumnsModalLabel">Select Group Price Columns to Display</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach ($group_prices as $group_price)
                        <div class="form-check">
                            <input class="form-check-input group-price-checkbox" type="checkbox"
                                value="{{ $group_price->id }}" id="groupPrice{{ $group_price->id }}" checked
                                style="transform: scale(3);">
                            <label class="form-check-label" for="groupPrice{{ $group_price->id }}" style="margin-left: 16px; margin-top:8px" >
                                {{ $group_price->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllGroupPrices">Select
                        All</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="unselectAllGroupPrices">Unselect
                        All</button>
                    <button type="button" class="btn btn-primary" id="applyGroupPriceColumns">Apply</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <!-- jQuery -->

    <script>
        const tablename = "#productgrouppriceall";
        document.addEventListener('DOMContentLoaded', function() {
            // Apply print orientation
            function applyPrintOrientation(orientation) {
                const style = document.createElement('style');
                style.innerHTML = `@page { size: A4 ${orientation}; margin: 1cm; }`;
                document.head.appendChild(style);
            }

            // Export to PDF
            document.getElementById('exportButton')?.addEventListener('click', async function() {
                try {
                    const orientation = document.getElementById('printOrientation').value;
                    const element = document.querySelector('.table-responsive');
                    const opt = {
                        margin: 1,
                        filename: 'products_by_group_price.pdf',
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2
                        },
                        jsPDF: {
                            unit: 'in',
                            format: 'letter',
                            orientation: orientation
                        }
                    };
                    await html2pdf().set(opt).from(element).save();
                } catch (error) {
                    console.error('Export error:', error);
                    alert('Error exporting to PDF: ' + error.message);
                }
            });

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
