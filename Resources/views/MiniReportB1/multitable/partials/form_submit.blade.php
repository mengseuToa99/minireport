<!-- resources/views/minireportb1/MiniReportB1/multitable/partials/form_submit.blade.php -->
<form id="createFileForm" class="mb-3">
    <div style="margin: 16px 20px">
        @csrf
        <div class="row">
            <!-- File Name Input -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fileName">File Name</label>
                    <input type="text" class="form-control" id="fileName" name="file_name" required>
                </div>
            </div>
            <!-- Parent Folder Dropdown -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="parentFolder">Select Session</label>
                    <select class="form-control" id="parentFolder" name="parent_id" required>
                        @foreach ($folders->where('type', 'report_section') as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->folder_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Save Button -->
            <div class="col-md-4 d-flex align-items-end" style="margin-top: 23px">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save mr-2"></i> Save
                </button>
            </div>

        </div>
    </div>
</form>
