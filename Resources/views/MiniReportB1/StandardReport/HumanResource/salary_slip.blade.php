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
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employee_id', __('employee.employee') . ':') !!}
                                {!! Form::select('employee_id', $employees->pluck('full_name', 'id'), $selected_employee, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('report_date', __('report.month') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('report_date', $report_date, ['class' => 'form-control', 'readonly', 'id' => 'report_date']) !!}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('exchange_rate', __('lang_v1.exchange_rate') . ':') !!}
                                {!! Form::number('exchange_rate', $exchange_rate, ['class' => 'form-control', 'step' => '0.01', 'id' => 'exchange_rate']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('salary_amount', __('hr.salary_amount') . ':') !!}
                                {!! Form::number('salary_amount', $salary_amount, ['class' => 'form-control', 'step' => '0.01', 'id' => 'salary_amount']) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('tax_amount', __('hr.tax_amount') . ':') !!}
                                {!! Form::number('tax_amount', $tax_amount, ['class' => 'form-control', 'step' => '0.01', 'id' => 'tax_amount']) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-right" style="margin-top: 25px;">
                            <button type="button" id="generate_slip" class="btn btn-primary">{{ __('report.generate') }}</button>
                        </div>
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    <div id="report-container">
        @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => 'បង្កាន់ដៃបើកប្រាក់ខែ (Salary Slip)'])
        
        <div class="salary-slip-container">
            <div class="salary-slip" id="salary-slip">
                <div class="employee-details">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr>
                                    <td class="label">Employee No:</td>
                                    <td id="employee-number"></td>
                                </tr>
                                <tr>
                                    <td class="label">Employee Name:</td>
                                    <td id="employee-name"></td>
                                </tr>
                                <tr>
                                    <td class="label">Position:</td>
                                    <td id="employee-position"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-right">
                            <table class="info-table" style="margin-left: auto;">
                                <tr>
                                    <td class="label">Date:</td>
                                    <td>{{ date('d-M-Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Exchange Rate:</td>
                                    <td id="display-exchange-rate">{{ $exchange_rate }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="salary-details">
                    <table class="salary-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">N°</th>
                                <th style="width: 45%;">DESCRIPTION បរិយាយ</th>
                                <th style="width: 10%;">បរិមាណ<br>Quantity</th>
                                <th style="width: 20%;">តម្លៃឯកតា<br>Unit Price</th>
                                <th style="width: 20%;">តម្លៃសរុប<br>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>ចំណាយលើប្រាក់ខែ ខែ <span id="month-year">{{ $formatted_date }}</span></td>
                                <td class="text-center">1</td>
                                <td class="text-right">$ <span id="salary-unit-price">{{ number_format($salary_amount, 2) }}</span></td>
                                <td class="text-right">$ <span id="salary-amount">{{ number_format($salary_amount, 2) }}</span></td>
                            </tr>
                            <!-- Empty rows -->
                            @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            @endfor
                            <!-- Totals -->
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right"><strong>សរុប (USD)</strong></td>
                                <td class="text-right">$ <span id="total-usd">{{ number_format($salary_amount, 2) }}</span></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right"><strong>Rate(KHR)</strong></td>
                                <td class="text-right"><span id="rate-khr">{{ number_format($exchange_rate) }}</span></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right"><strong>សរុប (KHR)</strong></td>
                                <td class="text-right"><span id="total-khr">{{ number_format($salary_amount * $exchange_rate) }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="signature-section">
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <div class="signature-box">
                                <p>អ្នកទទួល / Received By</p>
                                <div class="signature-line"></div>
                                <p>ឈ្មោះ / Name: <span id="signature-employee-name"></span></p>
                                <p>កាលបរិច្ឆេទ / Date: ______________</p>
                            </div>
                        </div>
                        <div class="col-xs-4 text-center">
                            <div class="signature-box">
                                <p>រៀបចំដោយ / Prepared By</p>
                                <div class="signature-line"></div>
                                <p>ឈ្មោះ / Name: _______________</p>
                                <p>កាលបរិច្ឆេទ / Date: ______________</p>
                            </div>
                        </div>
                        <div class="col-xs-4 text-center">
                            <div class="signature-box">
                                <p>អនុម័តដោយ / Approved By</p>
                                <div class="signature-line"></div>
                                <p>ឈ្មោះ / Name: _______________</p>
                                <p>កាលបរិច្ឆេទ / Date: ______________</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #report-container {
            background-color: white;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .salary-slip-container {
            width: 100%;
            display: flex;
            justify-content: center;
            font-family: 'Khmer OS', Arial, sans-serif;
            margin-bottom: 20px;
        }
        
        .salary-slip {
            width: 100%;
            max-width: 800px;
            padding: 0;
        }
        
        .employee-details {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 120px;
            color: #333;
            text-align: left;
        }
        
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #000;
        }
        
        .salary-table th, .salary-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        
        .salary-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .signature-section {
            margin-top: 40px;
        }
        
        .signature-box {
            padding: 10px;
        }
        
        .signature-line {
            margin: 30px auto 10px;
            border-top: 1px solid #000;
            width: 80%;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            #report-container {
                padding: 0;
                margin: 0;
            }
            
            .salary-slip-container {
                width: 100%;
                display: block;
            }
            
            .salary-slip {
                width: 100%;
                max-width: none;
                padding: 0;
            }
            
            body {
                margin: 0;
                padding: 0;
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
            
            // Calculate values when any input changes
            $('#salary_amount, #tax_amount, #exchange_rate').on('input', function() {
                updateSalaryCalculations();
            });
            
            // Generate salary slip
            $('#generate_slip').on('click', function() {
                updateSalarySlip();
            });
            
            // Initial update
            if ($('#employee_id').val()) {
                updateSalarySlip();
            } else {
                updateSalaryCalculations();
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
                        report_date: $('#report_date').val(),
                        exchange_rate: $('#exchange_rate').val(),
                        salary_amount: $('#salary_amount').val(),
                        tax_amount: $('#tax_amount').val(),
                    },
                    dataType: "json",
                    success: function(response) {
                        // Update employee details
                        $('#employee-name, #signature-employee-name').text(response.employee.full_name);
                        $('#employee-number').text(response.employee.employee_id || response.employee.id);
                        $('#employee-position').text(response.employee.position || 'ផ្នែក');
                        $('#month-year').text(response.formatted_date);
                        
                        // Update salary calculations
                        updateSalaryCalculations();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching employee data:", error);
                        toastr.error("Error loading employee data. Please try again.");
                    }
                });
            }
            
            // Function to update salary calculations
            function updateSalaryCalculations() {
                const salary = parseFloat($('#salary_amount').val()) || 0;
                const tax = parseFloat($('#tax_amount').val()) || 0;
                const exchangeRate = parseFloat($('#exchange_rate').val()) || 4100;
                const netSalary = salary - tax;
                const salaryKHR = salary * exchangeRate;
                
                // Update exchange rate display
                $('#display-exchange-rate').text(formatNumber(exchangeRate));
                
                // Update salary details
                $('#salary-unit-price, #salary-amount, #total-usd').text(formatDecimal(salary));
                $('#rate-khr').text(formatNumber(exchangeRate));
                $('#total-khr').text(formatNumber(salaryKHR));
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
    </script>
@endsection 