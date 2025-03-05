<!-- resources/views/print-layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Layout</title>
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .draggable { position: absolute; cursor: move; }
        #logo { width: 100px; height: 50px; background: #f0f0f0; text-align: center; line-height: 50px; }
        #companyName { width: 200px; height: 30px; background: #f0f0f0; text-align: center; line-height: 30px; }
        #printButton { margin-top: 20px; padding: 10px 20px; font-size: 16px; cursor: pointer; }
        #designButton { margin-top: 20px; padding: 10px 20px; font-size: 16px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .dropdown-checkbox { position: relative; display: inline-block; }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px;
        }
        .dropdown-checkbox:hover .dropdown-content { display: block; }
        .flex-container { display: flex; gap: 10px; align-items: center; }

        /* Floating Button Styles */
        .floating-button {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            background-color: rgb(255, 0, 0);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .floating-button:hover {
            background-color: rgb(173, 0, 0);
        }

        /* Layout Options Container */
        .layout-options {
            position: fixed;
            top: 120px;
            right: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 1000;
        }
        .layout-options.show {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important; /* Hide elements with the .no-print class */
            }
            .draggable {
                position: absolute !important; /* Ensure draggable elements respect their positions */
            }
            .session-2 {
                position: relative; /* Ensure the container respects the draggable elements */
            }
            .floating-button, .layout-options {
                display: none !important; /* Hide floating button and layout options during printing */
            }
        }
    </style>
