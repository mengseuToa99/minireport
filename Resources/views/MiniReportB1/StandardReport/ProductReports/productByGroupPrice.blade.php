@extends('layouts.app')

@section('title', 'Products by Group Price')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">
    
    <style>
        body,
        table {
            font-family: 'Hanuman', serif;
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

        /* Filter Container */
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px; /* Increased gap for breathing room */
            padding: 20px; /* Slightly more padding */
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%); /* Subtle gradient */
            border-radius: 12px; /* Softer corners */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); /* Lighter, modern shadow */
            margin-bottom: 24px; /* Adjusted spacing */
            border: 1px solid #e2e8f0; /* Thin border for definition */
        }

        /* Filter Form */
        .filter-form {
            display: flex;
            align-items: center;
            gap: 12px; /* Slightly larger gap */
            background-color: #fff; /* White background for contrast */
            padding: 8px 12px; /* Inner padding */
            border-radius: 8px; /* Rounded edges */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05); /* Subtle lift */
        }

        .filter-label {
            font-weight: 600; /* Bolder for emphasis */
            color: #2d3748; /* Darker, modern gray */
            margin-bottom: 0;
            font-size: 14px; /* Consistent sizing */
            text-transform: uppercase; /* Adds a touch of formality */
            letter-spacing: 0.5px; /* Slight spacing for readability */
        }

        .filter-select {
            width: 220px; /* Slightly wider */
            padding: 10px 12px; /* More comfortable padding */
            border-radius: 6px; /* Softer corners */
            border: 1px solid #cbd5e0; /* Lighter border */
            background-color: #fff;
            font-size: 14px; /* Match label size */
            color: #4a5568; /* Softer text color */
            transition: all 0.3s ease; /* Smooth transitions for all changes */
            appearance: none; /* Remove default dropdown arrow */
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%234a5568" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>'); /* Custom arrow */
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
        }

        .filter-select:hover {
            border-color: #a0aec0; /* Subtle hover effect */
        }

        .filter-select:focus {
            border-color: #63b3ed; /* Brighter blue for focus */
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 179, 237, 0.3); /* Softer glow */
        }

        /* Button Group */
        .button-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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


            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>

@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (!empty($business->logo))
        <div class="print-only-logo">
            <img src="{{ asset('/uploads/business_logos/' . $business->logo) }}" alt="{{ $business->name }}"
                style="max-height: 100px;">
        </div>
    @endif

    <div id="filter-container">
        <div style="margin: 16px;">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="button-group ">
                    <form method="GET" action="{{ url()->current() }}"  id="groupPriceForm">
                        <label for="group_price_filter" class="filter-label">Select Group Price:</label>
                        <select name="group_price btn" id="group_price_filter" class="form-control filter-select"
                            onchange="this.form.submit()">
                            @foreach ($group_prices as $group_price)
                                <option value="{{ $group_price->id }}"
                                    {{ request('group_price') == $group_price->id ? 'selected' : '' }}>
                                    {{ $group_price->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <button id="selectCategoryButton" class="btn" data-toggle="modal"
                        data-target="#categoryModal" style="margin-top: 16px">
                        <i class="fas fa-filter"></i> Select Categories
                    </button>
                </div>
                @include('minireportb1::MiniReportB1.components.filter', ['hideDateFilter' => true])
            @endcomponent
        </div>

        <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Select Categories to Display</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
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

    <div class="report-header">
        <div>
            <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
                @lang('minireportb1::lang.Price_list_by_price_group')
            </h2>
        </div>
    </div>

    <div class="reusable-table-container">
        <table class="reusable-table" id="productgroupprice">
            <thead>
                <tr>
                    <th class="col-xs">No</th>
                    <th class="col-md">Product Name</th>
                    <th class="col-md">Category Name</th>
                    <th class="col-sm">SKU</th>
                    @if (request('group_price'))
                        <th class="col-md">{{ $selected_group_price->name }}</th>
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
    <script>
        const tablename = "#productgroupprice";

        document.addEventListener('DOMContentLoaded', function() {
            // Select All Categories
            document.getElementById('selectAllCategories')?.addEventListener('click', function() {
                document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
            });

            // Unselect All Categories
            document.getElementById('unselectAllCategories')?.addEventListener('click', function() {
                document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
            });

            // Apply Category Filter
            document.getElementById('applyCategoryFilter')?.addEventListener('click', function() {
                const selectedCategories = Array.from(document.querySelectorAll(
                    '.category-checkbox:checked')).map(
                    checkbox => checkbox.value
                );
                const rows = document.querySelectorAll('#productgroupprice tbody tr');

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
@endsection