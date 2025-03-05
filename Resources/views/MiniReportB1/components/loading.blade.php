
<style>
    /* Loading Overlay */
    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    /* Loader Animation */
    .loader {
        width: 45px;
        aspect-ratio: .75;
        --c: no-repeat linear-gradient(#0f8800 0 0);
        background:
            var(--c) 0% 100%,
            var(--c) 50% 100%,
            var(--c) 100% 100%;
        background-size: 20% 65%;
        animation: l8 1s infinite linear;
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
<div id="loading-overlay">
    <div class="loader"></div>
</div>


<!-- JavaScript -->
<script>
    // Show loader when clicking any link
    document.addEventListener('click', function (event) {
        const target = event.target.closest('a');
        if (target && target.href) {
            event.preventDefault();
            showLoader();
            setTimeout(() => {
                window.location.href = target.href;
            }, 100);
        }
    });

    // Loader control functions
    function showLoader() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }

    function hideLoader() {
        document.getElementById('loading-overlay').style.display = 'none';
    }

    // Section toggle function
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }

    // Favorite toggle function
    function toggleFavorite(icon) {
        icon.classList.toggle('text-muted');
        icon.classList.toggle('text-success');
    }
</script>