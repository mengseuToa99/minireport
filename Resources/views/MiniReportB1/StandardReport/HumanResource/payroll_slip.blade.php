@extends('layouts.app')
@section('title', 'សាច់ប្រាក់បៀវត្សប្រចាំខែ (Salary Slip)')

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
                                {!! Form::label('employee_id', __('minireportb1::minireportb1.employee') . ':') !!}
                                {!! Form::select('employee_id', $employees->pluck('full_name', 'id'), $selected_employee, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('report_date', __('minireportb1::minireportb1.payroll_month') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('report_date', $report_date, ['class' => 'form-control', 'readonly', 'id' => 'report_date']) !!}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 text-right" style="margin-top: 25px;">
                            <button type="button" id="generate_slip" class="btn btn-primary">{{ __('report.generate') }}</button>
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
                        <span>ការបង់ប្រាក់ខែកម្មករ</span>
                    </div>
                </div>
                <div>
                    <div class="receipt-info">
                        <div class="bilingual">
                            <span>Exchange Rate</span>
                        </div>
                        <div id="display-exchange-rate">{{ $exchange_rate }}</div>
                    </div>
                </div>
            </div>

            <table class="receipt-details">
                <tr>
                    <td>
                        <div class="bilingual">
                            <span>Employee No</span>
                        </div>
                    </td>
                    <td id="employee-number"></td>
                </tr>
                <tr>
                    <td>
                        <div class="bilingual">
                            <span>Employee Name</span>
                            <span class="khmer">ឈ្មោះបុគ្គលិក</span>
                        </div>
                    </td>
                    <td id="employee-name"></td>
                </tr>
                <tr>
                    <td>
                        <div class="bilingual">
                            <span>Position</span>
                            <span class="khmer">តំណែង</span>
                        </div>
                    </td>
                    <td id="employee-position"></td>
                </tr>
                <tr>
                    <td>
                        <div class="bilingual">
                            <span>Grand Total</span>
                            <span class="khmer">ប្រាក់ឈ្នួលសរុប</span>
                        </div>
                    </td>
                    <td id="salary-amount"></td>
                </tr>
                <tr>
                    <td>
                        <div class="bilingual">
                            <span>Taxable Salary</span>
                            <span class="khmer">ប្រាក់ឈ្នួលជាប់ពន្ធ</span>
                        </div>
                    </td>
                    <td id="taxable-salary"></td>
                </tr>
                <tr>
                    <td>
                        <div class="bilingual">
                            <span>Tax</span>
                            <span class="khmer">ពន្ធប្រាក់ឈ្នួល</span>
                        </div>
                    </td>
                    <td id="tax-amount"></td>
                </tr>
                <tr class="total-row">
                    <td>
                        <div class="bilingual">
                            <span>Net Salary</span>
                        </div>
                    </td>
                    <td id="net-salary"></td>
                </tr>
            </table>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="bilingual">
                            <span>Employee by: ________________</span>
                            <span class="khmer">ហត្ថលេខាបុគ្គលិក</span>
                        </div>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="bilingual">
                            <span>HR/Director by: ________________</span>
                            <span class="khmer">ហត្ថលេខានាយកធនធានមនុស្ស</span>
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
        .receipt-details tr {
            border-bottom: 1px solid #eee;
        }
        .receipt-details tr:last-child {
            border-bottom: none;
        }
        .receipt-details td {
            padding: 12px 8px;
        }
        .receipt-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .receipt-details td:nth-child(2) {
            width: 60%;
        }
        .bilingual {
            display: flex;
            flex-direction: column;
        }
        .khmer {
            font-size: 14px;
            color: #666;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
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
        .ok-button {
            text-align: right;
            margin-top: 15px;
        }
        .button {
            padding: 8px 25px;
            background-color: #f26522;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
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
            // Initialize date picker (month picker)
            $('#report_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                minViewMode: 'months',
                startView: 'months'
            });
            
            // Initialize select2
            $('.select2').select2();
            
            // Load employee details when selected
            $('#employee_id').on('change', function() {
                updateSalarySlip();
            });
            
            // Generate salary slip
            $('#generate_slip').on('click', function() {
                updateSalarySlip();
            });
            
            // Initial update if employee is selected
            if ($('#employee_id').val()) {
                updateSalarySlip();
            }
            
            // Function to update salary slip with employee details
            function updateSalarySlip() {
                const employeeId = $('#employee_id').val();
                if (!employeeId) return;
                
                // Fetch employee data using AJAX
                $.ajax({
                    url: "{{ route('sr_payroll_slip') }}",
                    type: "GET",
                    data: {
                        employee_id: employeeId,
                        report_date: $('#report_date').val()
                    },
                    dataType: "json",
                    success: function(response) {
                        // Update employee details
                        $('#employee-name').text(response.employee.full_name);
                        $('#employee-number').text(response.employee.employee_id || response.employee.id);
                        $('#employee-position').text(response.employee.position || 'N/A');
                        
                        // Update salary information
                        const salary = parseFloat(response.salary_amount) || 0;
                        const tax = parseFloat(response.tax_amount) || 0;
                        const netSalary = salary - tax;
                        
                        $('#display-exchange-rate').text(formatNumber(response.exchange_rate));
                        $('#salary-amount').text(formatDecimal(salary));
                        $('#taxable-salary').text(formatDecimal(salary));
                        $('#tax-amount').text(tax > 0 ? formatDecimal(tax) : '-');
                        $('#net-salary').text(formatDecimal(netSalary));
                        
                        // Show a warning if no salary data was found
                        if (salary === 0) {
                            toastr.warning('No salary data found for this employee in the selected month.', 'Warning', {timeOut: 5000});
                            
                            // Debug info for admin users
                            if (response.debug_info) {
                                console.log('Salary debug info:', response.debug_info);
                                
                                // Add a small debug indicator in the UI for admins
                                let debugMsg = 'No salary data. Method: ' + response.debug_info.method;
                                $('<div class="debug-info" style="font-size: 10px; color: #999; margin-top: 5px;">' + debugMsg + '</div>')
                                    .insertAfter('#salary-amount');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching employee data:", error);
                        toastr.error("Error loading employee data. Please try again.");
                    }
                });
            }
            
            // Helper function to format numbers with commas
            function formatNumber(num) {
                return Number(num).toLocaleString();
            }
            
            // Helper function to format decimal numbers
            function formatDecimal(num) {
                return Number(num).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        });

        // Override the print button click handler
        document.addEventListener('DOMContentLoaded', function() {
            const printButton = document.getElementById('print-button');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Create a new window for printing
                    const printWindow = window.open('', '_blank');
                    const content = document.getElementById('report-container').innerHTML;
                    
                    // Write the print window content
                    printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Salary Slip</title>
                            <style>
                                @page {
                                    size: auto;
                                    margin: 1cm;
                                }
                                body {
                                    font-family: 'Khmer OS', Arial, Helvetica, sans-serif;
                                    margin: 0;
                                    padding: 0;
                                    color: #333;
                                    -webkit-print-color-adjust: exact !important;
                                    print-color-adjust: exact !important;
                                }
                                .container {
                                    max-width: none;
                                    width: 100%;
                                    margin: 0;
                                    padding: 15px;
                                    box-shadow: none;
                                    border: none;
                                }
                            </style>
                        </head>
                        <body>
                            ${content}
                        </body>
                        </html>
                    `);
                    
                    printWindow.document.close();
                    
                    // Wait for content to load before printing
                    printWindow.onload = function() {
                        printWindow.print();
                    };
                });
            }
        });
    </script>
@endsection 