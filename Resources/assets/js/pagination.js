
let currentPage = 1;
let totalPages = 1;
let rowLimit = 10;
// Store page totals
let pageTotals = {
    netPrice: '0.00',
    crossSale: '0.00',
    khrAmount: '0'
};

function calculatePageTotals() {
    let netPriceTotal = 0;
    let crossSaleTotal = 0;
    let khrTotal = 0;
    
    // Loop through each row in the table body
    $('#income-table-body tr').each(function() {
        const cells = $(this).find('td');
        if (cells.length > 1) { // Skip any message rows
            // Parse the numeric values from the cells (removing formatting)
            const netPrice = parseFloat($(cells[3]).text().replace(/,/g, '')) || 0;
            const crossSale = parseFloat($(cells[5]).text().replace(/,/g, '')) || 0;
            const khrAmount = parseFloat($(cells[10]).text().replace(/,/g, '')) || 0;
            
            // Add to totals
            netPriceTotal += netPrice;
            crossSaleTotal += crossSale;
            khrTotal += khrAmount;
        }
    });
    
    // Format the totals
    pageTotals.netPrice = number_format(netPriceTotal, 2, '.', ',');
    pageTotals.crossSale = number_format(crossSaleTotal, 2, '.', ',');
    pageTotals.khrAmount = number_format(khrTotal, 0, '.', ',');
    
    // Update the display
    $('#total-net-price').html('<strong>' + pageTotals.netPrice + '</strong>');
    $('#total-cross-sale').html('<strong>' + pageTotals.crossSale + '</strong>');
    $('#total-khr').html('<strong>' + pageTotals.khrAmount + '</strong>');
}


            // Row limit change event
            $('#row-limit').on('change', function() {
                rowLimit = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing limit
                loadData();
            });

            // Pagination events
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadData();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadData();
                }
            });

              // Initial data load
              loadData();
            
              // Helper function to format numbers
              function number_format(number, decimals, dec_point, thousands_sep) {
                  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                  var n = !isFinite(+number) ? 0 : +number,
                      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                      s = '',
                      toFixedFix = function (n, prec) {
                          var k = Math.pow(10, prec);
                          return '' + Math.round(n * k) / k;
                      };
                  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                  if (s[0].length > 3) {
                      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                  }
                  if ((s[1] || '').length < prec) {
                      s[1] = s[1] || '';
                      s[1] += new Array(prec - s[1].length + 1).join('0');
                  }
                  return s.join(dec);
              }