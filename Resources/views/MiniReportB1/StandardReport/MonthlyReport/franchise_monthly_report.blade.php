@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
@endsection

@section('content')

    <div class="no-print" style="margin: 2%">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="filter-container">
                @include('minireportb1::MiniReportB1.components.filter')
            </div>
        @endcomponent
    </div>

    <div class="report-header" id="report-header">
        <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
            របាយការណ៍ហ្វ្រែនឆាយប្រចាំខែ
        </h2>
        <h3 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 18px;">
            សាខាថ្មី សម្រាប់ខែ{{ $month }}
        </h3>
        <h3 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 18px;">
            ចាប់ពីថ្ងៃទី {{ $start_day }} ដល់ថ្ងៃទី {{ $end_day }}
        </h3>
        <h3 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 18px;">
            សាខាថ្មីរៀននៅ {{ $locations }}
        </h3>
    </div>

    <div class="reusable-table-container" id="franchise">
        <table class="reusable-table" id="franchise-report-table">
            <thead>
                <tr>
                    <th class="col-xs text-center">#</th>
                    <th class="col-md text-center">@lang('business.business_name')</th>
                    <th class="col-md text-center">@lang('user.name')</th>
                    <th class="col-md text-center">@lang('lang_v1.customer_group')</th>
                    <th class="col-md text-center">@lang('contact.mobile')</th>
                    <th class="col-md text-center">@lang('business.city')</th>
                    <th class="col-md text-center">@lang('business.state')</th>
                    <th class="col-md text-center">@lang('business.country')</th>
                    <th class="col-md text-center">@lang('lang_v1.assigned_to')</th>
                    <th class="col-md text-center">ថ្ងៃចុះឈ្មោះ</th>
                    <th class="col-md text-center">@lang('sale.total_paid')</th>
                    <th class="col-sm text-center">@lang('lang_v1.payment_method')</th>
                    <th class="col-md text-center">ថ្ងៃចូលរៀន</th>
                    <th class="col-md text-center">ថ្ងៃផុតកំណត់</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr class="table-row text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $contact->supplier_business_name ?? '' }}</td>
                        <td>{{ $contact->name ?? '' }}</td>
                        <td>{{ $contact->customer_group ?? '' }}</td>
                        <td>{{ $contact->mobile ?? '' }}</td>
                        <td>{{ $contact->address_line_1 ?? '' }}</td>
                        <td>{{ $contact->address_line_2 ?? '' }}</td>
                        <td>{{ $contact->city ?? '' }}</td>
                        <td>{{ $contact->assigned_to ?: 'គ្មាន' }}</td>
                        <td>{{ $contact->register_date ? \Carbon\Carbon::parse($contact->register_date)->format('d/m/Y') : '' }}</td>
                        <td>{{ $contact->total_paid ?? '0' }}</td>
                        <td>{{ $contact->payment_method ?? '' }}</td>
                        <td>{{ $contact->created_at ? \Carbon\Carbon::parse($contact->created_at)->format('d/m/Y') : '' }}</td>
                        <td>{{ $contact->expired_date ? \Carbon\Carbon::parse($contact->expired_date)->format('d/m/Y') : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total-row" style="text-align: right; padding: 1% 0; font-weight: bold; margin-top: -1%; margin-right: 2%;">
            ចំនួនសរុប: <span class="text-red tw-font-semibold h4"> {{ $contacts->count() }} </span> នាក់
        </div>
    </div>

    <script>
        const tablename = "#franchise";
        const reportname = "Franchise";
    </script>
    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
