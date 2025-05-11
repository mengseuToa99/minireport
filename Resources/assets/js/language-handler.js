/**
 * Language Handler for MiniReportB1 module
 * 
 * This file ensures correct language switching between English and Khmer
 * for the MiniReportB1 module's JavaScript components.
 */

$(document).ready(function() {
    // Get the current locale from the app
    var currentLocale = app_locale || 'en';
    
    // Add a visible indicator of the current language (for debugging)
    if ($('.language-indicator').length === 0) {
        $('body').append('<div class="language-indicator" style="position: fixed; bottom: 10px; right: 10px; background: rgba(0,0,0,0.5); color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; z-index: 9999;">Language: ' + currentLocale + '</div>');
    }
    
    // Force reload of language strings based on user's current language
    if (typeof LANG !== 'undefined') {
        console.log('MiniReportB1: Language handler initialized with locale: ' + currentLocale);
        
        // Check if we're using the MiniReportB1 module
        if (window.location.href.indexOf('minireportb1') > -1) {
            // Ensure the module loads the correct language file with a random parameter to bust cache
            var timestamp = new Date().getTime();
            $.ajax({
                url: base_path + '/js/lang/' + currentLocale + '.js?v=' + timestamp,
                dataType: 'script',
                cache: false,
                success: function() {
                    console.log('MiniReportB1: Successfully loaded language file for ' + currentLocale);
                    // Refresh UI elements with new language
                    refreshLanguageStrings();
                    
                    // Force reload the page after language change if we detect a language switcher click
                    if (sessionStorage.getItem('language_just_changed') === 'true') {
                        sessionStorage.removeItem('language_just_changed');
                        location.reload(true);
                    }
                },
                error: function() {
                    console.error('MiniReportB1: Failed to load language file for ' + currentLocale + ', falling back to English');
                    // Try to load English as fallback
                    $.ajax({
                        url: base_path + '/js/lang/en.js?v=' + timestamp,
                        dataType: 'script',
                        cache: false,
                        success: function() {
                            refreshLanguageStrings();
                        }
                    });
                }
            });
        }
    }
    
    // Attach click handler to language switcher buttons
    $('.language-switcher a').on('click', function() {
        sessionStorage.setItem('language_just_changed', 'true');
    });
    
    // Function to refresh UI elements with new language strings
    function refreshLanguageStrings() {
        // Update language indicator
        $('.language-indicator').text('Language: ' + currentLocale);
        
        // Update date range picker labels
        if (typeof dateRangeSettings !== 'undefined' && typeof LANG !== 'undefined') {
            dateRangeSettings.locale.cancelLabel = LANG.clear;
            dateRangeSettings.locale.applyLabel = LANG.apply;
            dateRangeSettings.locale.customRangeLabel = LANG.custom_range;
        }
        
        // Update DataTables language settings
        if ($.fn.dataTable && $.fn.dataTable.defaults) {
            $.fn.dataTable.defaults.language = {
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
                }
            };
            
            // Reinitialize any existing datatables
            $('.dataTable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                    $(this).DataTable();
                }
            });
        }
        
        // Refresh date ranges
        if (typeof ranges !== 'undefined' && typeof LANG !== 'undefined') {
            ranges = {};
            ranges[LANG.today] = [moment(), moment()];
            ranges[LANG.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            ranges[LANG.last_7_days] = [moment().subtract(6, 'days'), moment()];
            ranges[LANG.last_30_days] = [moment().subtract(29, 'days'), moment()];
            ranges[LANG.this_month] = [moment().startOf('month'), moment().endOf('month')];
            ranges[LANG.last_month] = [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month'),
            ];
            ranges[LANG.this_year] = [moment().startOf('year'), moment().endOf('year')];
            ranges[LANG.last_year] = [
                moment().startOf('year').subtract(1, 'year'),
                moment().endOf('year').subtract(1, 'year'),
            ];
        }
        
        // Force refresh any date pickers on the page
        $('.daterangepicker').remove();
        $('.date-range-picker').each(function() {
            $(this).daterangepicker(dateRangeSettings);
        });
        
        // Refresh any text elements that use LANG directly
        $('.lang-element').each(function() {
            var key = $(this).data('lang-key');
            if (key && LANG[key]) {
                $(this).text(LANG[key]);
            }
        });
        
        // Update HTML language attributes
        $('html').attr('lang', currentLocale);
    }
}); 