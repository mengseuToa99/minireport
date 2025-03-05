<div class="report-display-container">
    @foreach ($reports as $report)
        <div class="report-item bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-blue-800">{{ $report->name }}</h3>
                <div class="flex space-x-4">
                    <a href="{{ route('minireportb1.reports.edit', $report->id) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button class="text-red-600 hover:text-red-800 delete-report" data-report-id="{{ $report->id }}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="selected-fields">
                <h4 class="text-lg font-semibold text-blue-700 mb-3">Selected Fields</h4>
                <ul class="space-y-2">
                    @foreach ($report->fields as $field)
                        <li class="text-sm text-blue-700 flex justify-between items-center">
                            <span>{{ $field->table_name }} → {{ $field->field_name }}</span>
                            <button class="text-red-500 hover:text-red-700 remove-field" data-field-id="{{ $field->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-4">
                <a href="{{ route('minireportb1.reports.addField', $report->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus"></i> Add Field
                </a>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            // Handle Delete Report
            $('.delete-report').on('click', function () {
                const reportId = $(this).data('report-id');
                if (confirm('Are you sure you want to delete this report?')) {
                    $.ajax({
                        url: `/minireportb1/reports/${reportId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function (response) {
                            alert('Report deleted successfully!');
                            location.reload(); // Reload the page to reflect changes
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            alert('Failed to delete report.');
                        }
                    });
                }
            });

            // Handle Remove Field
            $('.remove-field').on('click', function () {
                const fieldId = $(this).data('field-id');
                if (confirm('Are you sure you want to remove this field?')) {
                    $.ajax({
                        url: `/minireportb1/fields/${fieldId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function (response) {
                            alert('Field removed successfully!');
                            location.reload(); // Reload the page to reflect changes
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            alert('Failed to remove field.');
                        }
                    });
                }
            });
        });
    </script>
@endpush