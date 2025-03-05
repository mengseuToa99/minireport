@extends('layouts.app')


@section('css')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Layout</title>

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/reusable-table.css') }}">

        <script>
            $(function() {
                $("#tabs").tabs();
            });
        </script>
        {{-- <link rel="stylesheet" href="{{ asset('modules/minireportb1/css/print-layout.css') }}"> --}}
        <style>
            :root {
                --primary: #0f8800;
                --primary-hover: #0f8902;
                --background: #f8fafc;
                --surface: #ffffff;
                --text: #0f172a;
                --border: #e2e8f0;
            }

            .modern-btn {
                background: var(--primary);
                color: white;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .modern-btn:hover {
                background: var(--primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1);
            }

            .modern-btn:active {
                transform: translateY(0);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--background);
                color: var(--text);
                line-height: 1.6;
                margin: 20px;
            }

            /* Modern Side Navigation */
            .sidenav {
                height: 100%;
                width: 400px;
                position: fixed;
                z-index: 1000;
                top: 16px;
                right: -400px;
                background: var(--surface);
                backdrop-filter: blur(10px);
                overflow-x: hidden;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                padding: 1.5rem;
                box-shadow: -4px 0 20px rgba(0, 0, 0, 0.05);
                border-left: 1px solid var(--border);
            }

            .sidenav.active {
                right: 0;
            }

            .sidenav-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
            }

            .sidenav-title {
                font-size: 1.25rem;
                font-weight: 600;
                color: var(--text);
            }

            .sidenav-menu {
                list-style: none;
            }

            .sidenav-menu li {
                margin-bottom: 0.5rem;
            }

            .sidenav-menu a {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 0.75rem 1rem;
                text-decoration: none;
                color: var(--text);
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .sidenav-menu a:hover {
                background: #f1f5f9;
                transform: translateX(-4px);
            }

            .sidenav-menu a i {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .closebtn {
                background: none;
                border: none;
                padding: 0.5rem;
                cursor: pointer;
                border-radius: 50%;
                transition: background 0.2s ease;
            }

            .closebtn:hover {
                background: #f1f5f9;
            }

            /* Open Button */
            .openbtn {
                position: fixed;
                top: 1.5rem;
                right: 1.5rem;
                background: var(--primary);
                color: white;
                border: none;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                cursor: pointer;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 100;
            }

            .openbtn:hover {
                background: var(--primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1);
            }

            /* Card Styles */
            .card,
            .card-table {
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

            .draggable {
                position: absolute;
                cursor: move;
            }

            #printButton {
                margin-top: 20px;
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
            }

            .dropdown-content {
                display: none;
                position: absolute;
                background-color: #f9f9f9;
                min-width: 160px;
                box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
                z-index: 1;
                padding: 10px;
            }

            @media print {
                .no-print {
                    display: none !important;
                }

                .draggable {
                    position: absolute !important;
                }

                .session-2 {
                    position: relative !important;
                }

                .card,
                .card-table {
                    background: none !important;
                    border: none !important;
                    border-radius: 0 !important;
                    padding: 0 !important;
                    margin-bottom: 0 !important;
                    box-shadow: none !important;
                }


            }
        </style>
    @endsection
    @section('content')

    <body>

        <div>
            <div class="arrow no-print" id="goBackButton"></div>

            <nav id="mySidenav" class="sidenav no-print">
                <div id="tabs">
                    <button class="closebtn" onclick="closeNav()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                    <ul>
                        <li><a href="#tabs-1">Layout</a></li>
                        <li><a href="#tabs-2">Component</a></li>
                        <li><a href="#tabs-3">Customize</a></li>
                    </ul>
                    <div id="tabs-1">
                        <h3 class="sidenav-title">Layout</h3>
                        <a href="/minireportb1/create-layout">
                            <i class="fas fa-pen"></i> Design Layout
                        </a>
                        <div class="form-group">
                            <label for="show_rows" style="display: block; font-size: 24px; color: #333;">
                                Available Layouts
                            </label>
                            <ul id="layoutList">
                                <!-- Layout options will be populated here -->
                            </ul>
                        </div>
                        <button id="printButton" class="modern-btn">Print</button>
                    </div>

                    <div id="tabs-2">
                        <div id="componentList"
                            style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-between;">

                            <!-- Card 1: Logo -->
                            <div class="card-element"
                                style="width: 45%; height: 150px; border: 1px solid #ddd; padding: 10px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-sizing: border-box;">
                                <div style="text-align: center;">
                                    @if ($component['logo'])
                                        <img src="{{ asset('/uploads/business_logos/' . $component['logo']) }}"
                                            alt="{{ $component['business_name'] }}" style="max-height: 60px;">
                                    @else
                                        <p>No logo available</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Card 2: Date -->
                            <div class="card-element"
                                style="width: 45%; height: 150px; border: 1px solid #ddd; padding: 10px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-sizing: border-box;">
                                <p style="text-align: center;">{{ $component['date'] }}</p>
                            </div>

                            <!-- Card 3: Signature Placeholder -->
                            <div class="card-element"
                                style="width: 45%; height: 150px; border: 1px solid #ddd; padding: 10px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-sizing: border-box;">
                                <div
                                    style="width: 100%; height: 100px; position: relative; display: flex; justify-content: center; align-items: center; font-family: 'Courier New', Courier, monospace;">
                                    <div style="width: 80%; border-top: 1px solid #000; margin-top: 10px;"></div>
                                    <div style="font-size: 16px; position: absolute; bottom: 10px;">Signature Here</div>
                                </div>
                            </div>

                        </div>
                    </div>



                </div>

                <div id="tabs-3">
                    <div class="buttons-container"></div>

                </div>
        </div>
        </nav>
        </div>

        <div class="card session-2">
            <div class="card-header"></div>
            <div id="layoutContent" style="position: relative; min-height: 150px;">
                <!-- Default content will go here -->
                <div class="default-content">Hello World</div>
            </div>
        </div>

        <div class="card-table">
            <div id="retrievedTable"></div>
        </div>

        <div class="card no-print">
            <div class="card-header">Components</div>
            <div id="components-container"></div>
        </div>

        <button class="openbtn no-print" onclick="openNav()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        {{-- <script src="{{ asset('modules/minireportb1/js/print_function.js') }}"></script> --}}
        <script>
            function openNav() {
                document.getElementById("mySidenav").classList.add('active');
            }

            function closeNav() {
                document.getElementById("mySidenav").classList.remove('active');
            }

            $(document).ready(function() {
                // Retrieve the table's HTML from localStorage and display it
                const savedTableHTML = localStorage.getItem('savedTable');
                if (savedTableHTML) {
                    console.log('Retrieved table HTML from localStorage:', savedTableHTML);
                    $('#retrievedTable').html(savedTableHTML);
                } else {
                    console.log('No table found in localStorage.');
                }

                // Other existing code...
                setDefaultContent();
                fetchLayouts();
                fetchLayoutComponents();

                $('.draggable').draggable({
                    containment: '.session-2',
                    cursor: 'move',
                    stop: function(event, ui) {
                        $(this).data('position', ui.position);
                    }
                });

                $('#printButton').on('click', function() {
                    window.print();
                });

                $('#layoutList').on('click', 'li', function() {
                    const selectedLayout = $(this).data('layout-name');
                    if (selectedLayout) {
                        if (selectedLayout === 'default') {
                            $('.draggable').remove();
                            setDefaultContent();
                        } else {
                            fetchLayoutComponents(selectedLayout);
                            updateTableHeader(selectedLayout);
                            updateLayout(selectedLayout);
                        }
                    }
                });

                function fetchLayouts() {
                    $.ajax({
                        url: '/minireportb1/layouts',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                populateLayoutList(response.layouts);
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

                function setDefaultContent() {
                    const layoutContent = $('#layoutContent');
                    const reportname = localStorage.getItem('reportname');

                    layoutContent.html(`
                    <div class="default-content" id="defaultContent">
                        <h2 style="text-align: center; padding: 20px;">
                            
                                <div class="report-header" id="report-header">
                                    <h2 class="p-4 bg-gray-100 tw-font-light tw-text-center normal-view-title" style="font-size: 20px;">
                                        {{ $component['business_name'] }}
                                    </h2>
                                    <h2 class="p-4 bg-gray-100 tw-font-semibold tw-text-center normal-view-title" style="font-size: 25px;">
                                        ${reportname}
                                    </h2>
                            
                            </div>
                        </h2>
                    </div>
                `);
                }

                function populateLayoutList(layouts) {
                    const list = $('#layoutList');
                    list.empty();

                    // Add Default Layout option first
                    const defaultItem = $('<li>', {
                        text: 'Default Layout',
                        'data-layout-name': 'default'
                    });
                    list.append(defaultItem);

                    // Add other layouts
                    layouts.forEach(function(layout) {
                        const listItem = $('<li>', {
                            text: layout.layout_name,
                            'data-layout-name': layout.layout_name
                        });
                        list.append(listItem);
                    });
                }

                function updateTableHeader(layoutName) {
                    const tableHeader = $('#tableHeader');
                    tableHeader.empty();
                    const headers = layoutName.split('_');
                    headers.forEach(function(header) {
                        const th = $('<th>', {
                            text: header
                        });
                        tableHeader.append(th);
                    });
                }

                function fetchLayoutComponents(layoutName) {
                    $.ajax({
                        url: '/minireportb1/get-layout-components/' + layoutName,
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                displayComponents(response.components);
                            } else {
                                console.error('Error in response:', response.error);
                                alert('Failed to load layout components: ' + response.error);
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
                    container.empty();
                    components.forEach(function(component) {
                        const element = $('<div>', {
                            id: component.id,
                            class: 'draggable',
                            css: {
                                left: component.x + 'px',
                                top: component.y + 'px'
                            }
                        });
                        if (component.content && component.content.html) {
                            element.html(component.content.html);
                        } else {
                            element.text('Component');
                        }
                        container.append(element);
                        element.draggable({
                            containment: '.session-2',
                            cursor: 'move'
                        });
                        element.show();
                    });
                }

                function updateLayout(layoutName) {
                    const layoutContent = $('#layoutContent');
                    $('#defaultContent').remove();
                    // Define available layouts
                    const layouts = {};

                    // Only update if layout exists
                    if (layouts[layoutName]) {
                        layoutContent.html(layouts[layoutName]);
                        $('.report-header').draggable({
                            containment: '.session-2',
                            cursor: 'move'
                        });
                    }
                }
            });
        </script>
        <script>
            // Select all the cards
            const cards = document.querySelectorAll('.card-element');

            // Select the layoutContent to insert the clicked content
            const layoutContent = document.getElementById('layoutContent');

            // Loop through each card and add a click event listener
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    // Clone the content of the clicked card
                    const cardContent = this.cloneNode(true);

                    // Add draggable class and styling
                    cardContent.classList.add('draggable');
                    cardContent.style.position = 'absolute';
                    cardContent.style.left = '0px'; // Initial position
                    cardContent.style.top = '0px'; // Initial position
                    // Override inherited size
                    cardContent.style.width = 'auto'; // Fixed width for cloned component
                    cardContent.style.height = 'auto'; // Fixed height for cloned component
                    cardContent.style.boxSizing = 'border-box'; // Ensure padding/border included
                    cardContent.style.border = 'none';

                    // Append the cloned content to layoutContent
                    layoutContent.appendChild(cardContent);

                    // Get the dimensions of the cloned component
                    const rect = cardContent.getBoundingClientRect();
                    const width = rect.width; // Now 150px
                    const height = rect.height; // Now 80px
                    const startX = rect.left;
                    const startY = rect.top;

                    // Set containment to fit within layoutContent's bounds (or component size)
                    const layoutRect = layoutContent.getBoundingClientRect();
                    $(cardContent).draggable({
                        containment: [
                            layoutRect.left, // Left boundary of layoutContent
                            layoutRect.top, // Top boundary of layoutContent
                            layoutRect.right - width, // Right boundary adjusted for component width
                            layoutRect.bottom -
                            height // Bottom boundary adjusted for component height
                        ],
                        cursor: 'move',
                        stop: function(event, ui) {
                            $(this).data('position', ui.position);
                        }
                    });
                });
            });
        </script>
    </body>

    <script src="{{ asset('modules/minireportb1/js/print.js') }}"></script>
@endsection
