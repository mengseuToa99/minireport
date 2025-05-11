{{-- print button animation --}}
<style>
    button.print-button {
        width: 50px;
        height: 50px;
    }

    span.print-icon,
    span.print-icon::before,
    span.print-icon::after,
    button.print-button:hover .print-icon::after {
        border: solid 2px #0f8800;
    }

    span.print-icon::after {
        border-width: 1px;
    }

    button.print-button {
        margin-top: 8px;
        position: relative;
        padding: 0;
        border: 0;
        background: transparent;
    }

    span.print-icon,
    span.print-icon::before,
    span.print-icon::after,
    button.print-button:hover .print-icon::after {
        box-sizing: border-box;
        background-color: #fff;
    }

    span.print-icon {
        position: relative;
        display: inline-block;
        padding: 0;
        margin-top: 20%;
        width: 60%;
        height: 35%;
        background: #fff;
        border-radius: 20% 20% 0 0;
    }

    span.print-icon::before {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 12%;
        right: 12%;
        height: 110%;
        transition: height .2s .15s;
    }

    span.print-icon::after {
        content: "";
        position: absolute;
        top: 55%;
        left: 12%;
        right: 12%;
        height: 0%;
        background: #fff;
        background-repeat: no-repeat;
        background-size: 70% 90%;
        background-position: center;
        background-image: linear-gradient(to top,
                #fff 0, #fff 14%,
                #0f8800 14%, #0f8800 28%,
                #fff 28%, #fff 42%,
                #0f8800 42%, #0f8800 56%,
                #fff 56%, #fff 70%,
                #0f8800 70%, #0f8800 84%,
                #fff 84%, #fff 100%);
        transition: height .2s, border-width 0s .2s, width 0s .2s;
    }

    button.print-button:hover {
        cursor: pointer;
    }

    button.print-button:hover .print-icon::before {
        height: 0px;
        transition: height .2s;
    }

    button.print-button:hover .print-icon::after {
        height: 120%;
        transition: height .2s .15s, border-width 0s .16s;
    }

    a {
  color: inherit;          /* Inherits text color instead of default blue/purple */
  text-decoration: none;   /* Removes underline */
}

/* Optional: Reset different link states (hover, active, visited, focus) */
a:hover,
a:active,
a:visited,
a:focus {
  color: inherit;         /* Prevents purple for visited links */
  text-decoration: none;  /* Ensures no underline appears on hover/focus */
}
</style>

{{-- Hidden div to store the business info for JavaScript access --}}
@php
    // Get the business directly from the model for reliability
    $business_id = session('user.business_id');
    $directBusiness = \App\Business::find($business_id);
    $directLogoPath = $directBusiness && $directBusiness->logo ? '/uploads/business_logos/' . $directBusiness->logo : '';
@endphp
<div id="business-info" style="display: flex; align-items: center; margin-bottom: 10px;">
    @if($directBusiness && $directBusiness->logo)
   
    @endif
    <div style="display: none;">
        Business data: {{ $business_name ?? ($directBusiness->name ?? '') }}
    </div>
</div>

<button class="print-button" id="print-button" title="Print Report">
    <span class="print-icon"></span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const printButton = document.getElementById('print-button');
    
    if (!printButton) return;
    
    // Find business info directly from the page - more robust approach
    function getBusinessInfo() {
        // Get business name and logo from various possible locations
        let businessName = '';
        let businessLogo = '';

        // Try meta tags first (most reliable)
        const businessNameMeta = document.querySelector('meta[name="business-name"]');
        const businessLogoMeta = document.querySelector('meta[name="business-logo"]');
        
        if (businessNameMeta) {
            businessName = businessNameMeta.getAttribute('content');
        }
        
        if (businessLogoMeta) {
            businessLogo = businessLogoMeta.getAttribute('content');
        }
        
        // If meta tags didn't work, try looking for visible business elements
        if (!businessName) {
            const businessNameElement = document.querySelector('.business-name');
            if (businessNameElement) {
                businessName = businessNameElement.innerText.trim();
            }
        }
        
        // For logo, look for any business logo image on the page
        if (!businessLogo) {
            // Look in logo-test div first (our custom solution)
            const logoTestImg = document.querySelector('.logo-test img');
            if (logoTestImg && logoTestImg.src) {
                businessLogo = logoTestImg.src;
            } else {
                // Try to find any logo that looks like a business logo
                const possibleLogos = [
                    ...document.querySelectorAll('img[src*="business_logos"]'),
                    ...document.querySelectorAll('img[alt*="Logo"]'),
                    ...document.querySelectorAll('.business-logo')
                ];
                
                if (possibleLogos.length > 0) {
                    for (const logo of possibleLogos) {
                        if (logo.src) {
                            businessLogo = logo.src;
                            break;
                        }
                    }
                }
            }
        }
        
        return { name: businessName, logo: businessLogo };
    }
    
    printButton.addEventListener('click', function() {
        // Get business info directly rather than relying on specific DOM structure
        const businessInfo = getBusinessInfo();
        const businessName = businessInfo.name;
        const businessLogo = businessInfo.logo;
    
        // Find main report elements
        const mainTable = document.querySelector('.dataTable, .reusable-table, table.table');
        
        // Get report name - try multiple selectors to find the report name
        let reportTitle = '';
        const reportElements = [
            document.querySelector('.report-subtitle b'),
            document.querySelector('.normal-view-title:nth-child(2)'),
            document.querySelector('.card-title'),
            document.querySelector('title'),
            document.querySelector('h1'),
            document.querySelector('.page-title'),
            document.querySelector('.report-name')
        ];
        
        for (const element of reportElements) {
            if (element && element.innerText) {
                reportTitle = element.innerText.trim();
                break;
            }
        }
        
        if (!reportTitle) {
            reportTitle = 'Report';
        }

        // Remove business name from report title if it's appended at the end
        if (reportTitle.endsWith(businessName) && businessName !== '') {
            reportTitle = reportTitle.substring(0, reportTitle.length - businessName.length).trim();
            // Remove any dash, hyphen or separator at the end
            reportTitle = reportTitle.replace(/\s*[-—–]\s*$/, '');
        }
        
        // Get date range info
        let dateRangeText = '';
        const reportDateElement = document.querySelector('.report-date');
        
        if (reportDateElement && reportDateElement.innerText) {
            dateRangeText = `<p>Date Range: ${reportDateElement.innerText}</p>`;
        } else {
            // Try to get date filter values as fallback
            const dateFilter = document.querySelector('[name="date_filter"]')?.value || '';
            const startDate = document.querySelector('[name="start_date"]')?.value || '';
            const endDate = document.querySelector('[name="end_date"]')?.value || '';
            
            if (startDate && endDate) {
                dateRangeText = `Date Range: ${startDate} to ${endDate}`;
            } else if (dateFilter) {
                // Create more descriptive date ranges for standard filters
                const today = new Date();
                let fromDate = new Date();
                let toDate = new Date();
                
                switch(dateFilter) {
                    case 'today':
                        dateRangeText = `Today (${today.toLocaleDateString()})`;
                        break;
                    case 'yesterday':
                        fromDate.setDate(today.getDate() - 1);
                        dateRangeText = `Yesterday (${fromDate.toLocaleDateString()})`;
                        break;
                    case 'this_week':
                        fromDate.setDate(today.getDate() - today.getDay());
                        dateRangeText = `This Week (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                        break;
                    case 'this_month':
                        fromDate.setDate(1);
                        dateRangeText = `This Month (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                        break;
                    case 'last_month':
                        fromDate.setMonth(today.getMonth() - 1);
                        fromDate.setDate(1);
                        toDate.setDate(0); // Last day of previous month
                        dateRangeText = `Last Month (${fromDate.toLocaleDateString()} to ${toDate.toLocaleDateString()})`;
                        break;
                    case 'last_3_months':
                        fromDate.setMonth(today.getMonth() - 3);
                        fromDate.setDate(1);
                        dateRangeText = `Last 3 Months (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                        break;
                    case 'last_6_months':
                        fromDate.setMonth(today.getMonth() - 6);
                        fromDate.setDate(1);
                        dateRangeText = `Last 6 Months (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                        break;
                    case 'this_quarter':
                        fromDate.setMonth(Math.floor(today.getMonth() / 3) * 3);
                        fromDate.setDate(1);
                        dateRangeText = `This Quarter (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                        break;
                    case 'last_quarter':
                        fromDate.setMonth(Math.floor(today.getMonth() / 3) * 3 - 3);
                        fromDate.setDate(1);
                        toDate.setMonth(Math.floor(today.getMonth() / 3) * 3);
                        toDate.setDate(0);
                        dateRangeText = `Last Quarter (${fromDate.toLocaleDateString()} to ${toDate.toLocaleDateString()})`;
                        break;
                    case 'this_year':
                        fromDate.setMonth(0);
                        fromDate.setDate(1);
                        dateRangeText = `This Year (${fromDate.toLocaleDateString()} to ${today.toLocaleDateString()})`;
                        break;
                    case 'last_year':
                        fromDate.setFullYear(today.getFullYear() - 1);
                        fromDate.setMonth(0);
                        fromDate.setDate(1);
                        toDate.setFullYear(today.getFullYear() - 1);
                        toDate.setMonth(11);
                        toDate.setDate(31);
                        dateRangeText = `Last Year (${fromDate.toLocaleDateString()} to ${toDate.toLocaleDateString()})`;
                        break;
                    default:
                        const filterMapping = {
                            'today': 'Today',
                            'this_month': 'This Month',
                            'last_month': 'Last Month',
                            'last_3_months': 'Last 3 Months',
                            'last_6_months': 'Last 6 Months',
                            'this_quarter': 'This Quarter',
                            'last_quarter': 'Last Quarter',
                            'this_year': 'This Year',
                            'last_year': 'Last Year'
                        };
                        dateRangeText = `Date Filter: ${filterMapping[dateFilter] || dateFilter.replace(/_/g, ' ')}`;
                }
            }
        }
        
        // Get any additional filters
        let additionalFilters = '';
        
        // Employee/username filter
        const usernameFilter = document.querySelector('#username_filter');
        if (usernameFilter && usernameFilter.value) {
            const selectedOption = usernameFilter.options[usernameFilter.selectedIndex];
            if (selectedOption && selectedOption.text) {
                additionalFilters += `<p>Employee: ${selectedOption.text.trim()}</p>`;
            }
        }
        
        if (!mainTable) {
            // Fallback to simple print if no table found
            window.print();
            return;
        }
        
        // Check if using DataTables
        const isDataTable = typeof $.fn?.dataTable !== 'undefined' && 
                           mainTable && 
                           $.fn.dataTable.isDataTable(mainTable);
        
        if (isDataTable) {
            // For DataTables
            const table = $(mainTable).DataTable();
            const oldPageLength = table.page.len();
            const oldPage = table.page();
            
            // Show all data
            table.page.len(-1).draw(false);
            
            setTimeout(function() {
                // Copy the table with all data visible
                const tableHtml = mainTable.outerHTML;
                
                // Create a new window with better styling
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>${reportTitle}</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                margin: 20px; 
                                padding: 0; 
                                color: #333; 
                            }
                            .report-header {
                                margin-bottom: 20px;
                                display: flex;
                                flex-wrap: wrap;
                                justify-content: space-between;
                                align-items: center;
                                background-color: #f8f9fa;
                                padding: 15px;
                            }
                            .header-left {
                                display: flex;
                                align-items: center;
                                flex: 1;
                            }
                            .header-right {
                                flex: 1;
                                text-align: right;
                            }
                            .business-logo {
                                max-height: 50px;
                                max-width: 50px;
                                margin-right: 15px;
                            }
                            .business-name {
                                font-size: 20px;
                                font-weight: 600;
                            }
                            .report-name {
                                font-size: 22px;
                                font-weight: 600;
                                margin-bottom: 5px;
                            }
                            .date-range {
                                font-size: 14px;
                                margin-top: 5px;
                            }
                            .date-info {
                                text-align: center;
                                margin: 10px 0;
                                font-size: 14px;
                            }
                            .table-container {
                                width: 100%;
                                overflow-x: auto;
                                margin-bottom: 20px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                font-size: 12px;
                            }
                            table th {
                                background-color: #f8f9fa;
                                border: 1px solid #dee2e6;
                                padding: 8px;
                                text-align: left;
                                font-weight: bold;
                            }
                            table td {
                                border: 1px solid #dee2e6;
                                padding: 8px;
                                text-align: left;
                            }
                            table tr:nth-child(even) {
                                background-color: #f9f9f9;
                            }
                            .print-controls {
                                text-align: center;
                                margin: 20px 0;
                            }
                            .print-button-window {
                                display: block;
                                margin: 20px auto;
                                padding: 10px 20px;
                                background-color: #0f8800;
                                color: white;
                                border: none;
                                border-radius: 4px;
                                font-size: 14px;
                                cursor: pointer;
                            }
                            .top-controls {
                                text-align: right;
                                margin-bottom: 20px;
                            }
                            @media print {
                                .print-button-window, .top-controls {
                                    display: none;
                                }
                                body {
                                    margin: 0;
                                    padding: 0;
                                }
                                table th {
                                    background-color: #f8f9fa !important;
                                    color: #333 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                                table tr:nth-child(even) {
                                    background-color: #f9f9f9 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }
                                thead {
                                    display: table-header-group;
                                }
                                tr {
                                    page-break-inside: avoid;
                                }
                                .report-header {
                                    background-color: #f8f9fa !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }

                                a {
  color: inherit;          /* Inherits text color instead of default blue/purple */
  text-decoration: none;   /* Removes underline */
}

/* Optional: Reset different link states (hover, active, visited, focus) */
a:hover,
a:active,
a:visited,
a:focus {
  color: inherit;         /* Prevents purple for visited links */
  text-decoration: none;  /* Ensures no underline appears on hover/focus */
}
                            }
                        </style>
                    </head>
                    <body>
                        <div class="top-controls">
                            <button class="print-button-window" onclick="window.print()">Print Report</button>
                        </div>
                        
                        <div class="report-header">
                            <div class="header-left">
                                ${businessLogo ? `
                                <img 
                                    src="${businessLogo}" 
                                    alt="${businessName || 'Business'} Logo" 
                                    class="business-logo" 
                                    onerror="this.style.display='none';" 
                                    loading="lazy">
                                ` : ''}
                                <div class="business-name">${businessName || 'Business Name'}</div>
                            </div>
                            <div class="header-right">
                                <div class="report-name">${reportTitle}</div>
                                <div class="date-range">${dateRangeText.replace(/<p>/g, '').replace(/<\/p>/g, '')}</div>
                                <div class="date-range">Printed on: ${new Date().toLocaleString()}</div>
                            </div>
                        </div>
                        
                        <div class="date-info">
                            ${additionalFilters}
                        </div>
                        
                        <div class="table-container">
                            ${tableHtml}
                        </div>
                        
                        <div class="print-controls">
                            <button class="print-button-window" onclick="window.print()">Print Report</button>
                        </div>
                    </body>
                    </html>
                `);
                printWindow.document.close();
                
                // Reset DataTable to previous state
                setTimeout(function() {
                    table.page.len(oldPageLength).page(oldPage).draw(false);
                }, 500);
            }, 500);
        } else {
            // For regular tables
            // Make a copy of the table to handle pagination
            const tableClone = mainTable.cloneNode(true);
            if (tableClone.tagName.toLowerCase() === 'div') {
                // If the main table is actually a container div, find the table inside
                const tableElement = tableClone.querySelector('table');
                if (tableElement) {
                    tableClone = tableElement;
                }
            }
            
            // Remove hidden rows for printing (important for category/product filtering)
            const rowsToRemove = [];
            const rows = tableClone.querySelectorAll('tbody tr');
            rows.forEach(row => {
                // Check if the row is hidden by style or by any filter
                if (row.style.display === 'none') {
                    rowsToRemove.push(row);
                }
            });
            
            // Remove the hidden rows from the clone
            rowsToRemove.forEach(row => {
                row.parentNode.removeChild(row);
            });
            
            // Create a similar window but for standard tables
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${reportTitle}</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px; 
                            padding: 0; 
                            color: #333; 
                        }
                        .report-header {
                            margin-bottom: 20px;
                            display: flex;
                            flex-wrap: wrap;
                            justify-content: space-between;
                            align-items: center;
                            background-color: #f8f9fa;
                            padding: 15px;
                        }
                        .header-left {
                            display: flex;
                            align-items: center;
                            flex: 1;
                        }
                        .header-right {
                            flex: 1;
                            text-align: right;
                        }
                        .business-logo {
                            max-height: 50px;
                            max-width: 50px;
                            margin-right: 15px;
                        }
                        .business-name {
                            font-size: 20px;
                            font-weight: 600;
                        }
                        .report-name {
                            font-size: 22px;
                            font-weight: 600;
                            margin-bottom: 5px;
                        }
                        .date-range {
                            font-size: 14px;
                            margin-top: 5px;
                        }
                        .date-info {
                            text-align: center;
                            margin: 10px 0;
                            font-size: 14px;
                        }
                        .table-container {
                            width: 100%;
                            overflow-x: auto;
                            margin-bottom: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            font-size: 12px;
                        }
                        table th {
                            background-color: #f8f9fa;
                            border: 1px solid #dee2e6;
                            padding: 8px;
                            text-align: left;
                            font-weight: bold;
                        }
                        table td {
                            border: 1px solid #dee2e6;
                            padding: 8px;
                            text-align: left;
                        }
                        table tr:nth-child(even) {
                            background-color: #f9f9f9;
                        }
                        .print-controls {
                            text-align: center;
                            margin: 20px 0;
                        }
                        .print-button-window {
                            display: block;
                            margin: 20px auto;
                            padding: 10px 20px;
                            background-color: #0f8800;
                            color: white;
                            border: none;
                            border-radius: 4px;
                            font-size: 14px;
                            cursor: pointer;
                        }
                        .top-controls {
                            text-align: right;
                            margin-bottom: 20px;
                        }
                        @media print {
                            .print-button-window, .top-controls {
                                display: none;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                            }
                            table th {
                                background-color: #f8f9fa !important;
                                color: #333 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            table tr:nth-child(even) {
                                background-color: #f9f9f9 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            thead {
                                display: table-header-group;
                            }
                            tr {
                                page-break-inside: avoid;
                            }
                            .report-header {
                                background-color: #f8f9fa !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }

                            a {
  color: inherit;          /* Inherits text color instead of default blue/purple */
  text-decoration: none;   /* Removes underline */
}

/* Optional: Reset different link states (hover, active, visited, focus) */
a:hover,
a:active,
a:visited,
a:focus {
  color: inherit;         /* Prevents purple for visited links */
  text-decoration: none;  /* Ensures no underline appears on hover/focus */
}
                        }
                    </style>
                </head>
                <body>
                    <div class="top-controls">
                        <button class="print-button-window" onclick="window.print()">Print Report</button>
                    </div>
                    
                    <div class="report-header">
                        <div class="header-left">
                            ${businessLogo ? `
                            <img 
                                src="${businessLogo}" 
                                alt="${businessName || 'Business'} Logo" 
                                class="business-logo" 
                                onerror="this.style.display='none';" 
                                loading="lazy">
                            ` : ''}
                            <div class="business-name">${businessName || 'Business Name'}</div>
                        </div>
                        <div class="header-right">
                            <div class="report-name">${reportTitle}</div>
                            <div class="date-range">${dateRangeText.replace(/<p>/g, '').replace(/<\/p>/g, '')}</div>
                            <div class="date-range">Printed on: ${new Date().toLocaleString()}</div>
                        </div>
                    </div>
                    
                    <div class="date-info">
                        ${additionalFilters}
                    </div>
                    
                    <div class="table-container">
                        ${tableClone.outerHTML}
                    </div>
                    
                   
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    });
});
</script>