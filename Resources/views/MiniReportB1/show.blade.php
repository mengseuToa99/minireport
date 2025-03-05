@extends('layouts.app')

@section('title', __('lang_v1.all_sales'))

@section('css')
    <style>
        /* Add your custom styles here */
    </style>
@endsection

@section('content')
    <div class="create-section" style="margin-left: 20px;">
        <a href="{{ route('MiniReportB1.index') }}" class="btn btn-sm btn-primary" style="margin: 10px; font-size: 15px">
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <div class="table-container">
            <div class="panel-2">
                <!-- Left Panel -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fa fa-list-alt"></i> Menu</h5>
                            <a style="margin-top: 15px; font-size: 10px;" href="{{ route('MiniReportB1.select-tables-fields') }}" class="btn btn-primary">
                                <i class="fas fa-layer-group mr-2"></i> Create Menu
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="fieldsAccordion">
                                @foreach ($allData as $reportId => $reportData)
                                    @foreach ($reportData['report']->fields->groupBy('table_name') as $table => $fields)
                                        <div class="card">
                                            <div class="card-header" id="heading{{ Str::slug($table) }}">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                                        data-target="#collapse{{ Str::slug($table) }}">
                                                        {{ $table }}
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="collapse{{ Str::slug($table) }}" class="collapse"
                                                data-parent="#fieldsAccordion">
                                                <div class="card-body">
                                                    @foreach ($fields as $field)
                                                        <div class="field-item" draggable="true"
                                                            ondragstart="fieldDragStart(event)" data-field="{{ $field->field_name }}"
                                                            onclick="selectColumnType('{{ $field->field_name }}', '{{ ucfirst(str_replace('_', ' ', $field->field_name)) }}')">
                                                            <i class="fa {{ getFieldIcon($field->field_name) }}"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $field->field_name)) }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                @foreach ($allData as $reportId => $reportData)
                    <table class="dynamic-table" id="dynamicTable">
                        <thead>
                            <tr>
                                @foreach ($reportData['report']->fields as $field)
                                    <th draggable="true" ondragstart="handleHeaderDragStart(event)" ondragover="dragOver(event)"
                                        ondrop="drop(event)" ondragend="dragEnd()" data-field="{{ $field->field_name }}">
                                        {{ ucfirst(str_replace('_', ' ', $field->field_name)) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData['data'] as $row)
                                <tr>
                                    @foreach ($reportData['report']->fields as $field)
                                        <td contenteditable="true">{{ $row[$field->field_name] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Include modals and context menus here -->
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Add your JavaScript logic here
    </script>
@endsection