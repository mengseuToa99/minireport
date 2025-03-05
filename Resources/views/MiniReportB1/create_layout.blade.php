@extends('minireportb1::layouts.master2')
@section('title', 'Create Layout')

<style>
    /* Container for A4 editing */
    #droppable {
        margin-bottom: 50px;
        width: 210mm;
        /* A4 width */
        height: 297mm;
        /* A4 height */
        background-color: #fefffe;
        margin: 0 auto;
        position: relative;
        /* important for absolute child positioning */
        border: 1px solid #ddd;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        /* Smooth transition for rotation */
    }

    /* Landscape mode */
    #droppable.landscape {
        width: 297mm;
        /* A4 height becomes width */
        height: 210mm;
        /* A4 width becomes height */
    }

    /* Draggables */
    [id^="draggable"] {
        position: absolute;
        cursor: move;
        background: transparent;
        z-index: 1000;
        padding: 5px;
    }

    /* Rotation button */
    #rotate-button,
    #save-button,
    #rotate-button,
    #toggle-dropdown {
        margin: 20px;
        padding: 10px 20px;
        background-color: #0f8800;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    #rotate-button:hover {
        background-color: #0f8800;
    }

    /* Dropdown checkbox styles */
    .dropdown-checkbox {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        padding: 15px;

    }

    /* Make checkboxes super big */
    .element-toggle {
        transform: scale(2);
        /* Increase checkbox size */
        margin: 10px 10px 10px 0;
        /* Add spacing */
    }

    /* Label styling for better alignment */
    .dropdown-content label {
        display: flex;
        align-items: center;
        font-size: 18px;
        /* Larger font size */
        margin-bottom: 10px;
        /* Spacing between checkboxes */
    }

    /* General styling for the toggle button */
    .toggle-elements-group {
        position: relative;
        display: inline-block;
    }

    .toggle-button {
        background-color: #0f8800;
        /* Bootstrap's success color */
        border: none;
        color: white;
        padding: 8px 12px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .toggle-button:hover {
        background-color: #0f8800;
        /* Darker shade for hover effect */
    }

    .toggle-button i {
        transition: transform 0.3s ease;
    }

    .toggle-button[aria-expanded="true"] i {
        transform: rotate(180deg);
        /* Rotate the chevron when dropdown is open */
    }

    /* Custom dropdown menu styling */
    .custom-dropdown {
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
        padding: 8px 0;
        min-width: 200px;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item input[type="checkbox"] {
        margin-right: 10px;
    }
</style>

@section('content')

    @component('components.widget', ['class' => 'box-solid'])
        <!-- Filename + Save button + Rotate button -->
        <div class="col-md-6">
            <div class="form-group">
                <div class="input-group">
                    <div>
                        <label for="filename">File Name:</label>
                        <!-- Input field -->
                        <input type="text" id="filename" name="filename" class="form-control" required>
                    </div>


                    <!-- Save and Rotate buttons -->
                    <div class="input-group-btn" style="margin-top: 10px;">
                        <button type="button" class="btn btn-success btn-flat" id="save-button">
                            <i class="text-white fas fa-save"></i> Save Layout
                        </button>
                        <button type="button" class="btn btn-success btn-flat" id="rotate-button">
                            <i class="text-white fas fa-sync"></i> Rotate
                        </button>

                        <!-- Dropdown button -->
                        <div class="toggle-elements-group">
                            <button type="button" class="toggle-button btn-success" data-toggle="dropdown"
                                title="Toggle Elements">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right custom-dropdown">
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable1"> Business Logo
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable2"> Business Name
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable3"> File Name
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable4"> Stuff Name
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable5"> Divider 1
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable6"> Divider 2
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable7"> Divider 3
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="element-toggle" data-target="draggable8"> {{ $current_day }}
                                </label>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endcomponent

    <!-- A4 Container -->
    <div id="droppable">
        <div id="draggable1" class="ui-widget-content" style="display: none;">
            <img src="{{ asset('/uploads/business_logos/' . $business_logo) }}" alt="{{ $business_name }}"
                style="max-height: 80px;">
        </div>

        <div id="draggable2" class="ui-widget-content" style="display: none;">
            <p class="mb-1" style="width: auto">{{ $business_name }} </p>
        </div>

        <div id="draggable3" class="ui-widget-content" style="display: none;">
            <p class="mb-1" style="width: auto">$file_name</p> <!-- Placeholder -->
        </div>

        <div id="draggable4" class="ui-widget-content" style="display: none;">
            <p class="mb-1" style="width: auto">$stuff_name</p> <!-- Placeholder -->
        </div>

        <div id="draggable5" class="ui-widget-content" style="display: none;">
            <p>----------</p>
        </div>

        <div id="draggable6" class="ui-widget-content" style="display: none;">
            <p>----------</p>
        </div>

        <div id="draggable7" class="ui-widget-content" style="display: none;">
            <p>----------</p>
        </div>

        <div id="draggable8" class="ui-widget-content" style="display: none;">
            <p>{{ $current_day }}</p> <!-- Placeholder -->
        </div>

        <div style="margin-top: 250px; solid #0f8800;">
            @include('minireportb1::MiniReportB1.multitable.partials.table')
        </div>
    </div>

    <!-- jQuery + jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <script>
        $(function() {
            // Define A4 dimensions in pixels
            const a4Width = 794; // 210mm in pixels (at 96 DPI)
            const a4Height = 1123; // 297mm in pixels (at 96 DPI)

            // Define blank page dimensions (e.g., viewport size or custom size)
            const blankPageWidth = window.innerWidth; // Use viewport width
            const blankPageHeight = window.innerHeight; // Use viewport height

            // Make elements draggable within #droppable
            $("[id^='draggable']").draggable({
                containment: "#droppable",
                stop: function(event, ui) {
                    // We won't immediately store anything here,
                    // we can log or do something if needed
                }
            });

            // Handle rotation
            let isLandscape = false;
            $("#rotate-button").click(function() {
                const $droppable = $("#droppable");
                isLandscape = !isLandscape; // Toggle state

                if (isLandscape) {
                    $droppable.addClass("landscape");
                    $("#rotate-button").text("Rotate to Portrait");
                } else {
                    $droppable.removeClass("landscape");
                    $("#rotate-button").text("Rotate to Landscape");
                }
            });

            // Handle saving
            // Handle saving
            $("#save-button").click(function() {
                const layoutName = $("#filename").val();
                if (!layoutName) {
                    alert("Please enter a file name");
                    return;
                }

                let components = [];
                $("[id^='draggable']").each(function() {
                    const $element = $(this);

                    if ($element.is(":visible")) {
                        const position = $element.position(); // Get position relative to #droppable
                        console.log(
                            `Element: ${$element.attr('id')}, X: ${position.left}, Y: ${position.top}`
                        ); // Debugging

                        components.push({
                            element_id: $element.attr('id'),
                            type: $element.attr('id'), // Save the draggable ID as type
                            content: {
                                html: $element.html()
                            },
                            x: position.left,
                            y: position.top,
                            layout_name: layoutName // Add layout name to each component
                        });
                    }
                });

                // Send data to the server
                $.ajax({
                    url: "{{ route('minireport.components.store') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        layout_name: layoutName,
                        components: components,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Save response:', response);
                        alert("Layout saved successfully!");
                    },
                    error: function(xhr, status, error) {
                        console.error('Save error:', error);
                        console.error('Response:', xhr.responseText);
                        alert("Error saving layout: " + error);
                    }
                });
            });



            // Handle checkbox toggles
            $(".element-toggle").change(function() {
                const target = $(this).data("target"); // Get the target element ID
                const isChecked = $(this).is(":checked"); // Check if the checkbox is checked

                if (isChecked) {
                    $("#" + target).show(); // Show the element
                } else {
                    $("#" + target).hide(); // Hide the element
                }
            });

            // Hide all elements by default
            $("[id^='draggable']").hide();

            // Toggle dropdown on button click
            $("#toggle-dropdown").click(function(event) {
                event.stopPropagation(); // Prevent the click from propagating to the document
                $("#dropdown-content").toggle(); // Toggle the dropdown visibility
            });

            // Close dropdown when clicking outside
            $(document).click(function() {
                $("#dropdown-content").hide(); // Hide the dropdown
            });

            // Prevent dropdown from closing when clicking inside it
            $("#dropdown-content").click(function(event) {
                event.stopPropagation(); // Prevent the click from propagating to the document
            });
        });
    </script>

@endsection
