@extends('layouts.app')

@section('title', 'View File')

@section('content')
    <div class="container my-4">
        <h1 class="text-center mb-4">View File: {{ $file->name ?? 'N/A' }}</h1>

        @component('components.filters', ['title' => __('report.filters')])
            @include('sell.partials.sell_list_filters')
            @if ($business_locations)
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('business.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

            @if ($customers)
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                        {!! Form::select('customer_id', $customers, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

            @if (!empty($sources))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_source', __('lang_v1.sources') . ':') !!}
                        {!! Form::select('sell_list_filter_source', $sources, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endif

  
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('រូបរាង', __('រូបរាង') . ':') !!}
                    {!! Form::select('រូបរាង', [
                        'class' => 'រូបរាង 1',
                        'style' => 'រូបរាង 2',
                        'placeholder' => __('lang_v1.all'),
                    ]) !!}
                </div>
            </div>


            <div class="box-tools">
                <a href="{{ route('MiniReportB1.dashboard') }}#reportlayout"
                    class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    បន្ថែមរូបរាង
                </a>
            @endcomponent

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (!empty($visibleColumnNames) && !empty($paginatedRows))
                <div class="table-responsive">
                    <table class="table table-hover table-striped border rounded shadow">
                        <thead class="thead-dark">
                            <tr>
                                @foreach ($visibleColumnNames as $column)
                                    <th class="text-center align-middle">{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedRows as $row)
                                <tr>
                                    @foreach ($row as $cell)
                                        <td class="align-middle text-center">{{ $cell['value'] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($visibleColumnNames) }}" class="text-center text-muted">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    No data available to display. Please check the configuration.
                </div>
            @endif
        </div>
    @endsection
