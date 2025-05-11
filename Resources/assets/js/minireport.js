// This file contains all common functionality for the application

$(document).on('submit', 'form', function (e) {
    if (!__is_online()) {
        e.preventDefault();
        toastr.error(LANG.not_connected_to_a_network);
        return false;
    }

    $(this).find('button[type="submit"]').attr('disabled', true);
});

$(document).ready(function () {
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    $.ajaxSetup({
        beforeSend: function (jqXHR, settings) {
            if (!__is_online()) {
                toastr.error(LANG.not_connected_to_a_network);
                return false;
            }
            if (settings.url.indexOf('http') === -1) {
                settings.url = base_path + settings.url;
            }
        },
    });

    update_font_size();
    if ($('#status_span').length) {
        var status = $('#status_span').attr('data-status');
        if (status === '1') {
            toastr.success($('#status_span').attr('data-msg'));
        } else if (status == '' || status === '0') {
            toastr.error($('#status_span').attr('data-msg'));
        }
    }

    // Default setting for select2
    $.fn.select2.defaults.set('minimumResultsForSearch', 6);
    if ($('html').attr('dir') == 'rtl') {
        $.fn.select2.defaults.set('dir', 'rtl');
    }
    $.fn.datepicker.defaults.todayHighlight = true;
    $.fn.datepicker.defaults.autoclose = true;
    $.fn.datepicker.defaults.format = datepicker_date_format;

    // Toastr setting
    toastr.options.preventDuplicates = true;
    toastr.options.timeOut = '3000';

    // Play notification sound on success, error, and warning
    toastr.options.onShown = function () {
        if ($(this).hasClass('toast-success')) {
            var audio = $('#success-audio')[0];
            if (audio !== undefined) {
                audio.play();
            }
        } else if ($(this).hasClass('toast-error')) {
            var audio = $('#error-audio')[0];
            if (audio !== undefined) {
                audio.play();
            }
        } else if ($(this).hasClass('toast-warning')) {
            var audio = $('#warning-audio')[0];
            if (audio !== undefined) {
                audio.play();
            }
        }
    };

    // Default setting for jQuery validator
    jQuery.validator.setDefaults({
        errorPlacement: function (error, element) {
            if (element.hasClass('select2') && element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2')) {
                error.insertAfter(element.next('span.select2-container'));
            } else if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else if (element.parent().hasClass('multi-input')) {
                error.insertAfter(element.closest('.multi-input'));
            } else if (element.parent().hasClass('input_inline')) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('upload-element')) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },

        invalidHandler: function () {
            toastr.error(LANG.some_error_in_input_field);
        },
    });

    jQuery.validator.addMethod(
        'max-value',
        function (value, element, param) {
            var is_draft = false;
            if (
                $(element).hasClass('pos_quantity') &&
                $('select#status').length &&
                $('select#status').val() !== 'final'
            ) {
                is_draft = true;
            }
            return is_draft || this.optional(element) || !(param < __number_uf(value));
        },
        function (params, element) {
            return $(element).data('msg-max-value');
        }
    );

    jQuery.validator.addMethod('abs_digit', function (value, element) {
        return this.optional(element) || Number.isInteger(Math.abs(__number_uf(value)));
    });

    // Set global currency to be used in the application
    __currency_symbol = $('input#__symbol').val();
    __currency_thousand_separator = $('input#__thousand').val();
    __currency_decimal_separator = $('input#__decimal').val();
    __currency_symbol_placement = $('input#__symbol_placement').val();
    if ($('input#__precision').length > 0) {
        __currency_precision = $('input#__precision').val();
    } else {
        __currency_precision = 2;
    }

    if ($('input#__quantity_precision').length > 0) {
        __quantity_precision = $('input#__quantity_precision').val();
    } else {
        __quantity_precision = 2;
    }

    // Set page-level currency to be used for some pages (e.g., Purchase page)
    if ($('input#p_symbol').length > 0) {
        __p_currency_symbol = $('input#p_symbol').val();
        __p_currency_thousand_separator = $('input#p_thousand').val();
        __p_currency_decimal_separator = $('input#p_decimal').val();
    }

    __currency_convert_recursively($(document), $('input#p_symbol').length);

    var buttons = [
        {
            text: '<i class="fa fa-save" aria-hidden="true"></i> ' + (LANG.save || 'Save'),
            className:
                'tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right tw-ml-4 tw-mb-8',
            action: function (e, dt, node, config) {
                // Call the function to submit/save the configuration
                createFile();
            },
        },
        {
            extend: 'colvis',
            text: '<i class= "text-primary fa fa-cog" aria-hidden="true"></i> ',
            className: 'text-primary tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
        },

        {
            extend: 'csv',
            text: '<i class="text-info fa fa-file-csv" aria-hidden="true"></i> ',
            className: 'text-info tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
            exportOptions: {
                columns: ':visible',
            },
            footer: true,
        },
        {
            extend: 'excel',
            text: '<i class="text-success fa fa-file-excel" aria-hidden="true"></i> ',
            className: 'text-success tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
            exportOptions: {
                columns: ':visible',
            },
            footer: true,
        },
        {
            extend: 'print',
            text: '<i class=" text-waring fa fa-print" aria-hidden="true"></i> ',
            className: 'text-warning tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
            exportOptions: {
                columns: ':visible',
                stripHtml: true,
            },
            footer: true,
            action: function (e, dt, node, config) {
                // Open a blank page
                var printWindow = window.open('about:blank', '_blank');

                // Get the file name from the container
                const fileName = $('#file-name-container').data('file-name') || 'Print Preview';

                // Log the file name to the console for debugging

                const designButtonUrl = "{{ route('minireportb1.createlayout') }}";
                // Inject HTML, CSS, and JavaScript into the blank page
                printWindow.document.write(`
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
                        ${$(tablename).prop('outerHTML')}
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
                `);
                printWindow.document.close(); // Close the document to ensure it renders properly // Close the document to ensure it renders properly // Close the document to ensure it renders properly // Close the document to ensure it renders properly
            },
        },
        {
            extend: 'pdf',
            text: '<i class="text-danger fa fa-file-pdf" aria-hidden="true"></i> ',
            className: 'text-danger tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
            exportOptions: {
                columns: ':visible',
            },
            footer: true,
        },
        // <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
        //                     href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}">
        //                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        //                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        //                         class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
        //                         <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        //                         <path d="M12 5l0 14" />
        //                         <path d="M5 12l14 0" />
        //                     </svg> @lang('messages.add')
        //                 </a>
    ];

    if (non_utf8_languages.indexOf(app_locale) == -1) {
        buttons.push(pdf_btn);
    }

    if ($('#view_export_buttons').length < 1) {
        buttons = [];
    }

    // Add createFile function globally
    window.createFile = function () {
        const fileData = localStorage.getItem('pendingFile'); // Use 'pendingFile' if that's your key

        // Check if fileData exists and is valid JSON
        if (!fileData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No file data found in localStorage. Please save a file first.',
            });
            return;
        }

        let parsedData;
        try {
            parsedData = JSON.parse(fileData);
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid file data in localStorage.',
            });
            console.error('JSON Parse Error:', e);
            return;
        }

        // Ensure parsedData is an object
        if (!parsedData || typeof parsedData !== 'object') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid file data format in localStorage.',
            });
            return;
        }

        const fileName = parsedData.fileName || 'Untitled'; // Fallback if undefined
        const parentFolder = parsedData.folderId || '';

        if (!fileName) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a file name',
            });
            return;
        }

        Swal.fire({
            title: 'Saving...',
            text: 'Please wait while we save your file',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const table = $(tablename).DataTable();

        // Extract visible column names
        const visibleColumnIndexes = []; // Use a more descriptive name for the array
        table.columns().every(function (index) {
            if (this.visible()) {
                visibleColumnIndexes.push(index); // Push the column index instead of the name
            }
        });

        console.log(visibleColumnIndexes); // This will output an array of visible column indexes

        // Capture filter criteria based on reportName
        let filterCriteria = {};
        switch (reportName) {
            case 'saleReport':
                filterCriteria = {
                    dateRange: $('#sell_list_filter_date_range').val(),
                    locationId: $('#sell_list_filter_location_id').val(),
                    customerId: $('#sell_list_filter_customer_id').val(),
                    paymentStatus: $('#sell_list_filter_payment_status').val(),
                    createdBy: $('#created_by').val(),
                    salesCommissionAgent: $('#sales_cmsn_agnt').val(),
                    serviceStaff: $('#service_staffs').val(),
                    shippingStatus: $('#shipping_status').val(),
                    source: $('#sell_list_filter_source').val(),
                    paymentMethod: $('#payment_method').val(),
                    onlySubscriptions: $('#only_subscriptions').is(':checked') ? 1 : 0,
                };
                break;

            case 'purchaseReport':
                filterCriteria = {
                    locationId: $('#purchase_list_filter_location_id').val(), // Modified ID
                    supplierId: $('#purchase_list_filter_supplier_id').val(), // Added supplierId
                    status: $('#purchase_list_filter_status').val(), // Added purchase status
                    paymentStatus: $('#purchase_list_filter_payment_status').val(), //Correct ID
                    dateRange: $('#purchase_list_filter_date_range').val(), //Modified ID
                    //The rest are not present, so, it must be null/empty/ignored
                };
                break;

            case 'payrollReport':
                filterCriteria = {
                    dateRange: $('#month_year_filter').val(), // Updated to match the Blade template
                    locationId: $('#location_id_filter').val(), // Updated to match the Blade template
                    userId: $('#user_id_filter').val(), // Updated to match the Blade template
                    departmentId: $('#department_id').val(), // Updated to match the Blade template
                    designationId: $('#designation_id').val(), // Updated to match the Blade template
                };
                break;

            case 'productReport':
            case 'stockReport':
                filterCriteria = {
                    dateRange: $('#month_year_filter').val(), // Assuming this is a date range picker
                    locationId: $('#location_id_filter').val(), // Assuming this is the location filter
                    type: $('#product_list_filter_type').val(), // Product type filter
                    categoryId: $('#product_list_filter_category_id').val(), // Category filter
                    unitId: $('#product_list_filter_unit_id').val(), // Unit filter
                    taxId: $('#product_list_filter_tax_id').val(), // Tax filter
                    brandId: $('#product_list_filter_brand_id').val(), // Brand filter
                    activeState: $('#active_state').val(), // Active state filter
                    notForSelling: $('#not_for_selling').is(':checked') ? 1 : 0, // Not for selling checkbox
                    woocommerceEnabled: $('#woocommerce_enabled').is(':checked') ? 1 : 0, // WooCommerce enabled checkbox
                };
                break;

            case 'expenseReport':
                filterCriteria = {
                    locationId: $('#location_id').val(),
                    expenseFor: $('#expense_for').val(),
                    expenseContact: $('#expense_contact_filter').val(),
                    expenseContactId: $('#expense_contact_id').val(),
                    expenseSubCategoryId: $('#expense_sub_category_id_filter').val(),
                    expenseCategoryId: $('#expense_category_id').val(),
                    dateRange: $('#expense_date_range').val(),
                    paymentStatus: $('#expense_payment_status').val(),
                    auditStatus: $('#audit_status').val(),
                };
                break;

            case 'followupReport':
                filterCriteria = {
                    contactId: $('#contact_id_filter').val(),
                    assignedTo: $('#assgined_to_filter').val(),
                    status: $('#status_filter').val(),
                    scheduleType: $('#schedule_type_filter').val(),
                    dateRange: $('#follow_up_date_range').val(),
                    followUpBy: $('#follow_up_by_filter').val(),
                    followupCategoryId: $('#followup_category_id_filter').val(),
                };

            case 'customerReport':
                filterCriteria = {
                    hasSellDue: $('#has_sell_due').is(':checked') ? 1 : 0, // Checkbox returns 1 if checked, 0 if not
                    hasStudyDate:$('#has_study_date').is(':checked') ? 1 : 0,
                    hasExpiredDate:$('#has_expired_date').is(':checked') ? 1 : 0,
                    hasRegisterDate:$('#has_register_date').is(':checked') ? 1 : 0,
                    hasSellReturn: $('#has_sell_return').is(':checked') ? 1 : 0,
                    hasAdvanceBalance: $('#has_advance_balance').is(':checked') ? 1 : 0,
                    hasOpeningBalance: $('#has_opening_balance').is(':checked') ? 1 : 0,
                    hasNoSellFrom: $('#has_no_sell_from').val(), // Select value (e.g., 'one_month')
                    customerGroup: $('#cg_filter').val(), // Customer group filter
                    assignedTo: $('#assigned_to').val(), // Assigned to user (if enabled)
                    status: $('#status_filter').val(), // Status (active/inactive)
                    searchKeyword: $('#search_keyword').val(), // Search keyword text
                    dateRange: $('#contact_date_range').val(), // Date range
                };
                break;

            case 'supplierReport':
                filterCriteria = {
                    hasPurchaseDue: $('#has_purchase_due').is(':checked') ? 1 : 0,
                    hasPurchaseReturn: $('#has_purchase_return').is(':checked') ? 1 : 0,
                    hasAdvanceBalance: $('#has_advance_balance').is(':checked') ? 1 : 0,
                    hasOpeningBalance: $('#has_opening_balance').is(':checked') ? 1 : 0,
                    assignedTo: $('#assigned_to').val(),
                    status: $('#status_filter').val(),
                    dateRange: $('#contact_date_range').val(),
                };
                break;

            case 'employeeReport':
                filterCriteria = {
                    allowLogin: $('#allow_login').is(':checked') ? 1 : 0, // Checkbox returns 1 if checked, 0 if not
                    notAllowLogin: $('#not_allow_login').is(':checked') ? 1 : 0, // Checkbox returns 1 if checked, 0 if not
                    user: $('#user').val(), // Selected user ID
                    role: $('#role').val(), // Selected role ID
                    searchKeyword: $('#search_keyword').val(), // Search keyword text (if applicable)
                    status: $('#status_filter').val(), // Status filter (e.g., active/inactive, if applicable)
                    dateRange: $('#employee_date_range').val(), // Date range (if applicable)
                };
                break;

            default:
                console.warn(`Unknown reportName: ${reportName}`);
                break;
        }

        // Prepare data
        const tableData = {
            reportName: reportName,
            visibleColumnNames: visibleColumnIndexes,
            filterCriteria: filterCriteria,
        };

        // Send to server
        $.ajax({
            url: '/minireportb1/create',
            method: 'POST',
            contentType: 'application/json; charset=UTF-8', // Explicitly set UTF-8 encoding
            data: JSON.stringify({
                file_name: fileName,
                parent_id: parentFolder,
                table_data: tableData, // Ensure tableData is a JavaScript object, not a JSON string
                _token: $('meta[name="csrf-token"]').attr('content'),
            }),
            success: function (response) {
                $('#saveViewModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.msg,
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(() => {
                        console.log('Table Data:', tableData);
                        window.location.href = '/minireportb1/MiniReportB1';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg || 'Error saving file',
                    });
                }
            },
            error: function (xhr, status, error) {
                $('#saveViewModal').modal('hide');
                console.error('Save error:', error);
                console.error('Response:', xhr.responseText); // Log full response for debugging
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error saving file: ' + error,
                });
            },
        });
    };

    var pdf_btn = {};

    if (non_utf8_languages.indexOf(app_locale) == -1) {
        buttons.push(pdf_btn);
    }

    // Datatables initialization
    jQuery.extend($.fn.dataTable.defaults, {
        fixedHeader: true,
        dom: '<"row margin-bottom-20"<"col-sm-4"l><"col-sm-4 text-center"f><"col-sm-4 text-right"B>>tip',
        buttons: buttons, // The modified buttons array
        aLengthMenu: [
            [25, 50, 100, 200, 500, 1000, -1],
            [25, 50, 100, 200, 500, 1000, LANG.all],
        ],
        iDisplayLength: __default_datatable_page_entries,
        language: {
            searchPlaceholder: LANG.search + ' ...',
            search: '',
            lengthMenu: LANG.show + ' _MENU_ ' + LANG.entries,
            emptyTable: LANG.table_emptyTable,
            info: LANG.table_info,
            infoEmpty: LANG.table_infoEmpty,
            loadingRecords: LANG.table_loadingRecords,
            processing: LANG.table_processing,
            zeroRecords: LANG.table_zeroRecords,
            paginate: {
                first: LANG.first,
                last: LANG.last,
                next: LANG.next,
                previous: LANG.previous,
            },
        },
    });
    if ($('input#iraqi_selling_price_adjustment').length > 0) {
        iraqi_selling_price_adjustment = true;
    } else {
        iraqi_selling_price_adjustment = false;
    }

    // Input number
    $(document).on(
        'click',
        '.input-number .quantity-up, .input-number .quantity-down',
        function () {
            var input = $(this).closest('.input-number').find('input');
            var qty = __read_number(input);
            var step = 1;
            if (input.data('step')) {
                step = input.data('step');
            }
            var min = parseFloat(input.data('min'));
            var max = parseFloat(input.data('max'));

            if ($(this).hasClass('quantity-up')) {
                // If max reached, return false
                if (typeof max != 'undefined' && qty + step > max) {
                    return false;
                }

                __write_number(input, qty + step);
                input.change();
            } else if ($(this).hasClass('quantity-down')) {
                // If min reached, return false
                if (typeof min != 'undefined' && qty - step < min) {
                    return false;
                }

                __write_number(input, qty - step);
                input.change();
            }
        }
    );

    $('div.pos-tab-menu>div.list-group>a').click(function (e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass('active');
        $(this).addClass('active');
        var index = $(this).index();
        $('div.pos-tab>div.pos-tab-content').removeClass('active');
        $('div.pos-tab>div.pos-tab-content').eq(index).addClass('active');
    });

    $('.scroll-top-bottom').each(function () {
        $(this).topScrollbar();
    });

    $('.datetimepicker').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
});

