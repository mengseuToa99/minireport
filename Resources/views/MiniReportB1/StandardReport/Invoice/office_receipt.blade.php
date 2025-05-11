@extends('layouts.app')
@section('title', 'Office Receipt')

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')

<!-- Main content -->
<section class="content">
@include('minireportb1::MiniReportB1.components.back_to_dashboard_button')

    <div class="row no-print">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                {!! Form::open(['url' => action([\Modules\MiniReportB1\Http\Controllers\StandardReport\InvoiceController::class, 'getOfficeReceipt']), 'method' => 'get', 'id' => 'filter_form' ]) !!}
                
                <div class="col-md-3">
                    @include('minireportb1::MiniReportB1.components.filterdate')
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('business.location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, request('location_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'location_id']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('expense_category_id', __('expense.expense_category') . ':') !!}
                        {!! Form::select('expense_category_id', $expense_categories ?? [], request('expense_category_id'), ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'expense_category_id']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('transaction_id', __('purchase.transaction') . ':') !!}
                        {!! Form::select('transaction_id', $transactions, $transaction_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'transaction_id']); !!}
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
                    <div class="container">
                        <div class="header">
                            <table class="header-table">
                                <tr>
                                    <td width="20%">
                                        @if($business->logo)
                                            <img src="{{ asset('/uploads/business_logos/' . $business->logo) }}" alt="{{ $business->name }}" class="logo">
                                        @else
                                            <img src="{{ asset('img/default.png') }}" alt="Logo" class="logo">
                                        @endif
                                    </td>
                                    <td width="60%" class="text-center">
                                        <div class="company-name">{{ $business->name }}</div>
                                        <div class="document-title">Office Receipt</div>
                                    </td>
                                    <td width="20%" class="text-right">
                                        <div class="receipt-number">№ {{ $transaction_id ?? 'New' }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <table class="info-section">
                            <tr>
                                <td width="15%">
                                    <div class="label">
                                        <div>Company name/Customer</div>
                                        <div class="khmer">ឈ្មោះក្រុមហ៊ុនអតិថិជន</div>
                                    </div>
                                </td>
                                <td width="35%" id="customer-name">{{ $customer_data ? $customer_data['name'] : 'វិទ្យាស្ថានបច្ចេកវិទ្យាកម្ពុជា' }}</td>
                                <td width="15%"></td>
                                <td width="15%">
                                    <div class="label">
                                        <div>កាលបរិច្ឆេទ៖</div>
                                        <div>Date</div>
                                    </div>
                                </td>
                                <td width="20%" class="date-cell" id="display-date">{{ $display_date }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="label">
                                        <div>Address</div>
                                        <div class="khmer">អាស័យដ្ឋាន</div>
                                    </div>
                                </td>
                                <td colspan="4" id="customer-address">{{ $customer_data ? $customer_data['address'] : '#08K ផ្លូវ900 សង្កាត់ទួលស្វាយព្រៃ ខណ្ឌបឹងកេងកង រាជធានីភ្នំពេញ' }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="label">
                                        <div>Phone</div>
                                        <div class="khmer">លេខទូរស័ព្ទ</div>
                                    </div>
                                </td>
                                <td colspan="4" id="customer-phone">{{ $customer_data ? $customer_data['phone'] : '004 លីម ស្រីពេជ្រ' }}</td>
                            </tr>
                        </table>
                        
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th class="small-column">
                                        <div>№</div>
                                        <div class="khmer">ល.រ</div>
                                    </th>
                                    <th class="description-column">
                                        <div>DESCRIPTION</div>
                                        <div class="khmer">បរិយាយ</div>
                                    </th>
                                    <th class="small-column">
                                        <div>បរិមាណ</div>
                                        <div>Unit</div>
                                    </th>
                                    <th class="number-column">
                                        <div>តម្លៃរាយ</div>
                                        <div>Unit Price</div>
                                    </th>
                                    <th class="number-column">
                                        <div>ទឹកប្រាក់</div>
                                        <div>Amount</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td class="description-column" id="item-description">{{ $description }}</td>
                                    <td id="item-quantity">{{ $quantity }}</td>
                                    <td class="number-column" id="item-price">$ {{ number_format($amount, 2) }}</td>
                                    <td class="number-column" id="item-total">$ {{ number_format($total_amount, 2) }}</td>
                                </tr>
                                <tr class="empty-row"><td colspan="5"></td></tr>
                                <tr class="empty-row"><td colspan="5"></td></tr>
                                <tr class="empty-row"><td colspan="5"></td></tr>
                                <tr class="empty-row"><td colspan="5"></td></tr>
                                <tr class="empty-row"><td colspan="5"></td></tr>
                                <tr class="empty-row"><td colspan="5"></td></tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="total-label">
                                        <div>សរុប (USD)</div>
                                    </td>
                                    <td class="number-column" id="total-usd">$ {{ number_format($total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="total-label">Rate(KHR)</td>
                                    <td class="number-column" id="rate-khr">{{ number_format($exchange_rate, 0) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="total-label">
                                        <div>សរុប (KHR)</div>
                                    </td>
                                    <td class="number-column" id="total-khr">{{ number_format($total_amount_khr, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <table class="signature-section">
                            <tr>
                                <td>
                                    <div class="signature-line">
                                        <div class="khmer">ហត្ថលេខា និងឈ្មោះអ្នកទទួល</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="signature-line">
                                        <div class="khmer">ហត្ថលេខា និងឈ្មោះអ្នកប្រគល់</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<style>
    body {
        font-family: 'Khmer OS', Arial, Helvetica, sans-serif;
        margin: 0;
        padding: 0;
        color: #333;
    }
    .mt-25 {
        margin-top: 25px;
    }
    .container {
        max-width: 900px;
        margin: 0 auto;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        background-color: white;
    }
    .header {
        color: #333;
        background-color: white;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .header-table {
        width: 100%;
        border-collapse: collapse;
    }
    .header-table td {
        vertical-align: middle;
        border: none;
        padding: 5px;
    }
    .logo {
        max-width: 100px;
        height: auto;
    }
    .company-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    .document-title {
        font-size: 22px;
        font-weight: bold;
        margin-top: 5px;
    }
    .receipt-number {
        font-size: 14px;
        color: #666;
    }
    .info-section {
        width: 100%;
        border-collapse: collapse;
    }
    .info-section td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .date-cell {
        text-align: center;
    }
    .label {
        font-weight: bold;
    }
    .khmer {
        font-size: 14px;
        color: #666;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .items-table th, .items-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    .items-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .description-column {
        text-align: left;
        width: 40%;
    }
    .number-column {
        width: 15%;
        text-align: right;
    }
    .small-column {
        width: 10%;
    }
    .totals-section {
        width: 100%;
        border-collapse: collapse;
    }
    .totals-section td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .total-label {
        text-align: right;
        font-weight: bold;
        width: 85%;
    }
    .total-value {
        text-align: right;
        width: 15%;
    }
    .signature-section {
        width: 100%;
        border-collapse: collapse;
        margin-top: 50px;
    }
    .signature-section td {
        width: 50%;
        padding: 10px;
        text-align: center;
    }
    .signature-line {
        border-top: 1px dotted #333;
        margin-top: 60px;
        padding-top: 5px;
    }
    .empty-row td {
        border: 1px solid #ddd;
        height: 30px;
    }

    /* Loading overlay styles */
    .loading {
        position: relative;
        min-height: 200px;
    }
    
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    
    .fa-spin {
        font-size: 24px;
        color: #007bff;
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
        // Initialize Select2 for all dropdowns
        if ($('.select2').length > 0) {
            $('.select2').select2();
        }
        
        // Flag to prevent auto-submission on page load
        var initialPageLoad = true;
        var ajaxInProgress = false;
        
        // Initialize the date filter component
        $('#date_filter').change(function() {
            if ($(this).val() === 'custom_month_range') {
                $('#custom_range_inputs').show();
            } else {
                $('#custom_range_inputs').hide();
                
                // Only auto-submit if it's not the initial page load
                if (!initialPageLoad) {
                    updateFilters();
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
        $('#apply_custom_range').click(function(e) {
            e.preventDefault();
            updateFilters();
        });
        
        // Filter change handlers
        $('#location_id').on('change', function() {
            if (!initialPageLoad) {
                updateFilters();
            }
        });
        
        $('#expense_category_id').on('change', function() {
            if (!initialPageLoad) {
                // When expense category changes, we need to update the transactions dropdown
                updateTransactionOptions();
            }
        });
        
        $('#transaction_id').on('change', function() {
            if (!initialPageLoad) {
                updateFilters();
            }
        });
        
        // Function to update transaction options based on location and expense category
        function updateTransactionOptions() {
            var location_id = $('#location_id').val();
            var expense_category_id = $('#expense_category_id').val();
            
            if (ajaxInProgress) return;
            ajaxInProgress = true;
            
            $.ajax({
                url: "{{ action([\Modules\MiniReportB1\Http\Controllers\StandardReport\InvoiceController::class, 'getTransactions']) }}",
                data: {
                    location_id: location_id,
                    expense_category_id: expense_category_id
                },
                dataType: 'json',
                success: function(result) {
                    $('#transaction_id').empty().append($('<option>', {
                        value: '',
                        text: "{{ __('lang_v1.all') }}"
                    }));
                    
                    if (result.transactions) {
                        $.each(result.transactions, function(id, name) {
                            $('#transaction_id').append($('<option>', {
                                value: id,
                                text: name
                            }));
                        });
                    }
                    
                    $('#transaction_id').select2('destroy').select2();
                    updateFilters();
                    ajaxInProgress = false;
                },
                error: function() {
                    ajaxInProgress = false;
                }
            });
        }
        
        // Function to update the report with AJAX
        function updateFilters() {
            if (ajaxInProgress) return;
            ajaxInProgress = true;
            
            $('#report-container').addClass('loading').prepend(
                '<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>'
            );
            
            var data = $('#filter_form').serialize();
            
            $.ajax({
                url: "{{ action([\Modules\MiniReportB1\Http\Controllers\StandardReport\InvoiceController::class, 'getOfficeReceipt']) }}",
                data: data,
                dataType: 'html',
                success: function(result) {
                    $('#report-container').html(
                        $(result).find('#report-container').html()
                    );
                    
                    // Update the URL with the new filters
                    history.pushState(null, null, '?' + data);
                    ajaxInProgress = false;
                },
                error: function() {
                    $('#report-container').find('.overlay').remove();
                    toastr.error("{{ __('messages.something_went_wrong') }}");
                    ajaxInProgress = false;
                }
            });
        }
    });
</script>
@endsection 