</head>
<body>
    <!-- Session 2: Draggable Elements Container -->
    <div class="card session-2">
        <div class="card-header"></div>
        <div style="position: relative; min-height: 150px;">
            <div class="draggable"></div>
            <div class="draggable"></div>
        </div>
    </div>

    <!-- Card for Table -->
    <div class="card">
        {{ $tableContent ?? '' }}
    </div>

    <!-- Card for Components Container -->
    <div class="card">
        <div class="card-header">Components</div>
        <div id="components-container"></div>
    </div>

    <!-- Floating Button -->
    <div class="floating-button no-print" onclick="toggleLayoutOptions()">
        <i class="fa fa-print" style="font-size: 30px;"></i> <!-- Font Awesome printer icon -->
    </div>

    <!-- Layout Options Container -->
    <div class="layout-options no-print">
        <div class="flex-container">
            <div class="form-group">
                <label for="show_rows" style="display: block; font-size: 24px; color: #333;">Design Layout</label>
                <div class="dropdown">
                    <select class="form-control" id="layoutSelect" name="layout_type"
                        style="font-size: 24px; width: 220px; cursor: pointer;" aria-haspopup="true"
                        aria-expanded="false">
                        <option value="">Select Layout</option>
                    </select>
                </div>
                <!-- Buttons Container -->
                <div class="buttons-container">
                    <button id="designButton" class="btn-primary" onclick="window.location.href='/minireportb1/create-layout'">Design</button>
                    <button id="printButton" class="btn-primary">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            // Fetch layouts on page load
            fetchLayouts();

            // Fetch logo and company name
            fetchLayoutComponents();

            // Make elements draggable
            $('.draggable').draggable({
                containment: '.session-2', // Restrict dragging to the Session 2 container
                cursor: 'move',
                stop: function (event, ui) {
                    // Save the position of the draggable element
                    $(this).data('position', ui.position);
                }
            });

            // Print button click handler
            $('#printButton').on('click', function () {
                // Capture positions of draggable elements before printing
                $('.draggable').each(function () {
                    const position = $(this).position();
                    $(this).css({
                        top: position.top + 'px',
                        left: position.left + 'px'
                    });
                });

                // Trigger the print dialog
                window.print();
            });

            // Handle checkbox change event
            $('.dropdown-content input[type="checkbox"]').on('change', function () {
                const elementId = $(this).val();
                const element = $('#' + elementId);
                if ($(this).is(':checked')) {
                    element.show(); // Show the element
                    restorePosition(element); // Restore its position
                } else {
                    savePosition(element); // Save its position before hiding
                    element.hide(); // Hide the element
                    adjustLayout(); // Adjust the layout after hiding
                }
            });

            // Handle layout selection change
            $('#layoutSelect').change(function () {
                const selectedLayout = $(this).val();
                console.log('Selected Layout:', selectedLayout); // Debugging
                if (selectedLayout) {
                    fetchLayoutComponents(selectedLayout);
                }
            });

            // Function to save the position of an element
            function savePosition(element) {
                const position = element.position();
                element.data('position', position); // Store the position in the element's data
            }

            // Function to restore the position of an element
            function restorePosition(element) {
                const position = element.data('position');
                if (position) {
                    element.css({ top: position.top, left: position.left }); // Restore the position
                }
            }

            // Function to adjust the layout after hiding an element
            function adjustLayout() {
                const visibleElements = $('.draggable:visible'); // Get all visible draggable elements
                let previousBottom = 0; // Track the bottom position of the previous element

                visibleElements.each(function () {
                    const element = $(this);
                    const elementHeight = element.outerHeight(true); // Get the height of the element

                    // Move the element to the top of the previous element's bottom position
                    element.css({ top: previousBottom });
                    previousBottom += elementHeight; // Update the bottom position for the next element
                });
            }

            // Function to fetch layouts
            function fetchLayouts() {
                $.ajax({
                    url: '/minireportb1/layouts', // Ensure this matches your backend route
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        console.log('Layouts fetched:', response);
                        if (response.success) {
                            populateLayoutDropdown(response.layouts);
                        } else {
                            console.error('Error fetching layouts:', response.error);
                            alert('Failed to load layout options.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching layouts:', error);
                        alert('An error occurred while fetching layout options.');
                    }
                });
            }

            // Function to populate the layout dropdown
            function populateLayoutDropdown(layouts) {
                const dropdown = $('#layoutSelect');
                dropdown.empty(); // Clear existing options
                dropdown.append($('<option>', {
                    value: '',
                    text: 'Select Layout'
                }));

                layouts.forEach(function (layout) {
                    dropdown.append($('<option>', {
                        value: layout.layout_name, // Use layout_name as the value
                        text: layout.layout_name // Use layout_name as the display text
                    }));
                });
            }

            // Function to fetch layout components
            function fetchLayoutComponents(layoutName) {
                console.log('Fetching components for layout:', layoutName); // Debugging
                $.ajax({
                    url: '/minireportb1/get-layout-components/' + layoutName , // Ensure this matches your backend route
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        console.log('Components fetched:', response); // Debugging
                        if (response.success) {
                            displayComponents(response.components);
                        } else {
                            console.error('Error in response:', response.error);
                            alert('Failed to load layout components: ' + response.error);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching components:', error);
                        alert('An error occurred while fetching layout components.');
                    }
                });
            }
            // Function to display components
            function displayComponents(components) {
                const container = $('#components-container');
                container.empty(); // Clear existing components
                console.log('Displaying components:', components); // Debugging

                components.forEach(function (component) {
                    // Ensure the component has the required properties
                    const element = $('<div>', {
                        id: component.id,
                        class: 'draggable',
                        css: {
                            left: component.x + 'px', // Ensure 'x' is returned by the backend
                            top: component.y + 'px'  // Ensure 'y' is returned by the backend
                        }
                    });

                    // Inject the HTML content
                    if (component.content && component.content.html) {
                        element.html(component.content.html);
                    } else {
                        element.text('Component'); // Fallback if no HTML content is available
                    }

                    container.append(element);
                    element.draggable({
                        containment: '.session-2', // Restrict dragging to the Session 2 container
                        cursor: 'move'
                    });

                    // Show the component by default when a layout is selected
                    element.show();
                });
            }
        });

        // Function to toggle layout options visibility
        function toggleLayoutOptions() {
            const layoutOptions = document.querySelector('.layout-options');
            layoutOptions.classList.toggle('show');
        }
    </script>
</body>
</html>