{{-- This file is kept for backwards compatibility --}}
{{-- The loading functionality is now provided by the master layout --}}

<style>
    /* Loading Overlay */
    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #loading-overlay.visible {
        opacity: 1;
    }

    /* Loading Container */
    .loading-container {
        text-align: center;
    }

    .loading-message {
        margin-top: 20px;
        font-size: 18px;
        color: #0f8800;
        font-weight: 500;
    }

    /* Loader Animation - Enhanced */
    .loader {
        width: 48px;
        aspect-ratio: .75;
        --c: no-repeat linear-gradient(#0f8800 0 0);
        background:
            var(--c) 0% 100%,
            var(--c) 50% 100%,
            var(--c) 100% 100%;
        background-size: 20% 65%;
        animation: l8 1s infinite cubic-bezier(0.215, 0.61, 0.355, 1);
        filter: drop-shadow(0 0 8px rgba(15, 136, 0, 0.2));
    }

    @keyframes l8 {
        16.67% { background-position: 0% 0%, 50% 100%, 100% 100% }
        33.33% { background-position: 0% 0%, 50% 0%, 100% 100% }
        50% { background-position: 0% 0%, 50% 0%, 100% 0% }
        66.67% { background-position: 0% 100%, 50% 0%, 100% 0% }
        83.33% { background-position: 0% 100%, 50% 100%, 100% 0% }
    }

    /* Business Overview Styles */
    .sprite-image3 {
        margin-left: auto;
        margin-top: -200px;
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px;
        background-position: -300px -400px;
        background-repeat: no-repeat;
    }

    .report-box {
        margin: 10px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .report-box:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .report-link {
        color: #333;
        text-decoration: none;
    }

    .favorite-icon {
        cursor: pointer;
        margin-right: 10px;
    }
</style>


<!-- Loading Overlay -->
<div id="component-loading-overlay">
    <div class="loading-container">
        <div class="loader"></div>
        <div class="loading-message">Loading your report...</div>
    </div>
</div>


<!-- JavaScript -->
<script>
    // This ensures the loading.blade.php component doesn't interfere with the global loader
    document.addEventListener('DOMContentLoaded', function() {
        // Make sure global loader functions exist
        if (!window.showLoader) {
            console.warn('Global showLoader function is missing. Loading component may not work correctly.');
        }
        
        if (!window.hideLoader) {
            console.warn('Global hideLoader function is missing. Loading component may not work correctly.');
        }
    });

    // Check if global loader functions exist, otherwise create them
    if (typeof window.showLoader !== 'function') {
        window.showLoader = function() {
            // Try main loader first
            const mainLoader = document.getElementById('loading-overlay');
            if (mainLoader) {
                mainLoader.style.display = 'flex';
                mainLoader.offsetHeight; // Force reflow
                mainLoader.classList.remove('fade-out');
                if (mainLoader.classList.contains('visible')) {
                    mainLoader.classList.add('visible');
                }
                return;
            }
            
            // Fall back to component loader
            const compLoader = document.getElementById('component-loading-overlay');
            if (compLoader) {
                compLoader.style.display = 'flex';
                compLoader.offsetHeight; // Force reflow
                compLoader.classList.add('visible');
            }
        };
    }
    
    if (typeof window.hideLoader !== 'function') {
        window.hideLoader = function() {
            // Try main loader first
            const mainLoader = document.getElementById('loading-overlay');
            if (mainLoader) {
                mainLoader.classList.add('fade-out');
                setTimeout(() => {
                    mainLoader.style.display = 'none';
                }, 300);
                return;
            }
            
            // Fall back to component loader
            const compLoader = document.getElementById('component-loading-overlay');
            if (compLoader) {
                compLoader.classList.remove('visible');
                setTimeout(() => {
                    compLoader.style.display = 'none';
                }, 300);
            }
        };
    }

    // Only attach event listeners if not already defined in master layout
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Only handle report links if they exist and aren't already handled
            const reportLinks = document.querySelectorAll('.report-link');
            if (reportLinks.length > 0) {
                reportLinks.forEach(link => {
                    if (link && !link.hasAttribute('data-loading-attached')) {
                        link.setAttribute('data-loading-attached', 'true');
                        link.addEventListener('click', function(e) {
                            if (!e.ctrlKey && !e.metaKey && !this.getAttribute('target')) {
                                e.preventDefault();
                                window.showLoader();
                                setTimeout(() => {
                                    window.location.href = this.href;
                                }, 100);
                            }
                        });
                    }
                });
            }
        } catch (e) {
            console.log('Error attaching click handlers to report links:', e);
        }
        
        // Hide component loader when fully loaded
        window.addEventListener('load', function() {
            window.hideLoader();
        });
    });

    // Section toggle function
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = section.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Favorite toggle function
    function toggleFavorite(icon) {
        if (icon) {
            icon.classList.toggle('text-muted');
            icon.classList.toggle('text-success');
        }
    }
</script>