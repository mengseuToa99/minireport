<!-- External Toggle Button for Visibility Options -->
<div class="toggle-elements-group" style="margin: 20px;">
    <button type="button" id="external-toggle-button" class="toggle-button">
        <i class="fas fa-chevron-down"></i> Toggle Elements
    </button>
    <!-- Dropdown container for checkboxes -->
    <div class="dropdown-menu custom-dropdown" id="external-dropdown" style="display: none;">
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

<style>
    /* Container for buttons */
    .button-container {
        display: flex;
        justify-content: center;
        /* Center buttons horizontally */
        align-items: center;
        /* Center buttons vertically */
        gap: 20px;
        /* Space between buttons */
        margin: 20px 0;
        /* Margin for spacing */
    }

    /* Rotation button */
    #rotate-button,
    #save-button,
    #toggle-dropdown {
        padding: 10px 20px;
        background-color: #0f8800;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    #rotate-button:hover,
    #save-button:hover,
    #toggle-dropdown:hover {
        background-color: #0f8800;
        /* Darker green on hover */
    }
</style>


<script>
    $(function() {
        // Toggle the external dropdown when the external button is clicked.
        $('#external-toggle-button').on('click', function(event) {
            event.stopPropagation(); // Prevent the event from bubbling up.
            $('#external-dropdown').toggle(); // Show/hide the dropdown.

            // Optionally, update the icon (rotate the chevron) for visual feedback.
            $(this).find('i').toggleClass('rotate'); // Add a CSS rule for .rotate if desired.
        });

        // Hide the external dropdown when clicking anywhere else.
        $(document).on('click', function() {
            $('#external-dropdown').hide();
            // Remove the rotated class if added.
            $('#external-toggle-button').find('i').removeClass('rotate');
        });

        // Prevent clicks inside the external dropdown from closing it.
        $('#external-dropdown').on('click', function(event) {
            event.stopPropagation();
        });

        // (Optional) You can also bind the checkboxes to show/hide elements as before.
        $(".element-toggle").on('change', function() {
            const target = $(this).data("target"); // Get the target element ID
            const isChecked = $(this).is(":checked"); // Check if the checkbox is checked

            if (isChecked) {
                $("#" + target).show(); // Show the element
            } else {
                $("#" + target).hide(); // Hide the element
            }
        });
    });
</script>
