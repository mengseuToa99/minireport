@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Database Tables</h2>

        <div>
            @foreach ($tablesWithColumns as $table => $columns)
                <div id="tableContainer_{{ $table }}" style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                    <div>
                        <input type="checkbox" class="table-checkbox" data-table="{{ $table }}"
                            id="table_{{ $table }}">
                        <label for="table_{{ $table }}">{{ $table }}</label>
                    </div>
                    <div style="margin-left: 20px; display: none;" id="fields_{{ $table }}">
                        @foreach ($columns as $column)
                            <div>
                                <input type="checkbox" class="field-checkbox" data-table="{{ $table }}"
                                    data-field="{{ $column }}" id="field_{{ $table }}_{{ $column }}">
                                <label for="field_{{ $table }}_{{ $column }}">{{ $column }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            <button id="confirmTableSelection"
                style="background: #4a90e2; color: white; padding: 8px 16px; margin-right: 10px;">
                Confirm Tables
            </button>
            <button id="generateReport" style="background: #50c878; color: white; padding: 8px 16px;">
                Generate Report
            </button>
        </div>

        <!-- Loading indicator -->
        <div id="loadingIndicator" style="display: none; text-align: center; margin-top: 20px;">
            <div
                style="display: inline-block; width: 30px; height: 30px; border: 3px solid #f3f3f3; 
                    border-top: 3px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;">
            </div>
        </div>

        <!-- Error message container -->
        <div id="errorContainer"
            style="display: none; margin-top: 20px; padding: 10px; background-color: #fee2e2; border: 1px solid #ef4444; border-radius: 4px;">
        </div>

        <!-- Results container -->
        <div id="queryResults" style="margin-top: 20px;"></div>
    </div>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const foreignKeyRelationships = @json($foreignKeyRelationships);
            const joinablePaths = @json($joinablePaths);
            let currentQuery = '';

            // Utility function to show/hide loading indicator
            function toggleLoading(show) {
                document.getElementById('loadingIndicator').style.display = show ? 'block' : 'none';
            }

            // Utility function to show error messages
            function showError(message) {
                const errorContainer = document.getElementById('errorContainer');
                errorContainer.textContent = message;
                errorContainer.style.display = 'block';
                setTimeout(() => {
                    errorContainer.style.display = 'none';
                }, 5000);
            }

            // Function to check if tables can be joined
            function canJoinTables(table1, table2, relationships, visited = new Set()) {
                // Direct foreign key relationship
                if (relationships[table1]?.some(fk => fk.referenced_table === table2) ||
                    relationships[table2]?.some(fk => fk.referenced_table === table1)) {
                    return true;
                }

                // Prevent infinite recursion
                if (visited.has(table1)) return false;
                visited.add(table1);

                // Check for indirect relationships through intermediate tables
                for (const [intermediateTable, fks] of Object.entries(relationships)) {
                    if (intermediateTable !== table1 && intermediateTable !== table2) {
                        if ((relationships[table1]?.some(fk => fk.referenced_table === intermediateTable) ||
                                relationships[intermediateTable]?.some(fk => fk.referenced_table === table1)) &&
                            canJoinTables(intermediateTable, table2, relationships, visited)) {
                            return true;
                        }
                    }
                }

                // If both tables have business_id, they can be joined
                const hasBusinessId = (table) => {
                    const columns = document.querySelectorAll(`#fields_${table} .field-checkbox`);
                    return Array.from(columns).some(checkbox => checkbox.dataset.field === 'business_id');
                };

                return hasBusinessId(table1) && hasBusinessId(table2);
            }

            // Function to build SQL query
            function buildQuery(selectedTables, selectedFields) {
                if (selectedTables.length === 0) return '';

                // For single table
                if (selectedTables.length === 1) {
                    const fields = selectedFields
                        .filter(f => f.table === selectedTables[0])
                        .map(f => `${f.table}.${f.field}`)
                        .join(', ');
                    return `SELECT ${fields} FROM ${selectedTables[0]}`;
                }

                // For multiple tables
                const fields = selectedFields
                    .map(f => `${f.table}.${f.field}`)
                    .join(', ');

                let query = `SELECT ${fields} FROM ${selectedTables[0]}`;

                // Build joins
                for (let i = 1; i < selectedTables.length; i++) {
                    const currentTable = selectedTables[i];
                    const previousTable = selectedTables[i - 1];

                    // Try to find join condition
                    let joinCondition = '';

                    // Check foreign keys from current table to previous table
                    if (foreignKeyRelationships[currentTable]) {
                        const fk = foreignKeyRelationships[currentTable].find(
                            rel => rel.referenced_table === previousTable
                        );
                        if (fk) {
                            joinCondition =
                                `${currentTable}.${fk.column} = ${previousTable}.${fk.referenced_column}`;
                        }
                    }

                    // Check foreign keys from previous table to current table
                    if (!joinCondition && foreignKeyRelationships[previousTable]) {
                        const fk = foreignKeyRelationships[previousTable].find(
                            rel => rel.referenced_table === currentTable
                        );
                        if (fk) {
                            joinCondition =
                                `${previousTable}.${fk.column} = ${currentTable}.${fk.referenced_column}`;
                        }
                    }

                    // If no direct foreign key relationship found, try to join through business_id
                    if (!joinCondition &&
                        hasColumn(currentTable, 'business_id') &&
                        hasColumn(previousTable, 'business_id')) {
                        joinCondition = `${currentTable}.business_id = ${previousTable}.business_id`;
                    }

                    // Add the join clause
                    if (joinCondition) {
                        query += ` INNER JOIN ${currentTable} ON ${joinCondition}`;
                    } else {
                        console.error(`No join condition found between ${previousTable} and ${currentTable}`);
                        return null;
                    }
                }

                return query;
            }

            // Helper function to check if a table has a specific column
            function hasColumn(table, columnName) {
                const fields = document.querySelectorAll(`#fields_${table} .field-checkbox`);
                return Array.from(fields).some(checkbox => checkbox.dataset.field === columnName);
            }

            // Add console logging for debugging
            document.getElementById('generateReport').addEventListener('click', function() {
                const selectedFields = [];
                const selectedTables = new Set();

                document.querySelectorAll('.field-checkbox:checked').forEach(checkbox => {
                    const table = checkbox.dataset.table;
                    const field = checkbox.dataset.field;
                    selectedFields.push({
                        table,
                        field
                    });
                    selectedTables.add(table);
                });

                if (selectedFields.length === 0) {
                    showError('Please select at least one field');
                    return;
                }

                const query = buildQuery(Array.from(selectedTables), selectedFields);
                console.log('Generated Query:', query); // Debug log

                if (!query) {
                    showError('Error: Unable to build query. Please confirm table selection first.');
                    return;
                }

                fetch('{{ route('reports.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fields: selectedFields,
                            query: query
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        console.log('API Response:', result); // Debug log
                        if (result.error) {
                            showError(result.error);
                        } else {
                            displayResults(result.data);
                        }
                    })
                    .catch(error => {
                        console.error('API Error:', error);
                        showError('Error generating report: ' + error.message);
                    });
            });

            // Function to format cell value for display
            function formatCellValue(value) {
                if (value === null || value === undefined) return '';
                if (typeof value === 'boolean') return value ? 'Yes' : 'No';
                if (value instanceof Date) return value.toLocaleString();
                return String(value);
            }

            // Function to display results in a table
            // Add this to your existing displayResults function

            function displayResults(data, pagination) {
                let resultsContainer = document.getElementById('queryResults');
                resultsContainer.innerHTML = '';

                if (!data || data.length === 0) {
                    resultsContainer.innerHTML = '<p class="text-gray-600">No results found</p>';
                    return;
                }

                // Create table
                const table = document.createElement('table');
                table.className = 'min-w-full divide-y divide-gray-200';
                table.style.borderCollapse = 'collapse';
                table.style.width = '100%';

                // Create header
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                Object.keys(data[0]).forEach(key => {
                    const th = document.createElement('th');
                    th.textContent = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    th.style.padding = '12px';
                    th.style.backgroundColor = '#f3f4f6';
                    th.style.borderBottom = '2px solid #e5e7eb';
                    th.style.textAlign = 'left';
                    th.style.fontWeight = 'bold';
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Create body
                const tbody = document.createElement('tbody');
                data.forEach((row, i) => {
                    const tr = document.createElement('tr');
                    tr.style.backgroundColor = i % 2 === 0 ? '#ffffff' : '#f9fafb';

                    Object.values(row).forEach(value => {
                        const td = document.createElement('td');
                        td.textContent = formatCellValue(value);
                        td.style.padding = '12px';
                        td.style.borderBottom = '1px solid #e5e7eb';
                        tr.appendChild(td);
                    });
                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);
                resultsContainer.appendChild(table);

                // Add pagination controls if pagination info exists
                if (pagination) {
                    const paginationContainer = document.createElement('div');
                    paginationContainer.style.marginTop = '20px';
                    paginationContainer.style.display = 'flex';
                    paginationContainer.style.justifyContent = 'space-between';
                    paginationContainer.style.alignItems = 'center';

                    // Results info
                    const infoDiv = document.createElement('div');
                    infoDiv.textContent = `Showing ${data.length} of ${pagination.total} results`;
                    paginationContainer.appendChild(infoDiv);

                    // Page controls
                    const controlsDiv = document.createElement('div');

                    // Previous page button
                    if (pagination.current_page > 1) {
                        const prevButton = document.createElement('button');
                        prevButton.textContent = 'Previous';
                        prevButton.style.marginRight = '10px';
                        prevButton.style.padding = '8px 16px';
                        prevButton.style.backgroundColor = '#4a90e2';
                        prevButton.style.color = 'white';
                        prevButton.style.border = 'none';
                        prevButton.style.borderRadius = '4px';
                        prevButton.onclick = () => generateReport(pagination.current_page - 1);
                        controlsDiv.appendChild(prevButton);
                    }

                    // Page numbers
                    const pageSpan = document.createElement('span');
                    pageSpan.textContent = `Page ${pagination.current_page} of ${pagination.last_page}`;
                    pageSpan.style.margin = '0 10px';
                    controlsDiv.appendChild(pageSpan);

                    // Next page button
                    if (pagination.current_page < pagination.last_page) {
                        const nextButton = document.createElement('button');
                        nextButton.textContent = 'Next';
                        nextButton.style.marginLeft = '10px';
                        nextButton.style.padding = '8px 16px';
                        nextButton.style.backgroundColor = '#4a90e2';
                        nextButton.style.color = 'white';
                        nextButton.style.border = 'none';
                        nextButton.style.borderRadius = '4px';
                        nextButton.onclick = () => generateReport(pagination.current_page + 1);
                        controlsDiv.appendChild(nextButton);
                    }

                    paginationContainer.appendChild(controlsDiv);
                    resultsContainer.appendChild(paginationContainer);
                }
            }

            // Modify your existing generateReport function to handle pagination
            function generateReport(page = 1) {
                const selectedFields = [];
                const selectedTables = new Set();

                document.querySelectorAll('.field-checkbox:checked').forEach(checkbox => {
                    const table = checkbox.dataset.table;
                    const field = checkbox.dataset.field;
                    selectedFields.push({
                        table,
                        field
                    });
                    selectedTables.add(table);
                });

                if (selectedFields.length === 0) {
                    showError('Please select at least one field');
                    return;
                }

                const query = buildQuery(Array.from(selectedTables), selectedFields);
                if (!query) {
                    showError('Error: Unable to build query. Please confirm table selection first.');
                    return;
                }

                toggleLoading(true);
                fetch('{{ route('reports.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fields: selectedFields,
                            query: query,
                            page: page,
                            per_page: 100 // You can adjust this value or make it configurable
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        toggleLoading(false);
                        if (result.error) {
                            showError(result.error);
                        } else {
                            document.getElementById('errorContainer').style.display = 'none';
                            displayResults(result.data, result.pagination);
                        }
                    })
                    .catch(error => {
                        toggleLoading(false);
                        showError('Error generating report: ' + error.message);
                    });
            }

            // Event Listeners
            document.querySelectorAll('.table-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const selectedTable = this.dataset.table;
                    const fieldsContainer = document.getElementById(`fields_${selectedTable}`);
                    fieldsContainer.style.display = this.checked ? 'block' : 'none';

                    // Reset error container
                    document.getElementById('errorContainer').style.display = 'none';

                    // Show/hide available joinable tables
                    document.querySelectorAll('[id^="tableContainer_"]').forEach(container => {
                        container.style.display = 'block';
                        if (this.checked) {
                            const tableName = container.id.replace('tableContainer_', '');
                            if (tableName !== selectedTable && !canJoinTables(selectedTable,
                                    tableName, foreignKeyRelationships)) {
                                container.style.display = 'none';
                            }
                        }
                    });
                });
            });

            document.getElementById('confirmTableSelection').addEventListener('click', function() {
                toggleLoading(true);
                const selectedTables = [];
                document.querySelectorAll('.table-checkbox:checked').forEach(checkbox => {
                    selectedTables.push(checkbox.dataset.table);
                });

                document.querySelectorAll('[id^="fields_"]').forEach(div => {
                    const table = div.id.replace('fields_', '');
                    div.style.display = selectedTables.includes(table) ? 'block' : 'none';
                });

                if (selectedTables.length > 1) {
                    fetch('{{ route('get-join-query') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                tables: selectedTables
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            toggleLoading(false);
                            if (data.error) {
                                showError(data.error);
                            } else {
                                currentQuery = data.query;
                            }
                        })
                        .catch(error => {
                            toggleLoading(false);
                            showError('Error confirming table selection: ' + error.message);
                        });
                } else {
                    toggleLoading(false);
                }
            });

            document.getElementById('generateReport').addEventListener('click', function() {
                const selectedFields = [];
                const selectedTables = new Set();

                document.querySelectorAll('.field-checkbox:checked').forEach(checkbox => {
                    const table = checkbox.dataset.table;
                    const field = checkbox.dataset.field;
                    selectedFields.push({
                        table,
                        field
                    });
                    selectedTables.add(table);
                });

                if (selectedFields.length === 0) {
                    showError('Please select at least one field');
                    return;
                }

                const query = buildQuery(Array.from(selectedTables), selectedFields);
                if (!query) {
                    showError('Error: Unable to build query. Please confirm table selection first.');
                    return;
                }

                toggleLoading(true);
                fetch('{{ route('reports.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            fields: selectedFields,
                            query: query
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        toggleLoading(false);
                        if (result.error) {
                            showError(result.error);
                        } else {
                            document.getElementById('errorContainer').style.display = 'none';
                            displayResults(result.data);
                        }
                    })
                    .catch(error => {
                        toggleLoading(false);
                        showError('Error generating report: ' + error.message);
                    });
            });
        });
    </script>
@endsection
