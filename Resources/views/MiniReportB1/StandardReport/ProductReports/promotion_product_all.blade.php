@extends('minireportb1::layouts.master2')
@section('title', 'Promotion Products')
@section('css')
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
            <div class="no-print">
                @component('components.filters', ['title' => __('report.filters'), 'class' => 'no-print'])
                    <form method="GET" action="{{ route('sr_promotion_product_all') }}" class="filter-form">
                        <div class="row">
                            <!-- Month Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Month')</label>
                                    <select name="filter_month" class="form-control">
                                        <option value="">All Months</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ request()->filter_month == $i ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- Year Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Year')</label>
                                    <select name="filter_year" class="form-control">
                                        <option value="">All Years</option>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ request()->filter_year == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Location Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Location')</label>
                                    <select name="location_id" class="form-control">
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

                            <!-- Filter Button -->
                            <div class="col-md-3 d-flex align-items-end" style="margin-top: 20px">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> ok
                                </button>
                            </div>
                        </div>

                        <!-- Print Button -->
                        <div class="text-right mb-3">
                            <button id="printButton" class="btn btn-success">
                                <i class="fas fa-print"></i> @lang('Print Report')
                            </button>
                        </div>
                    </form>

                    <!-- Add Discount Button -->
                    <a href="/discount" style="margin-top: 20px;">
                        <button class="btn btn-primary w-100">
                            <i class="fa fa-plus icon-spacing"></i> @lang('Add Discount')
                        </button>
                    </a>

                    <!-- Toggle Group Price Columns Button -->
                    <button id="toggleGroupPriceColumns" class="btn btn-warning" data-toggle="modal"
                        data-target="#groupPriceColumnsModal">
                        <i class="fas fa-eye"></i> Toggle Group Price Columns
                    </button>
                @endcomponent
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
                        <div class="modal-footer">
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
                        @if (request()->filter_month && request()->filter_year)
                            @php
                                $startDate = \Carbon\Carbon::create(request()->filter_year, request()->filter_month, 1);
                                $endDate = $startDate->copy()->endOfMonth();
                            @endphp
                            Monthly Promotion Report - {{ $startDate->format('F Y') }}
                            <br />
                            <small class="text-muted">Date: {{ $startDate->format('Y-m-d') }} to
                                {{ $endDate->format('Y-m-d') }}</small>
                            <br />
                        @else
                            Promotion Products Report
                        @endif
                    </h1>

                    <!-- Display Location Name -->
                    @if (request()->location_id)
                        @php
                            $selectedLocation = $locations[request()->location_id] ?? null;
                        @endphp
                        @if ($selectedLocation)
                            <div class="location-name">
                                <p style="font-size: 12px;">Location: {{ $selectedLocation }}</p>
                            </div>
                        @endif
                    @endif
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
                                    <th class="col-price">{{ $group_price->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $rowNumber = 1; @endphp
                            @foreach ($formatted_discounts as $promotion)
                                @foreach ($promotion['products'] as $product)
                                    <tr>
                                        <td class="col-no">{{ $rowNumber++ }}</td>
                                        <td class="col-image">
                                            @if (!empty($product['product_image']))
                                                <img src="{{ $product['product_image'] }}"
                                                    alt="{{ $product['product_name'] }}" class="img-thumbnail"
                                                    style="max-width: 60px; height: 60px; object-fit: contain;">
                                            @else
                                                <div class="no-image-placeholder">
                                                    <i class="fas fa-image fa-lg text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="col-product">{{ $product['product_name'] }}</td>
                                        {{-- <td class="col-price number">{{ number_format($product['price_before'], 2, '.', '') }}</td> --}}
                                        <td class="col-price number">
                                            @if (strpos($product['discount_amount'], '%') !== false)
                                                @php
                                                    $percentageValue = (float) str_replace(
                                                        '%',
                                                        '',
                                                        $product['discount_amount'],
                                                    );
                                                    $formattedPercentage =
                                                        number_format($percentageValue, 2, '.', '') . '%';
                                                @endphp
                                                {{ $formattedPercentage }}
                                            @else
                                                {{ number_format((float) $product['discount_amount'], 2, '.', '') }}
                                            @endif
                                        </td>
                                        {{-- <td class="col-price number">{{ number_format($product['price_after'], 2, '.', '') }}</td> --}}
                                        <td class="col-end-date">{{ $promotion['end_date'] }}</td>
                                        @foreach ($group_prices as $group_price)
                                            <td class="col-price number price-column-{{ $group_price->id }}">
                                                @if (isset($product['group_prices'][$group_price->id]))
                                                    @if (is_array($product['group_prices'][$group_price->id]))
                                                        <span class="discounted-price">{{ $product['group_prices'][$group_price->id]['discounted'] }}</span>
                                                        <span class="original-price">({{ $product['group_prices'][$group_price->id]['original'] }})</span>
                                                    @else
                                                        <span class="discounted-price">{{ $product['group_prices'][$group_price->id] }}</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print functionality
            document.getElementById('printButton').addEventListener('click', function() {
                window.print();
            });

            // Date filter validation
            const filterForm = document.querySelector('.filter-form');
            filterForm.addEventListener('submit', function(e) {
                const month = document.querySelector('[name="filter_month"]').value;
                const year = document.querySelector('[name="filter_year"]').value;

                if ((month && !year) || (!month && year)) {
                    e.preventDefault();
                    alert('Please select both month and year or clear both fields');
                }
            });

            // Add this inside your initializeGroupPriceColumns function:
            function initializeGroupPriceColumns() {
                console.log('Initializing columns...');
                const form = document.getElementById('toggleColumnsForm');
                if (!form) {
                    console.error('Toggle form not found');
                    return;
                }

                const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    const columnId = checkbox.value;
                    const columnClass = `price-column-${columnId}`;
                    const columns = document.getElementsByClassName(columnClass);

                    // Debug output
                    console.log(`Processing column ${columnId}`);
                    console.log(`Found ${columns.length} columns with class ${columnClass}`);

                    // Restore saved state or default to checked
                    const savedState = localStorage.getItem(`column_${columnId}`);
                    checkbox.checked = savedState === null ? true : savedState === 'true';

                    console.log(`Column ${columnId} state: ${checkbox.checked}`);

                    Array.from(columns).forEach((column, index) => {
                        // Debug output for each column
                        const originalPrice = column.querySelector('.original-price');
                        console.log(`Column ${columnId} item ${index}:`);
                        console.log('- Original price element:', originalPrice);
                        console.log('- Full HTML:', column.innerHTML);

                        if (checkbox.checked) {
                            column.style.display = '';
                            column.classList.remove('price-column-hidden');
                        } else {
                            column.style.display = 'none';
                            column.classList.add('price-column-hidden');
                        }
                    });
                });
            }

            // Toggle Group Price Columns
            document.getElementById('toggleGroupPriceColumns')?.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default behavior
                e.stopPropagation(); // Prevent event propagation
                $('#groupPriceColumnsModal').modal('show');
            });

            // Apply column toggling
            document.getElementById('applyColumnToggle')?.addEventListener('click', function() {
                const form = document.getElementById('toggleColumnsForm');
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');

                checkboxes.forEach(checkbox => {
                    const groupPriceName = checkbox.nextElementSibling.textContent
                .trim(); // Get the group price name
                    const columnIndex = Array.from(document.querySelectorAll('.product-table th'))
                        .findIndex(th => th.textContent.trim() === groupPriceName);

                    if (columnIndex !== -1) {
                        const rows = document.querySelectorAll('.product-table tbody tr');
                        rows.forEach(row => {
                            const cell = row.querySelectorAll('td')[
                            columnIndex]; // Target the correct column
                            if (cell) {
                                cell.style.display = checkbox.checked ? '' : 'none';
                            }
                        });

                        const headerCell = document.querySelectorAll('.product-table th')[
                            columnIndex];
                        if (headerCell) {
                            headerCell.style.display = checkbox.checked ? '' : 'none';
                        }
                    }
                });

                $('#groupPriceColumnsModal').modal('hide');
            });
        });
    </script>
@endsection
