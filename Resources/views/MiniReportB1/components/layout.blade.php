<div class="layout-container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between">
                        <h3 class="card-title">Report Layout Manager</h3>
                        <a id="designButton" class="btn-primary" href="{{ route('minireportb1.createlayout') }}"
                            style="margin-left: 10px; padding: 10px 20px; font-size: 16px; cursor: pointer; text-decoration: none; color: white; background-color: #007bff; border: none; border-radius: 4px;">
                            Design
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Layout Tools -->
                    <div class="mb-4">
                    </div>

                    <!-- Layout List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Layout Name</th>
                                    <th>Created Date</th>
                                    <th>Last Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Layout rows will be populated dynamically here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .layout-container {
        padding: 20px;
    }

    .layout-container .card {
        margin-bottom: 20px;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        padding: 20px;
    }

    .layout-container .table {
        margin-bottom: 0;
    }

    .layout-container .btn-sm {
        margin-right: 5px;
    }
</style>

<script>
    // Fetch layouts when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        fetchLayouts();
    });

    function fetchLayouts() {
        $.ajax({
            url: '/minireportb1/layouts',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Layouts fetched:', response);
                if (response.success) {
                    populateTable(response.layouts);
                } else {
                    console.error('Error fetching layouts:', response.error);
                    alert('Failed to load layout options.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching layouts:', error);
                alert('An error occurred while fetching layout options.');
            }
        });
    }

    function populateTable(layouts) {
    const tbody = document.querySelector('.table-responsive tbody');
    tbody.innerHTML = ''; // Clear existing rows

    layouts.forEach(layout => {
        const row = document.createElement('tr');

        const layoutNameCell = document.createElement('td');
        layoutNameCell.textContent = layout.layout_name;
        row.appendChild(layoutNameCell);

        const createdDateCell = document.createElement('td');
        createdDateCell.textContent = layout.created_at;
        row.appendChild(createdDateCell);

        const lastModifiedCell = document.createElement('td');
        lastModifiedCell.textContent = layout.updated_at;
        row.appendChild(lastModifiedCell);

        const actionsCell = document.createElement('td');
        const editLink = document.createElement('a');
        editLink.href = `/minireportb1/layouts/${layout.layout_name}/edit`; // Replace with your edit route
        editLink.className = 'btn btn-sm btn-info';
        editLink.innerHTML = '<i class="fas fa-edit"></i>';

        // Append the edit link to the actions cell
        actionsCell.appendChild(editLink);

        const deleteButton = document.createElement('button');
        deleteButton.className = 'btn btn-sm btn-danger';
        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
        deleteButton.onclick = () => deleteLayout(layout.layout_name); // Pass layout_name
        actionsCell.appendChild(deleteButton);

        row.appendChild(actionsCell);

        tbody.appendChild(row);
    });
}

    // Function to delete a layout
    function deleteLayout(layoutName) {
    if (confirm('Are you sure you want to delete this layout?')) {
        $.ajax({
            url: `/minireportb1/layouts/${layoutName}`, // Use layout_name instead of id
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token for Laravel
            },
            success: function(response) {
                if (response.success) {
                    alert('Layout deleted successfully.');
                    fetchLayouts(); // Refresh the table
                } else {
                    console.error('Error deleting layout:', response.error);
                    alert('Failed to delete layout.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deleting layout:', error);
                alert('An error occurred while deleting the layout.');
            }
        });
    }

  
}

</script>