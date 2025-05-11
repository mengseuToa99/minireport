document.getElementById('printButton').addEventListener('click', function () {
    const tableHTML = document.querySelector(tablename)?.outerHTML;
    const reportName = reportname;

    console.log(reportName);

    if (!tableHTML) {
        console.error('Table not found');
        return;
    }

    localStorage.setItem('savedTable', tableHTML);
    localStorage.setItem('reportname', reportName);

    console.log('Redirecting to print route...');
    window.location.href = '/minireportb1/print';
});

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('row-select').addEventListener('change', function () {
        const selectedValue = this.value; // Get the selected value
        const rows = document.querySelectorAll('.table-row'); // Get all table rows

        if (selectedValue === 'all') {
            // Show all rows
            rows.forEach((row) => (row.style.display = ''));
        } else {
            const limit = parseInt(selectedValue); // Convert to number
            rows.forEach((row, index) => {
                if (index < limit) {
                    row.style.display = ''; // Show rows within the limit
                } else {
                    row.style.display = 'none'; // Hide rows beyond the limit
                }
            });
        }
    });

    // Trigger the change event initially to set the default value
    document.getElementById('row-select').dispatchEvent(new Event('change'));
});

document.getElementById('exportExcelButton')?.addEventListener('click', function () {
    const table = document.querySelector(tablename);
    const rows = table.querySelectorAll('tr');
    let csvContent = 'data:text/csv;charset=utf-8,\uFEFF'; // Add BOM for UTF-8

    rows.forEach(function (row) {
        let rowData = [];
        row.querySelectorAll('th, td').forEach(function (cell) {
            let cellText = cell.innerText;

            // Check if the cell content is a long number (e.g., 8847780000000)
            if (!isNaN(cellText) && cellText.length > 10) {
                // Format the number as a string to prevent scientific notation in Excel
                cellText = `="${cellText}"`;
            }

            rowData.push(cellText);
        });
        csvContent += rowData.join(',') + '\r\n';
    });

    const cleanTableName = tablename.replace(/[#\.]/g, '');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `${cleanTableName}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});
