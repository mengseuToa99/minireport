/**
 * Global Loading Functionality
 * This script provides a simple loading overlay for all report pages.
 */

// Define loader functions in the global scope
window.showLoader = function() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.classList.remove('loader-hidden');
        loader.classList.add('loader-active');
        loader.style.display = 'flex';
    }
};

window.hideLoader = function() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.classList.add('loader-hidden');
        loader.classList.remove('loader-active');
        setTimeout(function() {
            if (loader.classList.contains('loader-hidden')) {
                loader.style.display = 'none';
            }
        }, 500);
    }
};

// When document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Show the loader initially
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.style.display = 'flex';
        loader.classList.add('loader-active');
        loader.classList.remove('loader-hidden');
    }
    
    // Set up click handlers for all report links
    setupReportLinkHandlers();
    
    // Set up AJAX request handlers if jQuery is available
    setupAjaxHandlers();
    
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        window.hideLoader();
    });
});

// Setup all report link handlers
function setupReportLinkHandlers() {
    try {
        // Find and handle all report links
        const reportLinks = document.querySelectorAll('a.report-link, a[href*="report"], a[href*="statement"]');
        if (reportLinks.length > 0) {
            reportLinks.forEach(function(link) {
                if (link) {
                    link.addEventListener('click', function(e) {
                        // Don't interfere with ctrl/cmd+click or links with target="_blank"
                        if (!e.ctrlKey && !e.metaKey && !this.getAttribute('target')) {
                            e.preventDefault();
                            window.showLoader();
                            setTimeout(function() {
                                window.location.href = link.href;
                            }, 100);
                        }
                    });
                }
            });
        }
        
        // Handle all form submissions
        const forms = document.querySelectorAll('form');
        if (forms.length > 0) {
            forms.forEach(function(form) {
                if (form) {
                    form.addEventListener('submit', function() {
                        window.showLoader();
                    });
                }
            });
        }
        
        // Handle filter/search buttons
        const filterButtons = document.querySelectorAll('.apply-filter-btn, .filter-btn, .search-btn');
        if (filterButtons.length > 0) {
            filterButtons.forEach(function(button) {
                if (button) {
                    button.addEventListener('click', function() {
                        window.showLoader();
                    });
                }
            });
        }
    } catch (error) {
        console.error("Error setting up report link handlers:", error);
    }
}

// Setup AJAX request handlers
function setupAjaxHandlers() {
    try {
        if (typeof jQuery !== 'undefined') {
            // Show loader during AJAX requests
            jQuery(document).ajaxStart(function() {
                window.showLoader();
            }).ajaxStop(function() {
                window.hideLoader();
            });
            
            // DataTables specific loading if DataTables exists
            if (typeof jQuery.fn !== 'undefined' && typeof jQuery.fn.dataTable !== 'undefined') {
                jQuery(document).on('preXhr.dt', function() {
                    window.showLoader();
                });
                
                jQuery(document).on('draw.dt', function() {
                    window.hideLoader();
                });
            }
        }
    } catch (error) {
        console.error("Error setting up AJAX handlers:", error);
    }
}

// Expose globally
window.setupReportLinkHandlers = setupReportLinkHandlers;
window.setupAjaxHandlers = setupAjaxHandlers; 