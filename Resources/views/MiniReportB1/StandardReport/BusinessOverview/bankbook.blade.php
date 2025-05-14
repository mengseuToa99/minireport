@extends('layouts.app')
@section('title', 'សៀវភៅធនាគារ (Bankbook Report)')

@include('minireportb1::MiniReportB1.components.linkforinclude')

<style>
    /* Print styles */
    @media print {
        body, html {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            width: 100% !important;
            overflow: visible !important;
        }
        
        .no-print, .regular-view, .reusable-table-container, header, footer, nav, aside, .main-footer, .main-header {
            display: none !important;
        }
        
        .content-wrapper, .content, .wrapper {
            margin: 0 !important;
            padding: 0 !important;
            position: static !important;
            transform: none !important;
        }
        
        .print-only-template {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        /* Ensure no page breaks */
        .print-container * {
            page-break-inside: avoid !important;
        }
        
        table { break-inside: avoid !important; }
        
        td {
            font-size: 10px !important;
            line-height: 1.1 !important;
            padding: 2px !important;
        }
        
        /* Special coloring for table headers */
        .header-blue {
            background-color: #a6c5e4 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Red text for exchange rate */
        .exchange-rate-red {
            color: red !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        @page {
            size: landscape;
            margin: 0mm;
        }
    }
    
    /* Regular styles */
    .reusable-table-container {
        width: 100%;
        overflow-x: auto;
    }
    
    .reusable-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }
    
    .reusable-table th,
    .reusable-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }
    
    .reusable-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .number {
        text-align: right;
    }
    
    /* This makes the print-only template hidden when not printing */
    .print-only-template {
        display: none;
    }
</style>

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")

    <div style="margin: 16px" class="no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                <form id="filterForm">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('account_id', __('account.account') . ' (គណនី):') !!}
                            {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.all')]) !!}
                        </div>
                    </div>
                    @include('minireportb1::MiniReportB1.components.filterdate')
                </form>
                <div class="currency-toggle-container" style="margin-top: 10px; margin-bottom: 10px;">
                    <label class="mr-2">បង្ហាញរូបិយប័ណ្ណ៖</label>
                    <button id="currency-toggle" class="btn btn-sm btn-primary" data-currency="khr">ប្តូរទៅ៖ ដុល្លារ (USD)</button>
                </div>
              @include('minireportb1::MiniReportB1.components.printbutton')
            </div>
        @endcomponent
    </div>

    <div class="regular-view">
        @include('minireportb1::MiniReportB1.components.reportheader', ['report_name' => 'សៀវភៅធនាគារ (BankBook)'])

        <div class="reusable-table-container">
            <table class="reusable-table" id="bankbook-table">
                <thead>
                    <tr>
                        <th class="col-xs">#</th>
                        <th class="col-sm">កាលបរិច្ឆេទ</th>
                        <th class="col-md">លេខប័ណ្ណ</th>
                        <th class="col-md">អតិថិជន</th>
                        <th class="col-md">ការពិពណ៌នា</th>
                        <th class="col-md">លេខកូដគណនី</th>
                        <th class="col-md">សាច់ប្រាក់ចូល</th>
                        <th class="col-md">ចំណាយ</th>
                        <th class="col-xl">សមតុល្យ</th>
                    </tr>
                </thead>
                <tbody id="bankbook-table-body">
                    <!-- Data loaded via AJAX -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><strong>សរុប:</strong></td>
                        <td class="number" id="total-cash-in"><strong>0</strong></td>
                        <td class="number" id="total-expense"><strong>0</strong></td>
                        <td class="number" id="final-balance"><strong>0</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Print Template that appears only when printing -->
    <div class="print-only-template">
        <div class="print-container" style="padding: 5mm;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 15%; text-align: center; vertical-align: middle;">
                        <img src="{{ asset('/logo.png') }}" alt="Logo" style="max-width: 70px; height: auto;">
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="font-size: 16px; font-weight: bold;">{{ Session::get('business.name') }}</div>
                        <div style="font-size: 14px;">សៀវភៅធនាគារ (BankBook)</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="header-blue" style="text-align: center; font-size: 14px; padding: 3px;">
                        Accounting
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding: 2px; font-size: 12px;">
                        @if(isset($start_date) && isset($end_date))
                            {{ \Carbon\Carbon::parse($start_date)->format('d M-Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M-Y') }}
                        @else
                            {{ now()->format('d M-Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; padding: 2px; font-size: 11px;">Exchange Rate:</td>
                    <td style="font-weight: bold; padding: 2px; font-size: 11px;">
                        <span id="print-exchange-rate" class="exchange-rate-red">4100</span>
                        <span style="float: right; margin-right: 15%;">USD</span>
                        <span style="float: right; margin-right: 5%;">KHR</span>
                    </td>
                </tr>
            </table>

            <table id="print-bankbook-table" style="width: 100%; border-collapse: collapse; border: 1px solid black; margin-top: 3px;">
                <thead>
                    <tr>
                        <th style="padding: 3px; border: 1px solid black; font-size: 10px;">កាលបរិច្ឆេទ</th>
                        <th style="padding: 3px; border: 1px solid black; font-size: 10px;">លេខប័ណ្ណ</th>
                        <th style="padding: 3px; border: 1px solid black; font-size: 10px;">អតិថិជន</th>
                        <th style="padding: 3px; border: 1px solid black; font-size: 10px;">ការពិពណ៌នា</th>
                        <th style="padding: 3px; border: 1px solid black; font-size: 10px;">លេខកូដគណនី</th>
                        <th style="padding: 3px; border: 1px solid black; text-align: right; font-size: 10px;">ចូល USD</th>
                        <th style="padding: 3px; border: 1px solid black; text-align: right; font-size: 10px;">ចូល KHR</th>
                        <th style="padding: 3px; border: 1px solid black; text-align: right; font-size: 10px;">ចំណាយ USD</th>
                        <th style="padding: 3px; border: 1px solid black; text-align: right; font-size: 10px;">ចំណាយ KHR</th>
                        <th style="padding: 3px; border: 1px solid black; text-align: right; font-size: 10px;">សមតុល្យ USD</th>
                        <th style="padding: 3px; border: 1px solid black; text-align: right; font-size: 10px;">សមតុល្យ KHR</th>
                    </tr>
                </thead>
                <tbody id="print-bankbook-body">
                    <!-- Print data loaded via JS -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">សរុប:</td>
                        <td id="print-total-cash-in-usd" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">$0.00</td>
                        <td id="print-total-cash-in-khr" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">0 ៛</td>
                        <td id="print-total-expense-usd" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">$0.00</td>
                        <td id="print-total-expense-khr" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">0 ៛</td>
                        <td id="print-final-balance-usd" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">$0.00</td>
                        <td id="print-final-balance-khr" style="padding: 3px; border: 1px solid black; text-align: right; font-weight: bold; font-size: 10px;">0 ៛</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize jQuery UI Datepicker
            $("#start_date, #end_date").datepicker({
                dateFormat: "yy-mm-dd"
            });

            // Initialize Select2
            $('.select2').select2();
            
            // Currency conversion variables
            let currentCurrency = 'khr'; // Set default to KHR
            let bankbookData = [];
            let exchangeRates = {};
            let defaultExchangeRate = 4100;
            
            // Toggle between USD and KHR display
            $('#currency-toggle').on('click', function() {
                const button = $(this);
                currentCurrency = button.data('currency');
                
                if (currentCurrency === 'khr') {
                    button.data('currency', 'usd');
                    button.text('ប្តូរទៅ៖ រៀល (KHR)');
                    displayInUSD();
                } else {
                    button.data('currency', 'khr');
                    button.text('ប្តូរទៅ៖ ដុល្លារ (USD)');
                    displayInKHR();
                }
            });
            
            // Print button click handler
        
            
          
            
            function displayInKHR() {
                if (bankbookData.length === 0) return;
                
                let totalCashInKHR = 0;
                let totalExpenseKHR = 0;
                let finalBalanceKHR = 0;
                
                const tbody = $('#bankbook-table-body');
                tbody.empty();
                
                $.each(bankbookData, function(index, row) {
                    // Get exchange rate for this transaction date
                    const exchangeRate = row.exchange_rate || defaultExchangeRate; // Default if not available
                    
                    // Convert USD values to KHR
                    const cashInKHR = parseFloat(row.cash_in) * exchangeRate;
                    const expenseKHR = parseFloat(row.expense) * exchangeRate;
                    const balanceKHR = parseFloat(row.balance) * exchangeRate;
                    
                    const cleanDescription = $('<div>').html(row.description).text();
                    
                    const tr = $('<tr>').append(
                        $('<td>').text(index + 1),
                        $('<td>').text(row.date),
                        $('<td>').text(row.voucher_no),
                        $('<td>').text(row.payee),
                        $('<td>').text(cleanDescription),
                        $('<td>').text(row.ac_code),
                        $('<td>').text(formatCurrency(cashInKHR, true)),
                        $('<td>').text(formatCurrency(expenseKHR, true)),
                        $('<td>').text(formatCurrency(balanceKHR, true))
                    );
                    tbody.append(tr);
                    
                    totalCashInKHR += cashInKHR;
                    totalExpenseKHR += expenseKHR;
                    finalBalanceKHR = balanceKHR; // Last balance is the final balance
                });
                
                $('#total-cash-in strong').text(formatCurrency(totalCashInKHR, true));
                $('#total-expense strong').text(formatCurrency(totalExpenseKHR, true));
                $('#final-balance strong').text(formatCurrency(finalBalanceKHR, true));
            }
            
            function displayInUSD() {
                if (bankbookData.length === 0) return;
                
                let totalCashIn = 0;
                let totalExpense = 0;
                let finalBalance = 0;
                
                const tbody = $('#bankbook-table-body');
                tbody.empty();
                
                $.each(bankbookData, function(index, row) {
                    const cleanDescription = $('<div>').html(row.description).text();
                    
                    const tr = $('<tr>').append(
                        $('<td>').text(index + 1),
                        $('<td>').text(row.date),
                        $('<td>').text(row.voucher_no),
                        $('<td>').text(row.payee),
                        $('<td>').text(cleanDescription),
                        $('<td>').text(row.ac_code),
                        $('<td>').text(formatCurrency(row.cash_in)),
                        $('<td>').text(formatCurrency(row.expense)),
                        $('<td>').text(formatCurrency(row.balance))
                    );
                    tbody.append(tr);
                    
                    totalCashIn += parseFloat(row.cash_in) || 0;
                    totalExpense += parseFloat(row.expense) || 0;
                    finalBalance = parseFloat(row.balance) || 0;
                });
                
                $('#total-cash-in strong').text(formatCurrency(totalCashIn));
                $('#total-expense strong').text(formatCurrency(totalExpense));
                $('#final-balance strong').text(formatCurrency(finalBalance));
            }

            // Function to load bankbook data via AJAX
            function loadBankbookData() {
                const formData = {
                    date_filter: $('#date_filter').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    account_id: $('#account_id').val(),
                    payee_filter: $('#payee_filter').val(),
                    include_exchange_rates: true // Request exchange rates with the data
                };

                $.ajax({
                    url: '{{ route("sr_bankbook") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        $('#bankbook-table-body').html(
                            '<tr><td colspan="9" class="text-center">កំពុងផ្ទុកទិន្នន័យ... (Loading data...)</td></tr>');
                    },
                    success: function(response) {
                        bankbookData = response.data || [];
                        
                        // Reset display if no data
                        if (bankbookData.length === 0) {
                            $('#bankbook-table-body').html(
                                '<tr><td colspan="9" class="text-center">មិនមានប្រតិបត្តិការសម្រាប់ការចម្រាញ់ដែលបានជ្រើសរើស។</td></tr>'
                            );
                            
                            // Reset totals
                            $('#total-cash-in strong').text(formatCurrency(0));
                            $('#total-expense strong').text(formatCurrency(0));
                            $('#final-balance strong').text(formatCurrency(0));
                            return;
                        }
                        
                        // Store exchange rates if provided
                        if (response.exchange_rates) {
                            exchangeRates = response.exchange_rates;
                        }
                        
                        // Display in current currency - default to KHR
                        if (currentCurrency === 'khr') {
                            displayInKHR();
                        } else {
                            displayInUSD();
                        }
                        
                        // Update print view data
                        updatePrintView();
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        $('#bankbook-table-body').html(
                            '<tr><td colspan="9" class="text-center text-danger">មានបញ្ហាក្នុងការផ្ទុកទិន្នន័យ។ សូមព្យាយាមម្តងទៀត។</td></tr>'
                        );
                    }
                });
            }

            // Helper function to format currency
            function formatCurrency(amount, isKHR = false) {
                if (isKHR) {
                    // Format as KHR (no decimal places)
                    return Math.round(parseFloat(amount)).toLocaleString() + ' ៛';
                }
                
                if (typeof __currency_trans_from_en === 'function') {
                    return __currency_trans_from_en(amount, true);
                }
                // Fallback if currency function not available
                return parseFloat(amount).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' $';
            }

            // Initial load with default date range
            loadBankbookData();

            // Event listener for date filter changes
            $('#date_filter').on('change', function() {
                if (this.value === 'custom_range') {
                    $('#custom_range_inputs').show();
                } else {
                    $('#custom_range_inputs').hide();
                    loadBankbookData();
                }
            });

            // Event listener for account filter changes
            $('#account_id, #payee_filter').on('change', function() {
                loadBankbookData();
            });

            // Apply custom date range
            $('#apply_custom_range').on('click', function() {
                if ($('#start_date').val() && $('#end_date').val()) {
                    loadBankbookData();
                } else {
                    alert('សូមជ្រើសរើសកាលបរិច្ឆេទចាប់ផ្តើម និងបញ្ចប់។');
                }
            });

            // Apply filters button
            $('#apply_filters').on('click', function() {
                loadBankbookData();
            });
        });
    </script>
@endsection
