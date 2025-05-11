/**
 * Handle Tax Tab Activation
 * This script manages the activation of the tax tab when the URL contains the #tax hash
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if the URL has the #tax hash
    if (window.location.hash === '#tax') {
        console.log('Tax hash detected in URL');
        
        // Use setTimeout to ensure the tabs have been initialized by Bootstrap
        setTimeout(function() {
            // Find the tax tab element
            const taxTab = document.getElementById('tax-tab');
            
            if (taxTab) {
                // Click the tab to activate it
                taxTab.click();
                console.log('Tax tab activated via script');
                
                // Scroll to the tab area
                taxTab.scrollIntoView({ behavior: 'smooth' });
            } else {
                console.warn('Tax tab element not found');
            }
        }, 300); // Wait 300ms to ensure the DOM is fully processed
    }
    
    // Also add listeners to all tabs to update the URL hash
    const tabs = document.querySelectorAll('[data-toggle="tab"]');
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            // Get the ID without the '-tab' suffix
            const tabId = e.target.id.replace('-tab', '');
            
            // Update the URL hash without causing a page reload
            if (history.pushState) {
                history.pushState(null, null, '#' + tabId);
            } else {
                window.location.hash = tabId;
            }
        });
    });
}); 