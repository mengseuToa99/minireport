<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Multiple Reports and Select Fields</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .table-accordion {
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .table-accordion:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .field-list {
            display: none;
            margin-left: 20px;
        }

        .selected {
            background-color: #e2e8f0;
        }

        .hidden {
            display: none;
        }

        .highlight {
            background-color: yellow;
            /* Bright yellow background */
            font-weight: bold;
            /* Bold text */
            color: #000;
            /* Black text for better contrast */
            padding: 2px 4px;
            /* Add some padding for better visibility */
            border-radius: 3px;
            /* Rounded corners */
        }

        .modal {
            transition: opacity 0.25s ease;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Highlight selected tables and fields */
        .modal-table-checkbox:checked+.table-name {
            font-weight: bold;
            color: #1e40af;
        }

        .modal-field-checkbox:checked+label {
            font-weight: bold;
            color: #1e40af;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-blue-500 to-blue-600 min-h-screen p-8">

    <a href="{{ route('MiniReportB1.create') }}" class="btn btn-sm btn-primary"
        style="margin: 10px; font-size: 15px; color: white;">
        <i class="fa fa-arrow-left"></i> Back
    </a>

    <div class="max-w-5xl mx-auto bg-white shadow-2xl rounded-lg p-6">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-800">
            <i class="fas fa-layer-group mr-2"></i>Select Element
        </h1>

        <!-- Report Management -->
        <div id="reportManagement" class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-blue-700">Menu</h2>
            <button type="button" id="addReportButton"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center">
                <i class="fas fa-plus mr-2"></i>Add Menu
            </button>
        </div>

        <!-- Report Containers -->
        <div id="reportContainers" class="space-y-6"></div>

        <!-- Template for Report Container -->
        <template id="reportTemplate">
            <div class="report-container p-4 border-2 border-blue-200 rounded-lg bg-blue-50 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-blue-800">Report <span class="report-number"></span></h2>
                    <button type="button"
                        class="text-sm text-red-600 hover:text-red-800 removeReportButton flex items-center">
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-blue-800" for="reportName">Report Name</label>
                    <input type="text" name="report_name"
                        class="report-name w-full p-2 border-2 border-blue-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                        placeholder="Enter report name (e.g., Sales)" required>
                </div>
                <div class="mb-4">
                    <button type="button"
                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition duration-300 selectFieldsButton flex items-center">
                        <i class="fas fa-tasks mr-2"></i>Select Tables and Fields
                    </button>
                </div>
                <!-- Selected Fields Preview -->
                <div class="selected-fields-preview p-4 border-2 border-blue-200 rounded-lg bg-white hidden">
                    <h3 class="text-md font-semibold text-blue-800 mb-3">Selected Fields</h3>
                    <ul class="selectedFieldsList space-y-2"></ul>
                </div>
            </div>
        </template>

        <!-- Modal for Selecting Tables and Fields -->
        <div id="fieldsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg w-11/12 md:w-3/4 lg:w-1/2 p-6 relative fade-in">
                <button type="button"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 closeModalButton">
                    <i class="fas fa-times text-2xl"></i>
                </button>
                <h2 class="text-2xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-tasks mr-2"></i>Select Tables and Fields
                </h2>
                <!-- Search Bar within Modal -->
                <div class="mb-4">
                    <input type="text" id="modalSearch" placeholder="Search tables or fields..."
                        class="w-full p-3 border-2 border-blue-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                </div>
                <!-- Tables List within Modal -->
                <div id="modalTablesList" class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach ($tablesWithFields as $tableName => $fields)
                        <div class="table-accordion p-4 border-2 border-blue-200 rounded-lg bg-gradient-to-r from-blue-50 to-blue-100"
                            data-table="{{ $tableName }}">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" id="{{ $tableName }}" name="tables[]"
                                        value="{{ $tableName }}"
                                        class="modal-table-checkbox form-checkbox h-5 w-5 text-blue-600">
                                    <span
                                        class="text-lg font-semibold text-blue-800 table-name">{{ $tableName }}</span>
                                </label>
                                <button type="button"
                                    class="text-sm text-blue-600 hover:text-blue-800 select-all-fields-modal"
                                    data-table="{{ $tableName }}">
                                    <i class="fas fa-check-circle mr-1"></i>Select All
                                </button>
                            </div>
                            <div id="{{ $tableName }}FieldsModal" class="field-list mt-3 space-y-2">
                                @foreach ($fields as $field)
                                    <div class="flex items-center space-x-3 ml-6" data-field="{{ $field->Field }}">
                                        <input type="checkbox" id="{{ $field->Field }}"
                                            name="fields[{{ $tableName }}][]" value="{{ $field->Field }}"
                                            class="modal-field-checkbox form-checkbox h-4 w-4 text-blue-600">
                                        <label for="{{ $field->Field }}"
                                            class="text-sm text-blue-700">{{ $field->Field }} <span
                                                class="text-gray-500">({{ $field->Type }})</span></label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Modal Actions -->
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-300 closeModalButton">
                        Cancel
                    </button>
                    <button type="button"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 saveFieldsButton">
                        Save Selection
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-center">
            <button type="button" id="saveReportsButton"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300 flex items-center">
                <i class="fas fa-save mr-2"></i>Save Menu
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let reportCounter = 0;
            let currentReport = null;

            const $addReportButton = $('#addReportButton');
            const $reportContainers = $('#reportContainers');
            const $reportTemplate = $('#reportTemplate').html();
            const $fieldsModal = $('#fieldsModal');
            const $closeModalButtons = $('.closeModalButton');
            const $saveFieldsButton = $('.saveFieldsButton');
            const $modalSearch = $('#modalSearch');
            const $modalTablesList = $('#modalTablesList');
            const $saveReportsButton = $('#saveReportsButton');

            // Function to open modal
            const openModal = () => {
                $fieldsModal.removeClass('hidden');
            };

            // Function to close modal
            const closeModal = () => {
                $fieldsModal.addClass('hidden');
                resetModalSelections();
            };

            // Function to reset modal selections
            const resetModalSelections = () => {
                $('.modal-table-checkbox:checked').prop('checked', false);
                $('.modal-field-checkbox:checked').prop('checked', false);
                $modalSearch.val('');
                filterModalTables('');
            };

            // Event listeners for opening and closing modal
            $(document).on('click', '.selectFieldsButton', function() {
                currentReport = $(this).closest('.report-container');
                openModal();
            });

            $closeModalButtons.on('click', closeModal);

            // Prevent modal from closing when clicking inside it
            $fieldsModal.on('click', function(e) {
                e.stopPropagation(); // Prevent clicks inside the modal from closing it
            });

            // Add Report Button Click
            $addReportButton.on('click', function() {
                reportCounter++;
                const $newReport = $($reportTemplate);
                $newReport.find('.report-number').text(reportCounter);

                // Add event listener to remove button
                $newReport.find('.removeReportButton').on('click', function() {
                    $newReport.remove();
                    updateReportNumbers();
                });

                // Add event listener to select fields button
                $newReport.find('.selectFieldsButton').on('click', function() {
                    currentReport = $newReport;
                    openModal();
                });

                $reportContainers.append($newReport);
            });

            // Function to update report numbers after removal
            const updateReportNumbers = () => {
                const $reports = $('.report-container');
                reportCounter = 0;
                $reports.each(function(index) {
                    reportCounter = index + 1;
                    $(this).find('.report-number').text(reportCounter);
                });
            };

            // Handle Select All Fields in Modal
            $(document).on('click', '.select-all-fields-modal', function() {
                const tableName = $(this).data('table');
                const $tableCheckbox = $('#' + tableName);
                const $fields = $('#' + tableName + 'FieldsModal .modal-field-checkbox');
                $tableCheckbox.prop('checked', true);
                $fields.prop('checked', true);
            });

            // Save Fields from Modal to Current Report
            $saveFieldsButton.on('click', function() {
                if (!currentReport) return;

                const selectedFields = [];
                const $checkedTables = $fieldsModal.find('.modal-table-checkbox:checked');

                $checkedTables.each(function() {
                    const tableName = $(this).val();
                    const $fields = $fieldsModal.find(
                        `input[name="fields[${tableName}][]"]:checked`);
                    $fields.each(function() {
                        selectedFields.push({
                            table: tableName,
                            field: $(this).val(),
                        });
                    });
                });

                // Update the selected fields preview in the current report
                const $selectedFieldsList = currentReport.find('.selectedFieldsList');
                $selectedFieldsList.empty();

                selectedFields.forEach(item => {
                    const $listItem = $('<li>').addClass('text-sm text-blue-700').text(
                        `${item.table} → ${item.field}`);
                    $selectedFieldsList.append($listItem);
                });

                // Toggle visibility based on selections
                const $selectedFieldsPreview = currentReport.find('.selected-fields-preview');
                $selectedFieldsPreview.toggleClass('hidden', selectedFields.length === 0);

                closeModal(); // Close modal only when Save Selection is clicked
            });

            // Search Functionality in Modal
            $modalSearch.on('input', function() {
                filterModalTables($(this).val());
            });

            const filterModalTables = (query) => {
                const lowerQuery = query.toLowerCase();
                const $tables = $modalTablesList.find('.table-accordion');

                $tables.each(function() {
                    const $tableName = $(this).find('.table-name');
                    const $fields = $(this).find('.field-list label');
                    const tableNameText = $tableName.text().toLowerCase();
                    let matches = tableNameText.includes(lowerQuery);

                    // Highlight matching table name
                    if (lowerQuery && matches) {
                        const regex = new RegExp(`(${lowerQuery})`, 'gi');
                        const highlightedText = $tableName.text().replace(regex,
                            '<span class="highlight">$1</span>');
                        $tableName.html(highlightedText);
                    } else {
                        $tableName.text($tableName.text()); // Reset to original text
                    }

                    // Check if any field matches
                    $fields.each(function() {
                        const $fieldLabel = $(this);
                        const fieldText = $fieldLabel.text().toLowerCase();
                        const fieldMatches = fieldText.includes(lowerQuery);

                        if (lowerQuery && fieldMatches) {
                            const regex = new RegExp(`(${lowerQuery})`, 'gi');
                            const highlightedText = $fieldLabel.text().replace(regex,
                                '<span class="highlight">$1</span>');
                            $fieldLabel.html(highlightedText);
                        } else {
                            $fieldLabel.text($fieldLabel.text()); // Reset to original text
                        }

                        matches = matches || fieldMatches;
                    });

                    // Show or hide the table based on matches
                    $(this).toggle(matches);

                    // If the table matches, show all fields
                    if (matches) {
                        $(this).find('.field-list').show();
                    } else {
                        // If the table does not match, hide the table and its fields
                        $(this).hide();
                    }
                });
            };

            // Handle Accordion Toggle for Tables in Modal
            $modalTablesList.on('click', '.table-accordion', function(e) {
                // Only toggle the field list if the click is on the table name or header
                if ($(e.target).hasClass('table-name') || $(e.target).closest('.table-name').length) {
                    const $fieldList = $(this).find('.field-list');
                    $fieldList.toggle();
                }
            });

            // Prevent field list from closing when clicking on fields or checkboxes
            $modalTablesList.on('click', '.field-list, .field-list *', function(e) {
                e.stopPropagation(); // Stop event propagation to prevent accordion toggle
            });

            $saveReportsButton.on('click', function() {
                const $reports = $('.report-container');
                const reportsData = [];

                $reports.each(function() {
                    const reportName = $(this).find('.report-name').val();
                    const selectedFields = [];

                    $(this).find('.selectedFieldsList li').each(function() {
                        const [tableName, fieldName] = $(this).text().split(' → ');
                        selectedFields.push({
                            table_name: tableName.trim(),
                            field_name: fieldName.trim(),
                        });
                    });

                    reportsData.push({
                        name: reportName,
                        fields: selectedFields,
                    });
                });

                // Log the data to the console
                console.log('Data to be sent:', reportsData);

                // Send data to the backend
                $.ajax({
                    url: '/minireportb1/store-menu',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(reportsData),
                    success: function(data) {
                        alert('Reports saved successfully!');
                        console.log('Response from server:', data);
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        alert('Failed to save reports: ' + (error.responseJSON?.message ||
                            'Unknown error'));
                    }
                });
            });
        });
    </script>


</body>

</html>
