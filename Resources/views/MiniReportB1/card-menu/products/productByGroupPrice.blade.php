@extends('layouts.app')
@section('title', 'Products by Group Price')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body,
        table {
            font-family: 'Hanuman', serif;
        }

        /* Larger checkboxes */
        .form-check-input.category-checkbox {
            width: 20px;
            /* Adjust size as needed */
            height: 20px;
            /* Adjust size as needed */
            margin-top: 0.3rem;
            /* Align with label */
        }

        /* Larger labels for better alignment */
        .form-check-label {
            font-size: 16px;
            /* Adjust font size as needed */
            margin-left: 0.5rem;
            /* Add some spacing between checkbox and label */
        }

        @media print {

            body,
            table {
                font-family: 'Hanuman', serif;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            /* A4 Portrait */
            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            /* A4 Landscape */
            @page landscape {
                size: A4 landscape;
                margin: 1cm;
            }

            /* Base font sizes */
            .product-table {
                font-size: 9pt;
                width: 100% !important;
                margin: 0 !important;
            }

            /* Column widths for portrait */
            .col-product {
                width: 25% !important;
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
                width: 15% !important;
            }

            /* Container adjustments */
            .table-responsive {
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-width: 100% !important;
            }

            /* Table adjustments */
            .product-table {
                page-break-inside: auto !important;
            }

            /* Adjust table cells for print */
            .product-table th,
            .product-table td {
                padding: 3px !important;
                white-space: normal !important;
                page-break-inside: avoid !important;
                border: 1px solid #000 !important;
            }

            /* Optimize text wrapping for smaller sizes */
            .product-table td:nth-child(1),
            .product-table td:nth-child(2) {
                font-size: 0.9em !important;
                line-height: 1.2 !important;
            }

            /* Ensure numbers remain readable */
            .number {
                font-family: monospace !important;
                white-space: nowrap !important;
                font-size: 0.95em !important;
            }

            /* Header adjustments */
            .print-only-logo {
                display: block !important;
                text-align: center;
                margin-bottom: 10px !important;
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
                margin-bottom: 5px !important;
                page-break-inside: avoid !important;
            }

            /* Hide unnecessary elements */
            #filter-container,
            #print-button,
            .filtered-view-button-container,
            .dropdown,
            .btn,
            .normal-view-title,
            .print-controls {
                display: none !important;
            }
        }

        .product-table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            font-size: 14px;
            table-layout: fixed;
        }

        .product-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            text-align: left;
            white-space: normal;
        }

        .product-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            white-space: normal;
        }

        .product-table td.number {
            text-align: right;
            font-family: monospace;
        }

        /* Adjust the width of the "No" column */
        .col-no {
            width: 5% !important;
        }

        .product,
        .col-category,
        .col-selling-price,
        .col-sku,
        .col-group-price {
            text-align: left;
            white-space: normal;
            width: auto;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-header h2 {
            font-size: 18px;
            margin: 5px 0;
        }

        .table-responsive {
            overflow-x: auto;
            margin: 20px;
        }

        /* Hide print-only elements during normal view */
        .print-only-logo,
        .company-name {
            display: none;
        }
    </style>
    <style>
        /* Filter Container */
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

        /* Filter Form */
        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0;
        }

        .filter-select {
            width: 200px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            background-color: #fff;
            transition: border-color 0.3s ease;
        }

        .filter-select:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Button Group */
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

        /* Row Count Selector */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select {
                width: 100%;
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

    {{-- Logo and Company Name (hidden during normal view, shown when printing) --}}
    @if (!empty($business->logo))
        <div class="print-only-logo">
            <img src="{{ asset('/uploads/business_logos/' . $business->logo) }}" alt="{{ $business->name }}"
                style="max-height: 100px;">
        </div>
    @endif


    <div class="report-header">
        <div>
            <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
                តារាងតម្លៃតាមក្រុមថ្លៃ
            </h2>
        </div>
    </div>

    {{-- Group Price Selling Filter --}}
    <div id="filter-container" class="filter-container">
        <form method="GET" action="{{ url()->current() }}" class="filter-form" id="groupPriceForm">
            <label for="group_price_filter" class="filter-label">Select Group Price:</label>
            <select name="group_price" id="group_price_filter" class="form-control filter-select"
                onchange="this.form.submit()">
                @foreach ($group_prices as $group_price)
                    <option value="{{ $group_price->id }}"
                        {{ request('group_price') == $group_price->id ? 'selected' : '' }}>
                        {{ $group_price->name }}
                    </option>
                @endforeach
            </select>
        </form>
        <div class="button-group">
            <button id="printButton" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
            <button id="exportButton" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </button>
            <button id="exportExcelButton" class="btn btn-info">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            </button>
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
                                <input class="form-check-input category-checkbox" type="checkbox"
                                    value="{{ $id }}" id="category{{ $id }}" checked>
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
    </div>

    {{-- Main Data Table --}}
    <div class="table-responsive">
        <table class="product-table" id="productgroupprice">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-product">Product Name</th>
                    <th class="col-category">Category Name</th>
                    <th class="col-sku">SKU</th>
                    @if (request('group_price'))
                        <th class="col-group-price">{{ $selected_group_price->name }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                    <tr data-category-id="{{ $product->category_id }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->category_name }}</td>
                        <td>{{ $product->sku }}</td>
                        @if (request('group_price'))
                            <td class="number" style="text-align: center !important;">
                                {{ number_format($product->group_price_value, 2) }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ request('group_price') ? 5 : 4 }}" class="text-center">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print functionality
          
            // Export to PDF functionality
            document.getElementById('exportButton')?.addEventListener('click', async function() {
                try {
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
                            orientation: 'landscape'
                        }
                    };

                    await html2pdf().set(opt).from(element).save();
                } catch (error) {
                    console.error('Export error:', error);
                    alert('Error exporting to PDF: ' + error.message);
                }
            });
        });
    </script>

   
    <script>
        const tablename = "#productgroupprice";
        document.addEventListener('DOMContentLoaded', function() {
            // Print functionality

            // Export to PDF functionality
            document.getElementById('exportButton')?.addEventListener('click', async function() {
                try {
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
                            orientation: 'landscape'
                        }
                    };

                    await html2pdf().set(opt).from(element).save();
                } catch (error) {
                    console.error('Export error:', error);
                    alert('Error exporting to PDF: ' + error.message);
                }
            });

            // Export to Excel functionality
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

            // Hide selected rows functionality
            document.getElementById('hideRowsButton')?.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.row-checkbox:checked');
                checkboxes.forEach(function(checkbox) {
                    checkbox.closest('tr').style.display = 'none';
                });
            });

            // Control the number of rows displayed
            document.getElementById('rowCountSelector')?.addEventListener('change', function() {
                const rowCount = this.value;
                const rows = document.querySelectorAll('.product-table tbody tr');
                rows.forEach(function(row, index) {
                    if (rowCount === 'all' || index < rowCount) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Apply category filter
            document.getElementById('applyCategoryFilter')?.addEventListener('click', function() {
                const selectedCategories = Array.from(document.querySelectorAll(
                    '.category-checkbox:checked')).map(checkbox => checkbox.value);
                const rows = document.querySelectorAll('.product-table tbody tr');

                rows.forEach(row => {
                    const categoryId = row.getAttribute('data-category-id');
                    if (selectedCategories.includes(categoryId)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                $('#categoryModal').modal('hide');
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Select All Categories
            document.getElementById('selectAllCategories')?.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.category-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            });

            // Unselect All Categories
            document.getElementById('unselectAllCategories')?.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.category-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            });

            // Apply Category Filter
            document.getElementById('applyCategoryFilter')?.addEventListener('click', function() {
                const selectedCategories = Array.from(document.querySelectorAll(
                    '.category-checkbox:checked')).map(checkbox => checkbox.value);
                const rows = document.querySelectorAll('.product-table tbody tr');

                rows.forEach(row => {
                    const categoryId = row.getAttribute('data-category-id');
                    if (selectedCategories.includes(categoryId)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                $('#categoryModal').modal('hide');
            });
        });
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