// Default settings for daterangePicker
var ranges = {};
ranges[LANG.today] = [moment(), moment()];
ranges[LANG.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
ranges[LANG.last_7_days] = [moment().subtract(6, 'days'), moment()];
ranges[LANG.last_30_days] = [moment().subtract(29, 'days'), moment()];
ranges[LANG.this_month] = [moment().startOf('month'), moment().endOf('month')];
ranges[LANG.last_month] = [
    moment().subtract(1, 'month').startOf('month'),
    moment().subtract(1, 'month').endOf('month'),
];
ranges[LANG.this_month_last_year] = [
    moment().subtract(1, 'year').startOf('month'),
    moment().subtract(1, 'year').endOf('month'),
];
ranges[LANG.this_year] = [moment().startOf('year'), moment().endOf('year')];
ranges[LANG.last_year] = [
    moment().startOf('year').subtract(1, 'year'),
    moment().endOf('year').subtract(1, 'year'),
];
ranges[LANG.this_financial_year] = [financial_year.start, financial_year.end];
ranges[LANG.last_financial_year] = [
    moment(financial_year.start._i).subtract(1, 'year'),
    moment(financial_year.end._i).subtract(1, 'year'),
];

var dateRangeSettings = {
    ranges: ranges,
    startDate: financial_year.start,
    endDate: financial_year.end,
    locale: {
        cancelLabel: LANG.clear,
        applyLabel: LANG.apply,
        customRangeLabel: LANG.custom_range,
        format: moment_date_format,
        toLabel: '~',
    },
};

// Check for number string in input field, if data-decimal is 0 then don't allow decimal symbol and if no_neg then don't allow negative value
$(document).on('keypress', 'input.input_number', function (event) {
    var is_decimal = $(this).data('decimal');

    if (is_decimal == 0) {
        if (__currency_decimal_separator == '.') {
            var regex = new RegExp(/^[0-9,-]+$/);
        } else {
            var regex = new RegExp(/^[0-9.-]+$/);
        }
    } else {
        var regex = new RegExp(/^[0-9.,-]+$/);
    }

    // Check for no negative values
    if (is_decimal == 'no_neg') {
        var regex = new RegExp(/^[0-9.,]+$/);
    }

    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
});

// Select all input values on click
$(document).on('click', 'input', function (event) {
    $(this).select();
});

$(document).on('click', '.toggle-font-size', function (event) {
    localStorage.setItem('upos_font_size', $(this).data('size'));
    update_font_size();
});
$(document).on('click', '.sidebar-toggle', function () {
    var sidebar_collapse = localStorage.getItem('upos_sidebar_collapse');
    if ($('body').hasClass('sidebar-collapse')) {
        localStorage.setItem('upos_sidebar_collapse', 'false');
    } else {
        localStorage.setItem('upos_sidebar_collapse', 'true');
    }
});

// Ask for confirmation for links
$(document).on('click', 'a.link_confirmation', function (e) {
    e.preventDefault();
    swal({
        title: LANG.sure,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((confirmed) => {
        if (confirmed) {
            window.location.href = $(this).attr('href');
        }
    });
});

// Change max quantity rule if lot number changes
$('table#stock_adjustment_product_table tbody').on('change', 'select.lot_number', function () {
    var tr = $(this).closest('tr');
    var qty_element = tr.find('input.product_quantity');
    var qty_available_el = tr.find('.qty_available_text');

    var multiplier = 1;
    var unit_name = '';
    var sub_unit_length = tr.find('select.sub_unit').length;
    if (sub_unit_length > 0) {
        var select = tr.find('select.sub_unit');
        multiplier = parseFloat(select.find(':selected').data('multiplier'));
        unit_name = select.find(':selected').data('unit_name');
    }

    if ($(this).val()) {
        var lot_qty = $('option:selected', $(this)).data('qty_available');
        var max_err_msg = $('option:selected', $(this)).data('msg-max');

        if (sub_unit_length > 0) {
            lot_qty = lot_qty / multiplier;
            var lot_qty_formated = __number_f(lot_qty, false);
            max_err_msg = __translate('lot_max_qty_error', {
                max_val: lot_qty_formated,
                unit_name: unit_name,
            });
        }

        qty_element.attr('data-rule-max-value', lot_qty);
        qty_element.attr('data-msg-max-value', max_err_msg);

        qty_element.rules('add', {
            'max-value': lot_qty,
            messages: {
                'max-value': max_err_msg,
            },
        });
        if (qty_available_el.length) {
            qty_available_el.text(__currency_trans_from_en(lot_qty, false));
        }
    } else {
        var default_qty = qty_element.data('qty_available');
        var default_err_msg = qty_element.data('msg_max_default');

        if (sub_unit_length > 0) {
            default_qty = default_qty / multiplier;
            var lot_qty_formated = __number_f(default_qty, false);
            default_err_msg = __translate('pos_max_qty_error', {
                max_val: lot_qty_formated,
                unit_name: unit_name,
            });
        }

        qty_element.attr('data-rule-max-value', default_qty);
        qty_element.attr('data-msg-max-value', default_err_msg);

        qty_element.rules('add', {
            'max-value': default_qty,
            messages: {
                'max-value': default_err_msg,
            },
        });

        if (qty_available_el.length) {
            qty_available_el.text(__currency_trans_from_en(default_qty, false));
        }
    }
    qty_element.trigger('change');
});
$('button#btnCalculator, button#return_sale').hover(function () {
    $(this).tooltip('show');
});
$('button#return_sale').click(function () {
    $(this).popover('toggle');
});
$('button#service_staff_replacement').click(function () {
    $(this).popover('toggle');
});
$(document).on('mouseleave', 'button#btnCalculator, button#return_sale', function (e) {
    $(this).tooltip('hide');
});

jQuery.validator.addMethod(
    'min-value',
    function (value, element, param) {
        return this.optional(element) || !(param > __number_uf(value));
    },
    function (params, element) {
        return $(element).data('min-value');
    }
);

$(document).on('click', '.view_uploaded_document', function (e) {
    e.preventDefault();
    var src = $(this).data('href');
    var html =
        '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><img src="' +
        src +
        '" class="img-responsive" alt="Uploaded Document"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <a href="' +
        src +
        '" class="btn btn-success" download=""><i class="fa fa-download"></i> Download</a></div></div></div>';
    $('div.view_modal').html(html).modal('show');
});

$(document).on('click', '#accordion .box-header', function (e) {
    if (e.target.tagName == 'A' || e.target.tagName == 'I') {
        return false;
    }
    $(this).find('.box-title a').click();
});

$(document).on('shown.bs.modal', '.contains_select2, .view_modal', function () {
    $(this)
        .find('.select2')
        .each(function () {
            var $p = $(this).parent();
            $(this).select2({ dropdownParent: $p });
        });
});

// Common configuration: tinyMCE editor
tinymce.overrideDefaults({
    height: 300,
    theme: 'silver',
    plugins: [
        'advlist autolink link image lists charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
        'table template paste help',
    ],
    toolbar:
        'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify |' +
        ' bullist numlist outdent indent | link image | print preview media fullpage | ' +
        'forecolor backcolor',
    menu: {
        favs: { title: 'My Favorites', items: 'code | searchreplace' },
    },
    menubar: 'favs file edit view insert format tools table help',
});

// Prevent Bootstrap dialog from blocking focusin
$(document).on('focusin', function (e) {
    if ($(e.target).closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root').length) {
        e.stopImmediatePropagation();
    }
});

// Search parameter in URL
function urlSearchParam(param) {
    var results = new RegExp('[?&]' + param + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    } else {
        return results[1];
    }
}

function updateOnlineStatus() {
    if (!__is_online()) {
        $('#online_indicator').removeClass('text-success');
        $('#online_indicator').addClass('text-danger');
    } else {
        $('#online_indicator').removeClass('text-danger');
        $('#online_indicator').addClass('text-success');
    }
}

$(document).on('change', '.cash_denomination', function () {
    var total = 0;
    var table = $(this).closest('table');
    table.find('tbody tr').each(function () {
        var denomination = parseFloat($(this).find('.cash_denomination').attr('data-denomination'));
        var count = $(this).find('.cash_denomination').val()
            ? parseInt($(this).find('.cash_denomination').val())
            : 0;
        var subtotal = denomination * count;
        total = total + subtotal;
        $(this).find('span.denomination_subtotal').text(__currency_trans_from_en(subtotal, true));
    });

    table.find('span.denomination_total').text(__currency_trans_from_en(total, true));
    table.find('input.denomination_total_amount').val(total);
});

// Autofocus select2 search input
let forceFocusFn = function () {
    // Gets the search input of the opened select2
    var searchInput = document.querySelector('.select2-container--open .select2-search__field');
    // If exists
    if (searchInput) searchInput.focus(); // focus
};

// Every time a select2 is opened
$(document).on('select2:open', () => {
    // We use a timeout because when a select2 is already opened and you open a new one, it has to wait to find the appropriate
    setTimeout(() => forceFocusFn(), 200);
});

function copyToClipboard(element_id) {
    var temp = $('<input>');
    $('body').append(temp);
    temp.val($('#' + element_id).text()).select();
    document.execCommand('copy');
    temp.remove();
    toastr.success(LANG.copied_to_clipboard);
}
