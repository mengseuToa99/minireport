@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.rental_invoice'))

@include('minireportb1::MiniReportB1.components.linkforinclude')

@section('content')

<div style="margin: 16px" class="no-print">
    @include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
    <button type="button" id="print-invoice" class="btn btn-info mt-3"><i class="fa fa-print"></i> @lang('messages.print')</button>
</div>

<div id="invoice-container" class="container">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .receipt td, .receipt th {
            border: 1px solid #ddd;
            padding: 8px;
            position: relative;
        }
        .receipt-header {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            background-color: #fff;
        }
        .label {
            color: #777;
            font-size: 12px;
            position: absolute;
            top: 2px;
            left: 8px;
        }
        .khmer-text {
            font-family: 'Khmer OS', 'Kantumruy', sans-serif;
        }
        .date-cell {
            text-align: right;
        }
        .item-row td {
            height: 40px;
        }
        .empty-row td {
            height: 30px;
        }
        .total-row td {
            text-align: right;
            font-weight: bold;
        }
        .signature-row td {
            height: 60px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            display: inline-block;
            border-top: 1px solid #000;
            width: 80%;
            margin-top: 40px;
        }
        @media print {
            .no-print {
                display: none;
            }
            #invoice-container {
                display: block !important;
            }
        }
    </style>

    <table class="receipt">
        <!-- Header Row -->
        <tr>
            <td colspan="5" class="receipt-header">Office Receipt</td>
        </tr>
        
        <!-- Customer Info -->
        <tr>
            <td width="20%">
                <span class="label khmer-text">ក្រុមហ៊ុន/អតិថិជន</span>
                <br>
                Company name/Customer
            </td>
            <td colspan="3">
                <span class="label khmer-text" id="customer-name">វិទ្យាស្ថានបច្ចេកវិទ្យាកម្ពុជា</span>
            </td>
            <td width="15%" class="date-cell">
                <span class="label khmer-text">កាលបរិច្ឆេទ៖</span>
                <br>
                <span class="label">Date</span>
                <br>
                <span id="invoice-date">{{ date('d/m/Y') }}</span>
            </td>
        </tr>
        
        <!-- Address -->
        <tr>
            <td>
                <span class="label khmer-text">អាសយដ្ឋាន</span>
                <br>
                Address
            </td>
            <td colspan="4">
                <span class="khmer-text" id="customer-address">#08K ផ្លូវ900 សង្កាត់ទួលស្វាយព្រៃ ខណ្ឌបឹងកេងកង រាជធានីភ្នំពេញ</span>
            </td>
        </tr>
        
        <!-- Phone -->
        <tr>
            <td>
                <span class="label khmer-text">លេខទូរស័ព្ទ</span>
                <br>
                Phone
            </td>
            <td colspan="4" id="customer-phone">004 លីម ស្រីពេជ្រ</td>
        </tr>
        
        <!-- Item Header -->
        <tr>
            <td class="khmer-text">
                Nº
            </td>
            <td class="khmer-text">
                DESCRIPTION រៀបរាប់
            </td>
            <td class="khmer-text" width="10%">
                បរិមាណ<br>Unit
            </td>
            <td class="khmer-text" width="15%">
                តម្លៃឯកតា<br>
                Unit Price
            </td>
            <td class="khmer-text" width="15%">
                តម្លៃសរុប<br>
                Amount
            </td>
        </tr>
        
        <!-- Item 1 -->
        <tr class="item-row">
            <td>1</td>
            <td class="khmer-text" id="description">ចំណាយលើការផ្សព្វផ្សាយ ខែ {{ $khmer_month }} {{ date('Y') }}</td>
            <td style="text-align: center;">1</td>
            <td style="text-align: right;" id="unit-price">$ {{ number_format($amount, 2) }}</td>
            <td style="text-align: right;" id="amount">$ {{ number_format($amount, 2) }}</td>
        </tr>
        
        <!-- Empty rows -->
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td></tr>
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td></tr>
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td></tr>
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td></tr>
        <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td></tr>
        
        <!-- Total USD -->
        <tr class="total-row">
            <td colspan="3" rowspan="3"></td>
            <td>
                <span class="khmer-text">សរុប (USD)</span>
            </td>
            <td style="text-align: right;" id="total-usd">$ {{ number_format($amount, 2) }}</td>
        </tr>
        
        <!-- Exchange Rate -->
        <tr class="total-row">
            <td>Rate(KHR)</td>
            <td style="text-align: right;" id="exchange-rate">{{ number_format($exchange_rate) }}</td>
        </tr>
        
        <!-- Total KHR -->
        <tr class="total-row">
            <td>
                <span class="khmer-text">សរុប (KHR)</span>
            </td>
            <td style="text-align: right;" id="total-khr">{{ number_format(round($amount * $exchange_rate)) }}</td>
        </tr>
        
        <!-- Signature Row -->
        <tr class="signature-row">
            <td colspan="2">
                <span class="khmer-text">ហត្ថលេខា និងឈ្មោះអ្នកទិញ</span>
                <div class="signature-line"></div>
            </td>
            <td colspan="3">
                <span class="khmer-text">ហត្ថលេខា និងឈ្មោះអ្នកលក់</span>
                <div class="signature-line"></div>
            </td>
        </tr>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Print invoice button click
        $('#print-invoice').on('click', function() {
            window.print();
        });
    });
</script>

@endsection 