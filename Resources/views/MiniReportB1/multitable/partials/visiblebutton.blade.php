{{-- resources/views/components/visiblebutton.blade.php --}}

@props(['columns' => [], 'tableId' => '', 'storageKey' => ''])

{{-- Column Visibility Button --}}
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#columnVisibilityModal">
    <i class="fas fa-columns"></i> Show/Hide Columns
</button>

{{-- Column Visibility Modal --}}
<div class="modal fade" id="columnVisibilityModal" tabindex="-1" role="dialog" aria-labelledby="columnVisibilityModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnVisibilityModalLabel">Show/Hide Columns</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="columnVisibilityForm">
                    @foreach ($columns as $columnName => $columnIndex)
                        <div class="form-check" style="margin-top: 20px; margin-left: 20px">
                            <input class="form-check-input column-toggle" type="checkbox"
                                style="transform: scale(2.5); width: 20px; height: 20px; margin-right: 25px;"
                                id="column_{{ $columnIndex }}" 
                                data-column-index="{{ $columnIndex }}" 
                                checked>
                            <label class="form-check-label" for="column_{{ $columnIndex }}">
                                {{ $columnName }}
                            </label>
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="unselectAllColumns">Unselect All</button>
                <button type="button" class="btn btn-primary" id="saveColumnVisibility">Okay</button>
            </div>
        </div>
    </div>
</div>