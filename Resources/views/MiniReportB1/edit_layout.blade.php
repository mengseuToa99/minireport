@extends('minireportb1::layouts.master2')
@section('title', 'Create Layout')

<style>
    /* Container for A4 editing */
    #droppable {
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
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    #rotate-button:hover {
        background-color: #0056b3;
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
        /* Wider dropdown */
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        padding: 15px;
        /* More padding */
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
</style>

@section('content')

    <!-- Filename + Save button + Rotate button -->
    <div style="margin: 20px;">
        <label for="filename">File Name:</label>
        <input type="text" id="filename" name="filename" class="form-control" required>
        <div class="dropdown-checkbox">
            <button id="toggle-dropdown">Toggle Elements</button>
            <div class="dropdown-content" id="dropdown-content">
                <label><input type="checkbox" class="element-toggle" data-target="draggable1"> Business Logo</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable2"> Business Name</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable3"> File Name</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable4"> Stuff Name</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable5"> Divider 1</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable6"> Divider 2</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable7"> Divider 3</label><br>
                <label><input type="checkbox" class="element-toggle" data-target="draggable8"> Date</label><br>
            </div>
        </div>
        <button id="save-button">Save Layout</button>
        <button id="rotate-button">Rotate to Landscape</button>
    </div>

    <!-- A4 Container -->
    <div id="droppable">

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
            <p>$Date</p> <!-- Placeholder -->
        </div>

        <div style="margin-top: 250px; width: 210mm; height: 257mm; border: 2px solid #0056b3;">
            hello
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
                        layout_name: layoutName, // Changed from filename to layout_name
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
