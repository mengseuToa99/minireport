@extends('layouts.app')
@section('title', __('minireportb1::minireportb1.MiniReportB1'))
@section('content')
    <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">


    <!-- Main content -->
    <section class="content no-print">
        <div class="row" style="height: 200px; weight: auto;">
            <div class="tw-flex tw-items-center tw-gap-4" style="margin-top: 60px; margin-left: 60px;">


                <img src="{{ asset($business_logo) }}" alt="{{ e($business_name) }} Logo"
                    class="img img-thumbnail img-logo tw-flex-shrink-0 w-16 h-16 rounded-lg shadow-sm" loading="lazy">


                <a href="{{ route('home') }}" class="tw-flex-1 tw-min-w-0">
                    <p class="tw-font-medium tw-text-black tw-font-bold tw-truncate" style="font-size: 26px;">
                        {{ $business_name }}
                    </p>
                </a>
            </div>

        </div>

        <ul class="nav nav-tabs custom-tabs" id="reportTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="standard-tab" data-toggle="tab" href="#standard" role="tab"
                    aria-controls="standard" aria-selected="true">Standard Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="custom-tab" data-toggle="tab" href="#custom" role="tab" aria-controls="custom"
                    aria-selected="false">Custom Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="layout-tab" data-toggle="tab" href="#layout" role="tab" aria-controls="layout"
                    aria-selected="false">Header Report</a>
            </li>

             <li class="nav-item">
                <a class="nav-link" id="tax-tab" data-toggle="tab" href="#tax" role="tab" aria-controls="tax"
                    aria-selected="false">Tax-Gov</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="mony-tab" data-toggle="tab" href="#mony" role="tab" aria-controls="mony"
                    aria-selected="false">Mony</a>
            </li>

    

        </ul>
        <div class="tab-content">
           

            <div class="tab-pane fade show active" id="standard" role="tabpanel" aria-labelledby="standard-tab">

                    @include('minireportb1::MiniReportB1.StandardReport.index')      
            </div>



            <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">

                <div class="report-containered">
                    

                    <button class="btn" onclick="showCreateFolderModal()">
                        create folder
                    </button>

                    <button class="btn" id="createFileButton">create report</button>

                </div>


                <!-- Your existing report sections code -->
                @foreach ($folders->where('type', 'report_section') as $folder)
                    <div class="menu-card" data-folder-id="{{ $folder->id }}">
                        <div class="section-header">
                            <div class="section-header-content">
                                <div class="section-title" onclick="toggleSection('section_{{ $folder->id }}')">
                                    <i class="fas fa-chevron-down"></i> {{ $folder->folder_name }}
                                </div>
                                <div class="section-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-link" onclick="toggleDropdown({{ $folder->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu" id="dropdown-{{ $folder->id }}">
                                            <a class="dropdown-item" href="#"
                                                onclick="moveFolder({{ $folder->id }}, 'up')">
                                                <i class="fas fa-arrow-up"></i> Move Up
                                            </a>
                                            <a class="dropdown-item" href="#"
                                                onclick="moveFolder({{ $folder->id }}, 'down')">
                                                <i class="fas fa-arrow-down"></i> Move Down
                                            </a>
                                            <a class="dropdown-item" href="#"
                                                onclick="showRenameModal({{ $folder->id }}, '{{ $folder->folder_name }}')">
                                                <i class="fas fa-edit"></i> Rename
                                            </a>
                                            <a class="dropdown-item" href="#"
                                                onclick="deleteFolder({{ $folder->id }})">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row report-container" id="section_{{ $folder->id }}">
                            @foreach ($files->where('parent_id', $folder->id) as $file)
                                <div class="col-md-6 report-item">
                                    <div class="report-box">
                                        <a href="{{ route('MiniReportB1.viewFile', $file->id) }}" class="report-link"
                                            style="text-decoration: none;">
                                            <span>{{ $file->file_name }}</span>
                                        </a>
                                        <div class="section-actions">
                                            <div class="dropdown">
                                                <button class="btn btn-link"
                                                    onclick="toggleFileDropdown({{ $file->id }})">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu" id="file-dropdown-{{ $file->id }}">
                                                    <a class="dropdown-item"
                                                        href="{{ route('MiniReportB1.viewFile', $file->id) }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="showFileRenameModal({{ $file->id }}, '{{ $file->file_name }}')">
                                                        <i class="fas fa-edit"></i> Rename
                                                    </a>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="showMoveFileModal({{ $file->id }})">
                                                        <i class="fas fa-exchange-alt"></i> Move
                                                    </a>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="deleteFile({{ $file->id }})">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>

             {{--  --}}
              <div class="tab-pane fade" id="layout" role="tabpanel" aria-labelledby="layout-tab">
                @include('minireportb1::MiniReportB1.components.layout')
            </div>

            <div class="tab-pane fade" id="tax" role="tabpanel" aria-labelledby="tax-tab">
                    @include('minireportb1::MiniReportB1.gov_tax.index')
            </div>

            <div class="tab-pane fade" id="mony" role="tabpanel" aria-labelledby="mony-tab">
                    @include('minireportb1::MiniReportB1.mony.index')
            </div>
        </div>
    </section>


    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Report Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createFolderForm" onsubmit="event.preventDefault(); createFolder();">
                        <div class="form-group">
                            <label for="folderName">Section Name</label>
                            <input type="text" class="form-control" id="folderName" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="createFolder()">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create File Modal -->
    <div class="modal fade" id="createFileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createFileForm" onsubmit="event.preventDefault(); createFile();">
                        <div class="form-group">
                            <label for="fileName">File Name</label>
                            <input type="text" class="form-control" id="fileName" required>
                        </div>
                        <div class="form-group">
                            <label for="parentFolder">Select Section</label>
                            <select class="form-control" id="parentFolder" required>
                                @foreach ($folders->where('type', 'report_section') as $folder)
                                    <option value="{{ $folder->id }}">{{ $folder->folder_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="createFile()">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Rename Modal -->
    <div class="modal fade" id="renameFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="renameFolderForm">
                        <input type="hidden" id="renameFolderId">
                        <div class="form-group">
                            <label for="newFolderName">New Name</label>
                            <input type="text" class="form-control" id="newFolderName" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="renameFolder()">Rename</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Move File Modal -->
    <div class="modal fade" id="moveFileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Move File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="moveFileForm">
                        <input type="hidden" id="moveFileId">
                        <div class="form-group">
                            <label for="newFolderSelect">Select Destination Folder</label>
                            <select class="form-control" id="newFolderSelect">
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="moveFile()">Move</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add File Rename Modal -->
    <div class="modal fade" id="renameFileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="renameFileForm">
                        <input type="hidden" id="renameFileId">
                        <div class="form-group">
                            <label for="newFileName">New Name</label>
                            <input type="text" class="form-control" id="newFileName" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="renameFile()">Rename</button>
                </div>
            </div>
        </div>
    </div>

    </div>






    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Single click handler for tab switching
            $('#reportTabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');

                // Get the target tab pane ID
                var targetId = $(this).attr('href');

                // Hide all tab panes
                $('.tab-pane').removeClass('show active');

                // Show the selected tab pane
                $(targetId).addClass('show active');
                
                // Store the active tab ID in localStorage
                localStorage.setItem('activeTab', $(this).attr('id'));
            });

            // Check if there's a stored active tab
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                // Activate the stored tab
                $('#' + activeTab).tab('show');
                
                // Show the corresponding tab content
                var targetId = $('#' + activeTab).attr('href');
                $('.tab-pane').removeClass('show active');
                $(targetId).addClass('show active');
            } else {
                // Show the first tab by default
                $('#reportTabs a[href="#standard"]').tab('show');
            }
        });


        // Filter function for reports
        function filterReports(val) {
            val = val.toUpperCase();
            let reportItems = document.getElementsByClassName('report-item');
            Array.prototype.forEach.call(reportItems, item => {
                let title = item.getAttribute('data-title').toUpperCase();
                item.style.display = title.includes(val) ? "block" : "none";
            });
        }

        // Toggle function to expand or collapse sections
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            section.style.display = section.style.display === "none" ? "block" : "none";
        }

        // Function to toggle favorite icon
        function toggleFavorite(element) {
            if (element.classList.contains('text-success')) {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
            } else {
                element.classList.remove('text-muted');
                element.classList.add('text-success');
            }
        }

        // Attach the filter function to the window object
        window.filterReports = filterReports;
        window.toggleSection = toggleSection;
        window.toggleFavorite = toggleFavorite;

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Ensure Bootstrap's dropdown functionality is initialized
        $('[data-toggle="dropdown"]').dropdown();

        // Handle dropdown toggle
        $(document).on('click', '.action-dots', function(e) {
            e.stopPropagation();
            const dropdown = $(this).siblings('.dropdown-menu');
            $('.dropdown-menu').not(dropdown).removeClass('show');
            dropdown.toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.section-actions').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Initialize the FAB menu
        $('.main-fab').click(function() {
            $('.floating-action-button').toggleClass('active');
        });

        // Close FAB menu when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.floating-action-button').length) {
                $('.floating-action-button').removeClass('active');
            }
        });

        // Add these functions to your existing script
        function showCreateFolderModal() {
            $('#folderName').val(''); // Clear the input
            $('#createFolderModal').modal('show');
        }

        function showCreateFileModal() {
            window.location.href = '{{ route('minireportb1.saleReport') }}';
        }

        function createFolder() {
            const folderName = $('#folderName').val().trim();

            if (!folderName) {
                alert('Please enter a section name');
                return;
            }

            $.ajax({
                url: '{{ route('minireportb1.create.folder') }}',
                type: 'POST',
                data: {
                    folder_name: folderName,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.msg || 'Failed to create section');
                    }
                },
                error: function(xhr) {
                    alert('Error creating section');
                }
            });

            $('#createFolderModal').modal('hide');
        }

        // Add delete handlers
        $(document).on('click', '.delete-folder', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this folder?')) {
                const folderId = $(this).data('folder-id');
                $.ajax({
                    url: '{{ route('minireportb1.delete.folder') }}',
                    type: 'DELETE',
                    data: {
                        folder_id: folderId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete folder');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.delete-file', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this file?')) {
                const fileId = $(this).data('file-id');
                $.ajax({
                    url: '{{ route('minireportb1.delete.file') }}',
                    type: 'DELETE',
                    data: {
                        file_id: fileId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete file');
                        }
                    }
                });
            }
        });

        // Toggle FAB menu
        document.getElementById('fab').addEventListener('click', function(e) {
            if (e.target.classList.contains('main-fab')) {
                this.classList.toggle('active');
            }
        });

        // Add these to your existing script section
        $(document).on('click', '.rename-folder', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const folderId = $(this).data('folder-id');
            const folderName = $(this).data('folder-name');

            $('#renameFolderId').val(folderId);
            $('#newFolderName').val(folderName);
            $('#renameFolderModal').modal('show');
        });

        function renameFolder() {
            const folderId = $('#renameFolderId').val();
            const newName = $('#newFolderName').val();

            if (!newName) {
                alert('Please enter a new name');
                return;
            }

            $.ajax({
                url: '{{ route('minireportb1.rename.folder') }}',
                type: 'POST',
                data: {
                    folder_id: folderId,
                    folder_name: newName,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to rename section');
                    }
                },
                error: function() {
                    alert('Error renaming section');
                }
            });
            $('#renameFolderModal').modal('hide');
        }

        $(document).on('click', '.delete-folder', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (confirm('Are you sure you want to delete this section and all its contents?')) {
                const folderId = $(this).data('folder-id');
                $.ajax({
                    url: '{{ route('minireportb1.delete.folder') }}',
                    type: 'DELETE',
                    data: {
                        folder_id: folderId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete section');
                        }
                    }
                });
            }
        });

        function toggleDropdown(folderId) {
            event.stopPropagation();
            const dropdown = document.getElementById(`dropdown-${folderId}`);
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== `dropdown-${folderId}`) {
                    menu.classList.remove('show');
                }
            });
            dropdown.classList.toggle('show');
        }

        function showRenameModal(folderId, folderName) {
            $('#renameFolderId').val(folderId);
            $('#newFolderName').val(folderName);
            $('#renameFolderModal').modal('show');
            // Hide the dropdown
            document.getElementById(`dropdown-${folderId}`).classList.remove('show');
        }

        function deleteFolder(folderId) {
            if (confirm('Are you sure you want to delete this section and all its contents?')) {
                $.ajax({
                    url: '{{ route('minireportb1.delete.folder') }}',
                    type: 'DELETE',
                    data: {
                        folder_id: folderId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete section');
                        }
                    }
                });
            }
        }

        function moveFolder(folderId, direction) {
            const currentFolder = $(`.menu-card[data-folder-id="${folderId}"]`);

            if (direction === 'up' && currentFolder.prev('.menu-card').length) {
                currentFolder.insertBefore(currentFolder.prev('.menu-card'));
                saveFolderOrder();
            } else if (direction === 'down' && currentFolder.next('.menu-card').length) {
                currentFolder.insertAfter(currentFolder.next('.menu-card'));
                saveFolderOrder();
            }

            // Hide the dropdown
            $(`#dropdown-${folderId}`).removeClass('show');
        }

        function saveFolderOrder() {
            const folderOrder = [];
            $('.menu-card').each(function() {
                folderOrder.push($(this).data('folder-id'));
            });

            $.ajax({
                url: '{{ route('minireportb1.update.folder.order') }}',
                type: 'POST',
                data: {
                    folder_order: folderOrder,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.success) {
                        alert('Failed to update folder order');
                    }
                },
                error: function() {
                    alert('Error updating folder order');
                }
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.section-actions')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        function toggleFileDropdown(fileId) {
            event.stopPropagation();
            const dropdown = document.getElementById(`file-dropdown-${fileId}`);
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== `file-dropdown-${fileId}`) {
                    menu.classList.remove('show');
                }
            });
            dropdown.classList.toggle('show');
        }

        function showFileRenameModal(fileId, fileName) {
            $('#renameFileId').val(fileId);
            $('#newFileName').val(fileName);
            $('#renameFileModal').modal('show');
            // Hide the dropdown
            document.getElementById(`file-dropdown-${fileId}`).classList.remove('show');
        }

        function renameFile() {
            const fileId = $('#renameFileId').val();
            const newName = $('#newFileName').val().trim(); // Trim whitespace

            // Validate new name
            if (!newName) {
                alert('Please enter a new name');
                return;
            }

            // Show loading indicator (optional)
            $('#renameFileModal').find('.modal-footer').append(
                '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');

            $.ajax({
                url: '{{ route('minireportb1.rename.file') }}',
                type: 'POST',
                data: {
                    file_id: fileId,
                    file_name: newName, // Corrected parameter name (was folder_name)
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Hide loading indicator
                    $('#renameFileModal').find('.spinner-border').remove();

                    if (response.success) {
                        // Show success message
                        // alert('File renamed successfully');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        // Show detailed error message from the server
                        alert(response.msg || 'Failed to rename file');
                    }
                },
                error: function(xhr) {
                    // Hide loading indicator
                    $('#renameFileModal').find('.spinner-border').remove();

                    // Handle AJAX errors
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        alert('Error: ' + xhr.responseJSON.msg);
                    } else {
                        alert('An unexpected error occurred. Please try again.');
                    }
                }
            });

            // Hide the modal
            $('#renameFileModal').modal('hide');
        }

        function showMoveFileModal(fileId) {
            $('#moveFileId').val(fileId);

            // Get available folders
            $.ajax({
                url: '{{ route('minireportb1.folders.list') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let folderSelect = $('#newFolderSelect');
                        folderSelect.empty();

                        response.data.forEach(folder => {
                            folderSelect.append(
                                `<option value="${folder.id}">${folder.folder_name}</option>`);
                        });

                        $('#moveFileModal').modal('show');
                    }
                }
            });

            // Hide the dropdown
            document.getElementById(`file-dropdown-${fileId}`).classList.remove('show');
        }

        function moveFile() {
            const fileId = $('#moveFileId').val();
            const newFolderId = $('#newFolderSelect').val();

            $.ajax({
                url: '{{ route('minireportb1.move.file') }}',
                type: 'POST',
                data: {
                    file_id: fileId,
                    parent_id: newFolderId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to move file');
                    }
                }
            });
            $('#moveFileModal').modal('hide');
        }

        function deleteFile(fileId) {
            if (confirm('Are you sure you want to delete this file?')) {
                $.ajax({
                    url: '{{ route('minireportb1.delete.file') }}',
                    type: 'DELETE',
                    data: {
                        file_id: fileId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete file');
                        }
                    }
                });
            }
        }
    </script>

    <script>
        function navigateToReport(reportType) {
            if (reportType) {
                // Map the dropdown values to their corresponding routes
                const reportRoutes = {
                    'payroll': '{{ route('minireportb1.payroll') }}',
                    'saleReport': '{{ route('minireportb1.saleReport') }}',
                    'purchaseReport': '{{ route('minireportb1.purchaseReport') }}',
                    'productReport': '{{ route('minireportb1.productReport') }}',
                    'payroll1': '{{ route('minireportb1.payroll1') }}',
                    'payroll2': '{{ route('minireportb1.payroll2') }}',
                    'expenseReport': '{{ route('minireportb1.expenseReport') }}',
                };

                // Navigate to the selected report
                if (reportRoutes[reportType]) {
                    window.location.href = reportRoutes[reportType];
                }
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            function navigateToPage(url) {
                window.location.href = url;
            }

            $('#createFileButton').on('click', function() {
                navigateToPage('{{ route('minireport_createfile') }}'); // Using Laravel route
            });
        });
    </script>

    <!-- Tab navigation handler -->
    <script>
        $(document).ready(function() {
            // Function to handle tab switching
            function switchTab(tabId) {
                // Remove active class from all tabs and panes
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');
                
                // Add active class to selected tab and pane
                $(`#${tabId}`).addClass('active');
                $(`#${tabId.replace('-tab', '')}`).addClass('show active');
                
                // Store the active tab in localStorage
                localStorage.setItem('activeTab', tabId);
            }

            // Handle tab clicks
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                const tabId = $(this).attr('id');
                switchTab(tabId);
            });

            // Check for hash in URL or stored tab
            const hash = window.location.hash.substring(1);
            const storedTab = localStorage.getItem('activeTab');
            
            if (hash) {
                switchTab(`${hash}-tab`);
            } else if (storedTab) {
                switchTab(storedTab);
            } else {
                // Default to standard tab
                switchTab('standard-tab');
            }

            // Update URL hash when tabs are clicked
            $('.nav-link').on('shown.bs.tab', function(e) {
                const id = e.target.id.replace('-tab', '');
                history.replaceState(null, null, '#' + id);
            });
        });
    </script>

    <style>
        .title {
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 20px;
            color: black;
        }

        .search-container {
            position: relative;
            width: 300px;
            float: right;
        }

        .search-input {
            padding-right: 35px;
            border-radius: 20px;
            border: 2px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
            color: black;
        }

        .search-input:hover {
            border-color: #28a745;
        }

        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .custom-tabs .nav-link.active {
            max-height: 100%;
            font-weight: bold;
            border-bottom: 3px solid #28a745;
            color: black;
        }

        .section-header {
            cursor: pointer;
            padding: 10px 0;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
            transition: background 0.2s ease;
            color: black;
        }

        .section-header i {
            margin-right: 8px;
        }

        .section-header:hover {
            background: #f8f9fa;
        }

        .report-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .report-link {
            color: #333;
            text-decoration: none;
        }

        .report-link:hover {
            text-decoration: none;
            color: #333;
        }

        .section-actions {
            margin-left: 10px;
        }

        .icons i {
            margin-left: 10px;
            cursor: pointer;
            color: black;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-error {
            color: #ff0000 !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .report-container {
            margin-top: 15px;
        }

        body,
        .nav-link,
        .nav-tabs .nav-item .nav-link,
        .section-header,
        .report-item span,
        .report-link {
            color: black;
        }

        a,
        .report-link {
            color: black;
            text-decoration: none;
        }

        a:hover,
        .report-link:hover {
            color: blue;
            text-decoration: underline;
        }

        .menu-card {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fff;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .custom-reports-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
            /* Set table background to white */
            color: black;
            /* Set text color to black */
        }

        .custom-reports-table th,
        .custom-reports-table td {
            border-bottom: 1px solid #ddd;
            text-align: left;
            padding: 8px;
            color: black;
            /* Set table header and cell text color to black */
        }

        .custom-reports-table thead th {
            background-color: #ffffff;
            font-weight: bold;
        }

        .empty-message {
            text-align: center;
            color: black;
            /* Set empty message text color to black */
            padding: 20px 0;
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            /* Aligns the items to the right */
            margin-top: 10px;
            font-size: 12px;
            color: black;
            /* Set pagination text color to black */
        }

        .pagination span {
            margin-left: 10px;
            /* Adds some space between the pagination items */
            cursor: pointer;
        }

        .floating-action-button {
            position: fixed;
            top: 60px;
            right: 10px;
            z-index: 1000;
        }

        .main-fab {
            width: 60px;
            height: 60px;
            background-color: #0f8800;
            border-radius: 50%;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .main-fab:hover {
            transform: scale(1.1);
        }

        .fab-options {
            position: absolute;
            top: 70px;
            right: 0;
            list-style: none;
            padding: 0;
            margin: 0;
            display: none;
        }

        .floating-action-button.active .fab-options {
            display: block;
        }

        .fab-option {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 16px;
            margin-bottom: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .fab-option:hover {
            transform: scale(1.05);
            background-color: #f8f9fa;
        }

        .fab-option i {
            font-size: 16px;
        }

        /* Add these to your existing styles */
        .section-header {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
        }

        .dropdown-toggle.no-caret::after {
            display: none;
        }

        .btn-link {
            color: #6c757d;
            padding: 0;
        }

        .btn-link:hover {
            color: #343a40;
            text-decoration: none;
        }

        .dropdown-menu {
            min-width: 120px;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item i {
            width: 20px;
        }

        /* Update your existing styles */
        .section-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 15px;
        }

        .section-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            flex-grow: 1;
            cursor: pointer;
        }

        .section-actions {
            position: relative;
        }

        .action-dots {
            cursor: pointer;
            padding: 5px 10px;
            color: #6c757d;
        }

        .action-dots:hover {
            color: #343a40;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
            display: none;
            min-width: 160px;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            color: #212529;
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 16px;
        }

        .section-actions {
            position: relative;
            margin-left: auto;
        }

        .section-actions .btn-link {
            background: none;
            border: none;
            padding: 5px 10px;
            color: #6c757d;
        }

        .section-actions .btn-link:hover {
            color: #343a40;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
            display: none;
            min-width: 160px;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 8px 15px;
            display: flex;
            align-items: center;
            color: #212529;
            text-decoration: none;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 16px;
        }

        .menu-card {
            margin-bottom: 10px;
            transition: all 0.3s ease;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .menu-card.moving {
            opacity: 0.5;
            transform: scale(0.98);
        }

        .report-containered {
            width: auto;
            margin: 16px;
            display: flex;
            flex-direction: row;
            gap: 8px;
        }

        .filter-select {
            border-radius: 8px;
            width: 362px;
            height: 38px;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid black;
            background-color: white;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: #0f8800;
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .modal-footer {
            display: flex;
            flex-direction: row;
        }
    </style>

@endsection
