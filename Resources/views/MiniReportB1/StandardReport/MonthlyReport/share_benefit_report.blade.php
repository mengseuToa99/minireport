@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        .two-tables-container {
            display: flex;
            justify-content: space-between;
            margin: 0 2%;
            gap: 2%;
        }
        .table-section {
            width: 48%;
        }
        .table-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 1%;
            background-color: #f5f5f5;
            padding: 8px;
        }
        .col-number {
            width: 5%;
        }
        .col-package {
            width: 30%;
        }
        .col-cost {
            width: 10%;
        }
        .col-quantity {
            width: 10%;
        }
        .col-total {
            width: 30%;
        }
        .col-empty {
            width: 15%;
        }
        .total-row {
            text-align: right;
            padding: 1% 0;
            font-weight: bold;
            margin-top: -1%;
            margin-left: -1%;
        }
        .signature-row {
            display: flex;
            justify-content: space-evenly; /* Changed from flex-end to space-evenly */
            align-items: center; /* Added to vertically center items */
            padding: 1% 0;
            font-weight: bold;
            margin-top: -2%;
            margin-right: 3%;
        }
    </style>
@endsection

@section('content')

    <div style="margin: 2%">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                @include('minireportb1::MiniReportB1.components.filter')
            </div>
        @endcomponent
    </div>

    <div class="report-header" id="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-light tw-text-center normal-view-title" style="font-size: 20px;">
            {{ $business_name }}
        </h2>
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            របាយការណ៍ភាគលាភ
        </h2>
        {{-- <h3 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 18px;">
            សាខាថ្មី សម្រាប់ខែ{{ $month }} ចាប់ពីថ្ងៃទី {{ $start_day }} ដល់ថ្ងៃទី {{ $end_day }}
        </h3> --}}
    </div>

    <?php
    $packages = [
        [
            'name' => 'កញ្ចប់1000$',
            'cost_manager' => 10,
            'cost_assistant' => 20,
            'quantity_manager' => 4,
            'quantity_assistant' => 4
        ],
        [
            'name' => 'កញ្ចប់2999$',
            'cost_manager' => 10,
            'cost_assistant' => 20,
            'quantity_manager' => 3,
            'quantity_assistant' => 3
        ],
        [
            'name' => 'កញ្ចប់3999$',
            'cost_manager' => 10,
            'cost_assistant' => 20,
            'quantity_manager' => 2,
            'quantity_assistant' => 2
        ],
        [
            'name' => 'កញ្ចប់6900$',
            'cost_manager' => 20,
            'cost_assistant' => 40,
            'quantity_manager' => 1,
            'quantity_assistant' => 1
        ],
        [
            'name' => 'រៀនបោកអ៊ុត',
            'cost_manager' => 5,
            'cost_assistant' => 5,
            'quantity_manager' => 6,
            'quantity_assistant' => 6
        ]
    ];

    // Calculate totals
    $manager_total = array_sum(array_map(function($package) {
        return $package['cost_manager'] * $package['quantity_manager'];
    }, $packages));

    $assistant_total = array_sum(array_map(function($package) {
        return $package['cost_assistant'] * $package['quantity_assistant'];
    }, $packages));
    ?>

    <h3 class="p-4 bg-gray-100 tw-font-semibold normal-view-title" style="font-size: 18px; margin-left: 2%;">
        ការលើកទឹកចិត្តផ្នែកហ្វេនឆាយ
    </h3>
    
    <div class="two-tables-container">
        <!-- Left Table (Manager Franchise) -->
        <div class="table-section">
            <div class="text-left" style="padding: 0; margin-left: 3%; margin-top: -1.5%; margin-bottom: -1.5%;">
                ប្រធានហ្វ្រេនឆាយ (Manager Franchise)
            </div>
            <div class="reusable-table-container">
                <table class="reusable-table">
                    <tbody>
                        @foreach ($packages as $index => $package)
                            <tr class="table-row text-center">
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-package">{{ $package['name'] }}</td>
                                <td class="col-cost">{{ $package['cost_manager'] }}$</td>
                                <td class="col-quantity">{{ $package['quantity_manager'] }}</td>
                                <td class="col-total">{{ $package['cost_manager'] * $package['quantity_manager'] }}$</td>
                                <td class="col-empty"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="total-row" style="margin-top: -2%; margin-right: 3%;">
                សរុបរួម: {{ $manager_total }}$
            </div>
        </div>
    
        <!-- Right Table (Assistant Franchise) -->
        <div class="table-section">
            <div class="text-left" style="padding: 0; margin-left: 3%; margin-top: -1.5%; margin-bottom: -1.5%;">
                ជំនួយការហ្វ្រេនឆាយ (Assistant Franchise)
            </div>
            <div class="reusable-table-container">
                <table class="reusable-table">
                    <tbody>
                        @foreach ($packages as $index => $package)
                            <tr class="table-row text-center">
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-package">{{ $package['name'] }}</td>
                                <td class="col-cost">{{ $package['cost_assistant'] }}$</td>
                                <td class="col-quantity">{{ $package['quantity_assistant'] }}</td>
                                <td class="col-total">{{ $package['cost_assistant'] * $package['quantity_assistant'] }}$</td>
                                <td class="col-empty"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="total-row" style="margin-top: -2%; margin-right: 3%;">
                សរុបរួម: {{ $assistant_total }}$
            </div>
        </div>
    </div>

    <!-- New Two Tables -->
    <div class="two-tables-container">
        <!-- Left Table (Team Leader) -->
        <div class="table-section">
            <div class="text-left" style="padding: 0; margin-left: 3%; margin-top: -1.5%; margin-bottom: -1.5%;">
                ប្រធានក្រុមហ្វ្រេនឆាយ
            </div>
            <div class="reusable-table-container">
                <table class="reusable-table">
                    <tbody>
                        @foreach ($packages as $index => $package)
                            <tr class="table-row text-center">
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-package">{{ $package['name'] }}</td>
                                <td class="col-cost">{{ $package['cost_manager'] }}$</td>
                                <td class="col-quantity">{{ $package['quantity_manager'] }}</td>
                                <td class="col-total">{{ $package['cost_manager'] * $package['quantity_manager'] }}$</td>
                                <td class="col-empty"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="total-row" style="margin-top: -2%; margin-right: 3%;">
                សរុបរួម: {{ $manager_total }}$
            </div>
        </div>

        <!-- Right Table (Total Received) -->
        <div class="table-section" style="margin-top: -1.2%;">
            <div class="text-center" style="padding: 0; margin-left: 3%; margin-top: -1.5%; margin-bottom: -1.5%; font-size: 1.5rem;">
                សរុបរួមដែលទទួលបាន
            </div>
            <div class="reusable-table-container">
                <table class="reusable-table">
                    <tbody>
                        @foreach ($packages as $index => $package)
                            <tr class="table-row text-center">
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-package">{{ $package['name'] }}</td>
                                <td class="col-cost">{{ $package['cost_assistant'] }}$</td>
                                <td class="col-quantity">{{ $package['quantity_assistant'] }}</td>
                                <td class="col-total">{{ $package['cost_assistant'] * $package['quantity_assistant'] }}$</td>
                                <td class="col-empty"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="signature-row">
                <span>ហត្ថលេខាអ្នករៀបចំ</span>
                <span>ហត្ថលេខាប្រធានហ្វ្រេនឆាយ</span>
            </div>
        </div>
    </div>

    <script>
        const tablename = "#shareBenefit";
        const reportname = "ShareBenefit";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
