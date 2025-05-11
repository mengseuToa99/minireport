@extends('layouts.app')
@section('title', 'Income for The Month')

@section('css')
{{-- style for table --}}
<link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body,
    table {
        font-family: 'Hanuman', serif;
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

    #exspend-list {
        margin: 16px;
    }
</style>
@endsection

@section('content')

<div class="filter-container">
    <button id="printButton" class="btn btn-primary">
        <i class="fa fa-print"></i> @lang('messages.print')
    </button>
</div>

<div class="reusable-table-conter" id="exspend-list">
    <table class="reusable-table">
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

<script>
    const tablename = "#exspend-list";
</script>
<script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
