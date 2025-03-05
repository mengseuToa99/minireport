@extends('minireportb1::layouts.master')

@section('title', __('{{ $file_name }}'))

<style>
    .dropdown select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 1em;
        padding-right: 2.5em;
    }

    .dropdown select:focus {
        outline: none;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }
</style>

<style>
    .component {
        position: absolute;
        pointer-events: none;
        /* prevents dragging in display mode */
    }

    .component img {
        max-width: 100%;
        height: auto;
    }

    /* A4 Container */
    .box {
        background-color: transparent;
        /* Changed to transparent */
        width: 210mm;
        height: 297mm;
        margin: 0 auto;
        padding: 20px;
        box-sizing: border-box;
        position: relative;
        /* important for absolutely positioned children */
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    @media print {
        .box {
            border: none;
            box-shadow: none;
        }

        .component {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }

    /* Draggable Components */
    [id^="draggable"] {
        position: absolute;
        cursor: move;
        background: transparent;
        z-index: 1000;
        padding: 5px;
    }
</style>

@section('content')
    <section class="content" style="background-color: transparent;">
        <div class="row" style="padding: 20px; background-color: transparent;">
            <div class="box-body" style="background-color: transparent; padding: 20px; border-radius: 8px;">

                <!-- Back to report list link -->
                <a href="http://127.0.0.1:8001/minireportb1/MiniReportB1" class="btn btn-link"
                    style="display: inline-flex; align-items: center; text-decoration: none; color: #007bff; font-size: 14px; margin-bottom: 15px;">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;">
                        <path fill-rule="evenodd"
                            d="M10.354 3.646a.5.5 0 0 1 0 .708L6.707 8l3.647 3.646a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 0 1 .708 0z" />
                    </svg>
                    @lang('Back to report list')
                </a>

                <!-- Title -->
                <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 20px; padding-left: 12px;">
                    {{ $file_name }}
                </h3>
                <h3 style="font-size: 12px; margin-bottom: 20px; padding-left: 12px;">Report period</h3>

                <!-- Form elements -->
                <form class="form-inline"
                    style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between;">
                    <!-- Top Row -->
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <!-- Report Period -->
                        <div class="form-group">
                            {!! Form::text('date_range_filter', null, [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'readonly',
                                'id' => 'date_range_filter',
                                'style' => 'border-radius: 10px; border: 1px solid #ccd1d9; padding: 8px 12px; background-color: #ffffff;',
                            ]) !!}
                        </div>

                        <!-- As of Date Fields in a Row -->
                        <div class="form-group" style="display: flex; gap: 10px;">
                            {!! Form::date('as_of', \Carbon\Carbon::now()->format('Y-m-d'), [
                                'class' => 'form-control',
                                'style' => 'width: 130px; font-size: 12px;',
                            ]) !!}
                            {!! Form::date('as_of_2', \Carbon\Carbon::now()->format('Y-m-d'), [
                                'class' => 'form-control',
                                'style' => 'width: 130px; font-size: 12px;',
                            ]) !!}
                        </div>
                    </div>

                    <!-- Bottom Row -->
                    <div
                        style="display: flex; flex-wrap: wrap; gap: 15px; width: 100%; align-items: center; margin-top: 10px;">
                        <!-- Display Columns By -->
                        <div class="form-group">
                            {!! Form::label('display_columns', __('Display columns by'), [
                                'style' => 'display: block; font-size: 12px; color: #333;',
                            ]) !!}
                            {!! Form::select(
                                'display_columns',
                                [
                                    'Total Only' => 'Total Only',
                                    'Days' => 'Days',
                                    'Weeks' => 'Weeks',
                                    'Months' => 'Months',
                                    'Quarters' => 'Quarters',
                                    'Years' => 'Years',
                                    'Customers' => 'Customers',
                                    'Suppliers' => 'Suppliers',
                                    'Classes' => 'Classes',
                                    'Products/Services' => 'Products/Services',
                                ],
                                null,
                                [
                                    'class' => 'form-control',
                                    'style' => 'font-size: 12px; width: 160px; border-radius: 4px;',
                                ],
                            ) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('display_columns', __('Display columns by'), [
                                'style' => 'display: block; font-size: 12px; color: #333;',
                            ]) !!}
                            {!! Form::select(
                                'display_columns',
                                [
                                    'Total Only' => 'Total Only',
                                    'Days' => 'Days',
                                    'Weeks' => 'Weeks',
                                    'Months' => 'Months',
                                    'Quarters' => 'Quarters',
                                    'Years' => 'Years',
                                    'Customers' => 'Customers',
                                    'Suppliers' => 'Suppliers',
                                    'Classes' => 'Classes',
                                    'Products/Services' => 'Products/Services',
                                ],
                                null,
                                [
                                    'class' => 'form-control',
                                    'style' => 'font-size: 12px; width: 160px; border-radius: 4px;',
                                ],
                            ) !!}
                        </div>

                        <!-- Show Non-Zero or Active Only -->
                        <div class="form-group">
                            {!! Form::label('show_rows', __('Design Layout'), [
                                'style' => 'display: block; font-size: 12px; color: #333;',
                            ]) !!}
                            <div class="dropdown" style="position: relative;">
                                <select class="form-control" id="layoutSelect" name="layout_type"
                                    style="font-size: 12px; width: 220px; cursor: pointer;" aria-haspopup="true"
                                    aria-expanded="false">
                                    <option value="">Select Layout</option>
                                </select>
                            </div>
                        </div>

                        <button id="printButton"
                            style="margin: 20px auto; display: block; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Print A4 Container
                        </button>
                    </div>
                </form>
                <a href="{{ route('minireportb1.createlayout') }}" style="text-decoration: none;">
                    <button
                        style="margin: 20px auto; display: block; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Create Layout
                    </button>
                </a>
            </div>
        </div>

        <!-- A4 Paper Container -->
        <div class="box" style="height: auto;">
            <!-- Components Container (will get placed absolutely within .box) -->
            <div id="components-container">
                <!-- Draggable Components -->
                <div id="draggable2" class="ui-widget-content">
                    <p>----------</p>
                </div>

            </div>

            <!-- Display Table with Sales Data -->
            <table id="dynamicTable" style="width: 100%; height: auto; border-collapse: collapse; margin-top: 250px;">
                <thead>
                    <tr style="background-color: #f4f5f8;">
                        @foreach ($visibleColumnNames as $column)
                            <th style="border: 1px solid #000; padding: 8px; text-align: left; min-width: 100px; cursor: pointer;"
                                onclick="sortTable({{ $loop->index }})">
                                {{ $column }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($data))
                        @foreach ($data as $row)
                            <tr>
                                @foreach ($visibleColumnNames as $column)
                                    @php
                                        $key = $columnMapping[$column] ?? null;
                                        $value = $key ? $row[$key] ?? 'N/A' : 'N/A';
                                    @endphp
                                    <td style="border: 1px solid #000; padding: 8px; text-align: left; min-width: 100px;">
                                        {{ $value }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ count($visibleColumnNames) }}" style="text-align: center;">
                                No data available
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Make elements draggable within .box
            $("#draggable1, #draggable2, #draggable3, #draggable4, #draggable5, #draggable6, #draggable7, #draggable8")
                .draggable({
                    containment: ".box",
                    stop: function(event, ui) {
                        // Log the new position of the component
                        console.log(
                            `Component ${this.id} moved to (${ui.position.left}, ${ui.position.top})`);
                    }
                });

            // Fetch and populate layout options on page load
            fetchLayouts();

            // Handle layout selection change
            $('#layoutSelect').change(function() {
                const selectedLayout = $(this).val();
                if (selectedLayout) {
                    fetchLayoutComponents(selectedLayout);
                }
            });

            // Print button functionality
            $('#printButton').on('click', function() {
                // Clone the A4 container
                const printContents = $('.box').clone();

                // Create a new window for printing
                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write('<html><head><title>Print</title>');
                printWindow.document.write('<style>');
                printWindow.document.write(`
                    .box {
                        background-color: transparent;
                        width: 210mm;
                        height: 297mm;
                        margin: 0 auto;
                        padding: 20px;
                        box-sizing: border-box;
                        position: relative;
                        border: 1px solid #ddd;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    @media print {
                        .box {
                            border: none;
                            box-shadow: none;
                        }
                        .component {
                            print-color-adjust: exact;
                            -webkit-print-color-adjust: exact;
                        }
                    }
                `);
                printWindow.document.write('</style></head><body>');
                printWindow.document.write(printContents.html());
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                // Trigger print
                printWindow.print();
                printWindow.close();
            });

            /**
             * Fetches available layouts and populates dropdown
             */
            function fetchLayouts() {
                $.ajax({
                    url: '{{ route('minireport.components.show') }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            populateLayoutDropdown(response.layouts);
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

            /**
             * Populate <select> with layout data
             */
            function populateLayoutDropdown(layouts) {
                const dropdown = $('#layoutSelect');
                dropdown.empty(); // Clear existing options
                dropdown.append($('<option>', {
                    value: '',
                    text: 'Select Layout'
                }));

                layouts.forEach(function(layout) {
                    dropdown.append($('<option>', {
                        value: layout.type,
                        text: layout.type
                    }));
                });
            }

            /**
             * Given a layout type, fetch components for that layout
             */
            function fetchLayoutComponents(layoutType) {
                $.ajax({
                    url: '{{ route('minireport.components.get', '') }}/' + layoutType,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            displayComponents(response.components);
                        } else {
                            console.error('Error in response:', response.error);
                            alert('Failed to load layout components.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching components:', error);
                        alert('An error occurred while fetching layout components.');
                    }
                });
            }

            function displayComponents(components) {
    const container = $('#components-container');
    container.empty(); // Clear existing components

    // Append the draggable2 element to the container
    const draggableElement = $('<div>', {
        id: 'draggable2',
        class: 'ui-widget-content',
    });
    container.append(draggableElement);

    components.forEach(function(component) {
        try {
            const content = component.content;

            // For debugging
            console.log(
                `Component coordinates => x: ${component.x}, y: ${component.y}`
            );

            // Create the absolutely positioned element
            const element = $('<div>', {
                class: 'component',
                html: content.html,
                css: {
                    position: 'absolute',
                    left: component.x + 'px',
                    top: component.y + 'px',
                    // If needed, use 'z-index': component.position
                }
            });

            // Append to our container
            container.append(element);

            // Append the new content to the #draggable2 element
            $('#draggable2').html(content.html);

            // Reinitialize draggable for #draggable2
            $('#draggable2').draggable({
                containment: ".box",
                stop: function(event, ui) {
                    // Log the new position of the component
                    console.log(
                        `Component ${this.id} moved to (${ui.position.left}, ${ui.position.top})`
                    );
                }
            });

        } catch (e) {
            console.error('Error displaying component:', e, component);
        }
    });
}
        });
    </script>
@endsection
