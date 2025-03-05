@extends('layouts.app')
@section('title', 'Income for The Month')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body,
    table {
        font-family: 'Hanuman', serif;
    }

    .income-table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
        font-size: 14px;
        table-layout: fixed;
    }

    .income-table th {
        background-color: #f0f0f0;
        border: 1px solid #000;
        padding: 8px;
        font-weight: bold;
        text-align: left;
        white-space: normal;
    }

    .income-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
        white-space: normal;
    }

    .income-table td.number {
        text-align: right;
        font-family: monospace;
    }

    .table-responsive {
        overflow-x: auto;
        margin: 20px;
    }

    /* Signature Section */
    .signature-section {
        margin-top: 70px;
        padding-top: 20px;
        margin-left: 75%;
        text-align: center;
    }

    .signature-line {
        display: inline-block;
        width: 200px;
        border-bottom: 1px solid #000;
        margin: 20px 0;
    }

    .signature-label {
        font-size: 14px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="table-responsive">
    <table class="income-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference NO</th>
                <th>Location</th>
                <th>Supplier</th>
                <th>Purchase Status</th>
                <th>Payment Status</th>
                <th>Grand Total</th>
                <th>Payment Due</th>
                <th>Add By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($formatted_data as $row)
            <tr>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['voucher'] }}</td>
                <td class="number">{{ $row['unit_price'] }}</td>
                <td class="number">{{ $row['item_tax'] }}</td>
                <td class="number">{{ $row['subtotal'] }}</td>
                <td>{{ $row['customer'] }}</td>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['description'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No data available for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Signature Section -->
<div class="signature-section">
    <div class="signature-line"></div>
    <div class="signature-label">Signature</div>
</div>
@endsection