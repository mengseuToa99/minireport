@extends('layouts.app')
@section('title', 'Monthly Bank Reconciliation')

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')

<!-- Main content -->
<section class="content">
@include('minireportb1::MiniReportB1.components.back_to_dashboard_button')

    <div class="row no-print">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                {!! Form::open(['url' => action([\Modules\MiniReportB1\Http\Controllers\StandardReport\HumanResourceController::class, 'bankReconciliationReport']), 'method' => 'get', 'id' => 'filter_form' ]) !!}
                
                <div class="col-md-6">
                    @include('minireportb1::MiniReportB1.components.filterdate')
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('account_id', 'Bank Account:') !!}
                        {!! Form::select('account_id', $accounts->pluck('name', 'id'), request('account_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => 'All Accounts']); !!}
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group mt-25">
                        <button type="submit" class="btn btn-primary pull-right">@lang('report.apply_filters')</button>
                    </div>
                </div>
                
                <div class="col-md-12 text-right">
                    @include('minireportb1::MiniReportB1.components.printbutton')
                </div>
                
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div id="report-container">
                    <div class="reconciliation-form">
                        <!-- Header Section -->
                        <div class="header">
                        <div>
                    @if($business->logo)
                        <img src="{{ asset('/uploads/business_logos/' . $business->logo) }}" alt="{{ $business->name }}" class="logo">
                    @else
                        <img src="/api/placeholder/120/80" alt="Company Logo" class="logo">
                    @endif
                </div>
                
                            <div class="title-section">
                                <div class="khmer-text">{{ $business->name }}</div>
                                <div class="khmer-text">តារាងផ្ទៀងផ្ទាត់សមតុល្យធនាគារប្រចាំខែ Monthly Bank Reconciliation</div>
                            </div>
                            <div>
                                <span class="khmer-text">.ក</span>
                            </div>
                        </div>
                        
                        <!-- Date Section -->
                        <div class="date-section">
                            AS AT {{ $formatted_date }}
                        </div>
                        
                        @if(!empty($formatted_data))
                            @foreach($formatted_data as $account_data)
                                <!-- Account Information -->
                                <div class="form-section">
                                    <div class="info-row">
                                        <div class="info-label">Bank Account No.:</div>
                                        <div class="info-input">{{ $account_data['bank_account_no'] }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Account Name:</div>
                                        <div class="info-input">{{ $account_data['account_name'] }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Name of the Bank:</div>
                                        <div class="info-input">{{ $account_data['bank_name'] }}</div>
                                    </div>
                                    
                                    <!-- Balance Per Bank Statement -->
                                    <div class="balance-row">
                                        <div>Balance per bank statement</div>
                                        <div>KHR {{ number_format($account_data['balance_per_statement'], 2) }}</div>
                                    </div>
                                    
                                    <!-- Outstanding Checks -->
                                    <div>
                                        <div class="section-label">Less: Outstanding checks</div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="check-column">Check #</th>
                                                    <th class="date-column">Date</th>
                                                    <th class="check-column">Check Holder</th>
                                                    <th class="amount-column">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td></td><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td><td></td></tr>
                                            </tbody>
                                        </table>
                                        <div class="total-row">
                                            <div></div>
                                            <div>KHR -</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Receipt in book, not on statement -->
                                    <div>
                                        <div class="section-label">Plus: Receipt in book, not on statement (e.g. direct credits)</div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="date-column">Date</th>
                                                    <th class="description-column">Description</th>
                                                    <th class="amount-column">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                            </tbody>
                                        </table>
                                        <div class="total-row">
                                            <div></div>
                                            <div>KHR -</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Total Deduction -->
                                    <div class="total-row">
                                        <div>Total Deduction</div>
                                        <div>KHR -</div>
                                    </div>
                                    
                                    <!-- Receipts in book, not on statement -->
                                    <div class="section-label">
                                        Plus: Receipts in book, not on statement (e.g. deposit in transit)
                                    </div>
                                    
                                    <!-- Adjusted Bank Balance -->
                                    <div class="balance-row highlight">
                                        <div>Adjusted bank balance</div>
                                        <div>KHR {{ number_format($account_data['adjusted_bank_balance'], 2) }}</div>
                                    </div>
                                    
                                    <!-- Exchange Rate -->
                                    <div class="exchange-rate">
                                        <div>Exchange rate</div>
                                        <div class="exchange-rate-value">{{ number_format($exchange_rate, 0) }}</div>
                                    </div>
                                    
                                    <!-- Balance Per Book -->
                                    <div class="balance-row">
                                        <div>Balance per book</div>
                                        <div>$ {{ number_format($account_data['balance_per_book'], 2) }}</div>
                                    </div>
                                    
                                    <!-- Payments on statement, not on book -->
                                    <div>
                                        <div class="section-label">Less: Payments on statement, not on book (e.g. bank charges) & non-cash trans in book</div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="date-column">Date</th>
                                                    <th class="description-column">Description</th>
                                                    <th class="amount-column">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                            </tbody>
                                        </table>
                                        <div class="total-row">
                                            <div></div>
                                            <div>$ -</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Receipts on statement, not in book -->
                                    <div>
                                        <div class="section-label">Plus: Receipts on statement, not in book (e.g. interest earned)</div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="date-column">Date</th>
                                                    <th class="description-column">Description</th>
                                                    <th class="amount-column">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                                <tr><td></td><td></td><td></td></tr>
                                            </tbody>
                                        </table>
                                        <div class="total-row">
                                            <div></div>
                                            <div>$ -</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Adjusted Book Balance -->
                                    <div class="balance-row highlight">
                                        <div>Adjusted book balance</div>
                                        <div class="underline">$ {{ number_format($account_data['adjusted_book_balance'], 2) }}</div>
                                    </div>
                                    
                                    <!-- Note Section -->
                                    <div class="balance-row">
                                        <div>Note: The difference between statement and book balance MUST be</div>
                                        <div>$ {{ number_format($account_data['difference'], 2) }}</div>
                                    </div>
                                    
                                    <!-- Signature Section -->
                                    <div class="signature-section">
                                        <div class="signature-block">
                                            <div>Prepared by:</div>
                                            <div class="signature-line"></div>
                                            <div>Name:</div>
                                            <div>Position:</div>
                                            <div>Date: {{ $formatted_date }}</div>
                                        </div>
                                        <div class="signature-block">
                                            <div>Approved by:</div>
                                            <div class="signature-line"></div>
                                            <div>Name:</div>
                                            <div>Position: <span class="khmer-text">នាយកបណ្ឌិត</span></div>
                                            <div>Date: {{ $formatted_date }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="form-section">
                                <p class="text-center">No bank account data found for the selected filters.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .mt-25 {
        margin-top: 25px;
    }
    .reconciliation-form {
        border: 2px solid #000;
        max-width: 900px;
        margin: 0 auto;
        padding: 0;
    }
    .header {
        display: flex;
        align-items: center;
        background-color: #f0f0f0;
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }
    .logo {
        width: 80px;
        margin-right: 15px;
    }
    .title-section {
        flex-grow: 1;
    }
    .khmer-text {
        font-family: 'Khmer OS', 'Kantumruy', sans-serif;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    .english-title {
        font-size: 18px;
        font-weight: bold;
        color: #065b9a;
    }
    .date-section {
        text-align: center;
        background-color: #d9edf7;
        padding: 5px;
        font-weight: bold;
    }
    .form-section {
        padding: 10px 20px;
    }
    .info-row {
        display: flex;
        margin-bottom: 10px;
    }
    .info-label {
        width: 150px;
        font-weight: bold;
    }
    .info-input {
        flex-grow: 1;
        border-bottom: 1px solid #000;
        padding-left: 5px;
    }
    .balance-row {
        display: flex;
        justify-content: space-between;
        margin: 15px 0;
        font-weight: bold;
    }
    .section-label {
        font-weight: bold;
        margin: 15px 0 5px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 5px 0 15px 0;
    }
    table, th, td {
        border: 1px solid #000;
    }
    th, td {
        padding: 5px;
        height: 25px;
    }
    .amount-column {
        width: 20%;
    }
    .date-column {
        width: 15%;
    }
    .check-column {
        width: 15%;
    }
    .description-column {
        width: 50%;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        margin: 5px 0;
        font-weight: bold;
    }
    .exchange-rate {
        display: flex;
        justify-content: center;
        margin: 10px 0;
    }
    .exchange-rate-value {
        color: red;
        font-weight: bold;
        margin-left: 10px;
    }
    .signature-section {
        display: flex;
        margin-top: 30px;
    }
    .signature-block {
        flex: 1;
        margin: 0 10px;
    }
    .signature-line {
        border-top: 1px solid #000;
        margin: 50px 0 5px 0;
    }
    .highlight {
        background-color: #ffeeba;
    }
    .right-align {
        text-align: right;
    }
    .underline {
        text-decoration: underline;
    }
    
    @media print {
        .no-print {
            display: none;
        }
        
        #report-container {
            padding: 0;
            margin: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
    }
</style>

@endsection

@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for account filter
        if ($('.select2').length > 0) {
            $('.select2').select2();
        }
        
        // Flag to prevent auto-submission on page load
        var initialPageLoad = true;
        
        // Initialize the date filter component
        $('#date_filter').change(function() {
            if ($(this).val() === 'custom_month_range') {
                $('#custom_range_inputs').show();
            } else {
                $('#custom_range_inputs').hide();
                
                // Only auto-submit if it's not the initial page load
                if (!initialPageLoad) {
                    $('#filter_form').submit();
                }
            }
        });
        
        // Trigger change event to properly set the initial state
        $('#date_filter').trigger('change');
        
        // Reset the flag after initial page load
        setTimeout(function() {
            initialPageLoad = false;
        }, 500);
        
        // Initialize date pickers
        $('#start_date, #end_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
        
        // Apply custom range button
        $('#apply_custom_range').click(function() {
            $('#filter_form').submit();
        });
    });
</script>
@endsection 