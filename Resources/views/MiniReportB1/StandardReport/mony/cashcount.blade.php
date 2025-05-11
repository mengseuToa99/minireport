@extends('layouts.app')
@section('title', 'Cash Count Report')

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('date', __('report.date') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('date', $date, ['class' => 'form-control', 'readonly', 'id' => 'date']) !!}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('location_id', __('business.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations, $location_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 text-right" style="margin-top: 25px;">
                            <button type="button" id="generate_report" class="btn btn-primary">{{ __('report.generate') }}</button>
                        </div>
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    <div id="report-container">
        <div class="container">
            <div class="header">
                <div>
                    @if($business->logo)
                        <img src="{{ asset('/uploads/business_logos/' . $business->logo) }}" alt="{{ $business->name }}" class="logo">
                    @else
                        <img src="/api/placeholder/120/80" alt="Company Logo" class="logo">
                    @endif
                </div>
                <div>
                    <div class="bilingual">
                        <span>{{ $business->name }}</span>
                        <span>Cash Count Report</span>
                    </div>
                </div>
                <div>
                    <div class="receipt-info">
                        <div class="bilingual">
                            <span>Date</span>
                        </div>
                        <div id="display-date">{{ $date }}</div>
                    </div>
                </div>
            </div>

            <table class="receipt-details">
                <thead>
                    <tr>
                        <th>Denomination</th>
                        <th>Count</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cash_count as $denomination => $count)
                    <tr>
                        <td>{{ $denomination }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ number_format($denomination * $count, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2">Total</td>
                        <td>{{ number_format($total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="bilingual">
                            <span>Counted by: ________________</span>
                        </div>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="bilingual">
                            <span>Verified by: ________________</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Khmer OS', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f26522;
            padding-bottom: 15px;
        }
        .logo {
            width: 120px;
            height: auto;
        }
        .receipt-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-subtitle {
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .receipt-details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .receipt-details th,
        .receipt-details td {
            padding: 12px 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .receipt-details th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .receipt-details tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .bilingual {
            display: flex;
            flex-direction: column;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa !important;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }

        /* Print styles */
        @media print {
            @page {
                size: auto;
                margin: 1cm;
            }

            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .container {
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 15px;
                box-shadow: none;
                border: none;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize date picker
            $('#date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            
            // Initialize select2
            $('.select2').select2();
            
            // Generate report
            $('#generate_report').on('click', function() {
                updateReport();
            });
            
            // Initial update if date is selected
            if ($('#date').val()) {
                updateReport();
            }
            
            // Function to update report
            function updateReport() {
                const date = $('#date').val();
                const locationId = $('#location_id').val();
                
                if (!date) {
                    toastr.error('Please select a date');
                    return;
                }
                
                // Fetch report data using AJAX
                $.ajax({
                    url: "{{ route('sr_cash_count') }}",
                    type: "GET",
                    data: {
                        date: date,
                        location_id: locationId
                    },
                    dataType: "json",
                    success: function(response) {
                        // Update report content
                        $('#display-date').text(response.date);
                        
                        // Update table content
                        let tableHtml = `
                            <thead>
                                <tr>
                                    <th>Denomination</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;
                        
                        response.cash_count.forEach(function(item) {
                            tableHtml += `
                                <tr>
                                    <td>${item.denomination}</td>
                                    <td>${item.count}</td>
                                    <td>${formatNumber(item.amount)}</td>
                                </tr>
                            `;
                        });
                        
                        tableHtml += `
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="2">Total</td>
                                    <td>${formatNumber(response.total_amount)}</td>
                                </tr>
                            </tfoot>
                        `;
                        
                        $('.receipt-details').html(tableHtml);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching report data:", error);
                        toastr.error("Error loading report data. Please try again.");
                    }
                });
            }
            
            // Helper function to format numbers
            function formatNumber(num) {
                return Number(num).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        });
    </script>
@endsection 