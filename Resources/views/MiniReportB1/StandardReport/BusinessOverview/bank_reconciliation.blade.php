@extends('layouts.app')
@section('title', 'ផ្ទៀងផ្ទាត់សាច់ប្រាក់​ធនាគារ (Bank Reconciliation)')

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
                                {!! Form::label('account_id', __('account.account') . ' (គណនី):') !!}
                                {!! Form::select('account_id', $accounts->pluck('name', 'id'), $selected_account, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('report_date', __('report.date_range') . ':') !!}
                                <div class="input-group">
                                    {!! Form::text('report_date', $report_date, ['class' => 'form-control', 'readonly', 'id' => 'report_date']) !!}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('bank_statement_balance', __('account.bank_statement_balance') . ':') !!}
                                {!! Form::number('bank_statement_balance', $bank_statement_balance, ['class' => 'form-control', 'step' => '0.01', 'id' => 'bank_statement_balance']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="generate_report" class="btn btn-primary">{{ __('report.generate') }}</button>
                        </div>
                    </div>
                </form>
                @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    <div class="report-container">
        <div class="text-center report-header">
            <h2>{{ $business->name }}</h2>
            <h3>ផ្ទៀងផ្ទាត់សាច់ប្រាក់​ធនាគារ Monthly Bank Reconciliation</h3>
            <h4>AS AT {{ $formatted_date }}</h4>
        </div>
        
        <div class="report-content" id="report-content">
            <!-- Bank Account Info -->
            <div class="account-info">
                <table class="table-data">
                    <tr>
                        <td>Bank Account No.:</td>
                        <td><span id="account_number"></span></td>
                    </tr>
                    <tr>
                        <td>Account Name:</td>
                        <td><span id="account_name"></span></td>
                    </tr>
                    <tr>
                        <td>Name of the Bank:</td>
                        <td><span id="bank_name"></span></td>
                    </tr>
                </table>
            </div>
            
            <!-- Bank Reconciliation Data -->
            <div class="reconciliation-data">
                <table class="table-data">
                    <tr>
                        <td>Balance per bank statement</td>
                        <td class="currency">USD</td>
                        <td class="text-right" id="statement_balance">{{ number_format($bank_statement_balance, 2) }}</td>
                    </tr>
                </table>
                
                <p><strong>Less:</strong></p>
                
                <table class="table-bordered table-striped" id="outstanding-checks-table">
                    <thead>
                        <tr>
                            <th>Check #</th>
                            <th>Date</th>
                            <th>Check Holder</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="outstanding-checks-body">
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">USD</td>
                            <td class="text-right" id="total-outstanding-checks">-</td>
                        </tr>
                    </tfoot>
                </table>
                
                <p><strong>Plus:</strong></p>
                <table class="table-bordered table-striped" id="deposits-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="deposits-body">
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">USD</td>
                            <td class="text-right" id="total-deposits">-</td>
                        </tr>
                    </tfoot>
                </table>
                
                <table class="table-data total-deduction">
                    <tr>
                        <td>Total Deduction</td>
                        <td class="text-right" id="total-deduction">-</td>
                    </tr>
                </table>
                
                <p><strong>Plus:</strong></p>
                <table class="table-data">
                    <tr>
                        <td>Receipts in book, not on statement (e.g. deposit in transit)</td>
                        <td class="text-right" id="receipts-in-transit">USD</td>
                    </tr>
                </table>
                
                <table class="table-data adjusted-bank-balance">
                    <tr>
                        <td>Adjusted bank balance</td>
                        <td class="text-right" id="adjusted-bank-balance">USD</td>
                    </tr>
                </table>
                
                <table class="table-data">
                    <tr>
                        <td>Balance per book</td>
                        <td class="text-right" id="balance-per-book">USD</td>
                    </tr>
                </table>
                
                <p><strong>Less:</strong></p>
                <table class="table-bordered table-striped" id="payments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="payments-body">
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">USD</td>
                            <td class="text-right" id="total-payments">-</td>
                        </tr>
                    </tfoot>
                </table>
                
                <p><strong>Plus:</strong></p>
                <table class="table-bordered table-striped" id="receipts-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="receipts-body">
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">USD</td>
                            <td class="text-right" id="total-receipts">-</td>
                        </tr>
                    </tfoot>
                </table>
                
                <table class="table-data">
                    <tr>
                        <td>Adjusted book balance</td>
                        <td class="text-right" id="adjusted-book-balance">-</td>
                    </tr>
                </table>
                
                <div class="note">
                    <p><strong>Note:</strong> The difference between statement and book balance <span id="must-be-zero">MUST be zero.</span></p>
                    <p id="difference-value"></p>
                </div>
                
                <div class="signature-section">
                    <table class="signature-table">
                        <tr>
                            <td>Prepared by:</td>
                            <td>Approved by:</td>
                        </tr>
                        <tr>
                            <td>Name: <span>មុន ស្រីភ័ក្រ្ត</span></td>
                            <td>Name: <span>មុន ស្រីភ័ក្រ្ត</span></td>
                        </tr>
                        <tr>
                            <td>Position:</td>
                            <td>Position: <span>មន្ត្រីគណនេយ្យ</span></td>
                        </tr>
                        <tr>
                            <td>Date: <span>{{ $formatted_date }}</span></td>
                            <td>Date: <span>{{ $formatted_date }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .report-container {
            width: 95%;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }
        
        .report-header {
            margin-bottom: 30px;
        }
        
        .report-header h2,
        .report-header h3,
        .report-header h4 {
            margin: 5px 0;
        }
        
        .report-content {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: white;
        }
        
        .account-info {
            margin-bottom: 20px;
        }
        
        .table-data {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .table-data td {
            padding: 5px 10px;
        }
        
        .table-data td:first-child {
            font-weight: bold;
            width: 300px;
        }
        
        .currency {
            width: 80px;
            text-align: center;
        }
        
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-deduction,
        .adjusted-bank-balance {
            font-weight: bold;
        }
        
        .note {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .signature-section {
            margin-top: 50px;
        }
        
        .signature-table {
            width: 100%;
        }
        
        .signature-table td {
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            .report-container {
                width: 100%;
                margin: 0;
            }
            
            .report-content {
                border: none;
                padding: 0;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize date picker
            $('#report_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            
            // Initialize select2
            $('.select2').select2();
            
            // Load account details when an account is selected
            $('#account_id').on('change', function() {
                const accountId = $(this).val();
                if (!accountId) return;
                
                // Find the selected account from the accounts array
                @if(count($accounts) > 0)
                const accounts = {!! json_encode($accounts) !!};
                const selectedAccount = accounts.find(acc => acc.id == accountId);
                
                if (selectedAccount) {
                    $('#account_number').text(selectedAccount.account_number);
                    $('#account_name').text(selectedAccount.name);
                    // Assuming bank name is not directly stored, you might need to adjust this
                    $('#bank_name').text('');
                }
                @endif
            });
            
            // Trigger account details loading if an account is pre-selected
            if ($('#account_id').val()) {
                $('#account_id').trigger('change');
            }
            
            // Handle Generate Report button click
            $('#generate_report').on('click', function() {
                loadBankReconciliationData();
            });
            
            // Function to load bank reconciliation data
            function loadBankReconciliationData() {
                const formData = {
                    account_id: $('#account_id').val(),
                    report_date: $('#report_date').val(),
                    bank_statement_balance: $('#bank_statement_balance').val()
                };
                
                if (!formData.account_id) {
                    alert('Please select an account');
                    return;
                }
                
                // Update the statement balance immediately
                $('#statement_balance').text(parseFloat(formData.bank_statement_balance).toFixed(2));
                
                // In a real implementation, you would load actual data from the server
                // For now, we'll just show sample data
                displaySampleData();
                
                // Calculate totals
                calculateTotals();
            }
            
            // Function to display sample data
            function displaySampleData() {
                // Sample outstanding checks
                const outstandingChecks = [
                    { check_no: '1001', date: '2024-12-25', holder: 'ABC Suppliers', amount: 250.00 },
                    { check_no: '1002', date: '2024-12-27', holder: 'XYZ Services', amount: 380.00 }
                ];
                
                // Sample deposits in transit
                const depositsInTransit = [
                    { date: '2024-12-30', description: 'Customer payment', amount: 520.00 }
                ];
                
                // Sample bank charges
                const bankCharges = [
                    { date: '2024-12-31', description: 'Monthly fee', amount: 15.00 },
                    { date: '2024-12-31', description: 'Transaction fees', amount: 8.00 }
                ];
                
                // Sample interest earned
                const interestEarned = [
                    { date: '2024-12-31', description: 'Interest on deposit', amount: 2.40 }
                ];
                
                // Render outstanding checks
                let checksHtml = '';
                outstandingChecks.forEach(check => {
                    checksHtml += `<tr>
                        <td>${check.check_no}</td>
                        <td>${check.date}</td>
                        <td>${check.holder}</td>
                        <td class="text-right">${check.amount.toFixed(2)}</td>
                    </tr>`;
                });
                $('#outstanding-checks-body').html(checksHtml || '<tr><td colspan="4" class="text-center">No outstanding checks</td></tr>');
                
                // Render deposits in transit
                let depositsHtml = '';
                depositsInTransit.forEach(deposit => {
                    depositsHtml += `<tr>
                        <td>${deposit.date}</td>
                        <td>${deposit.description}</td>
                        <td class="text-right">${deposit.amount.toFixed(2)}</td>
                    </tr>`;
                });
                $('#deposits-body').html(depositsHtml || '<tr><td colspan="3" class="text-center">No deposits in transit</td></tr>');
                
                // Render bank charges
                let chargesHtml = '';
                bankCharges.forEach(charge => {
                    chargesHtml += `<tr>
                        <td>${charge.date}</td>
                        <td>${charge.description}</td>
                        <td class="text-right">${charge.amount.toFixed(2)}</td>
                    </tr>`;
                });
                $('#payments-body').html(chargesHtml || '<tr><td colspan="3" class="text-center">No bank charges</td></tr>');
                
                // Render interest earned
                let interestHtml = '';
                interestEarned.forEach(interest => {
                    interestHtml += `<tr>
                        <td>${interest.date}</td>
                        <td>${interest.description}</td>
                        <td class="text-right">${interest.amount.toFixed(2)}</td>
                    </tr>`;
                });
                $('#receipts-body').html(interestHtml || '<tr><td colspan="3" class="text-center">No interest earned</td></tr>');
            }
            
            // Function to calculate totals
            function calculateTotals() {
                // Get bank statement balance
                const statementBalance = parseFloat($('#bank_statement_balance').val()) || 0;
                
                // Calculate total outstanding checks
                let totalChecks = 0;
                $('#outstanding-checks-body tr').each(function() {
                    const amount = parseFloat($(this).find('td:last').text()) || 0;
                    totalChecks += amount;
                });
                $('#total-outstanding-checks').text(totalChecks.toFixed(2));
                
                // Calculate total deposits in transit
                let totalDeposits = 0;
                $('#deposits-body tr').each(function() {
                    const amount = parseFloat($(this).find('td:last').text()) || 0;
                    totalDeposits += amount;
                });
                $('#total-deposits').text(totalDeposits.toFixed(2));
                
                // Calculate total deduction
                const totalDeduction = totalChecks - totalDeposits;
                $('#total-deduction').text(totalDeduction.toFixed(2));
                
                // Get total in-transit receipts (placeholder for now)
                const receiptsInTransit = 0;
                $('#receipts-in-transit').text(receiptsInTransit.toFixed(2));
                
                // Calculate adjusted bank balance
                const adjustedBankBalance = statementBalance - totalDeduction + receiptsInTransit;
                $('#adjusted-bank-balance').text(adjustedBankBalance.toFixed(2));
                
                // For demonstration, set book balance equal to adjusted bank balance
                const bookBalance = adjustedBankBalance;
                $('#balance-per-book').text(bookBalance.toFixed(2));
                
                // Calculate total bank charges
                let totalCharges = 0;
                $('#payments-body tr').each(function() {
                    const amount = parseFloat($(this).find('td:last').text()) || 0;
                    totalCharges += amount;
                });
                $('#total-payments').text(totalCharges.toFixed(2));
                
                // Calculate total interest earned
                let totalInterest = 0;
                $('#receipts-body tr').each(function() {
                    const amount = parseFloat($(this).find('td:last').text()) || 0;
                    totalInterest += amount;
                });
                $('#total-receipts').text(totalInterest.toFixed(2));
                
                // Calculate adjusted book balance
                const adjustedBookBalance = bookBalance - totalCharges + totalInterest;
                $('#adjusted-book-balance').text(adjustedBookBalance.toFixed(2));
                
                // Calculate and display difference
                const difference = adjustedBankBalance - adjustedBookBalance;
                $('#difference-value').text(difference.toFixed(2));
                
                // Highlight if difference is not zero
                if (Math.abs(difference) > 0.01) {
                    $('#must-be-zero').css('color', 'red');
                    $('#difference-value').css('color', 'red');
                } else {
                    $('#must-be-zero').css('color', 'inherit');
                    $('#difference-value').css('color', 'inherit');
                }
            }
        });
    </script>
@endsection 