@php
// Define route mappings for specific report numbers
$routeMapping = [
    1 => 'tax_gov_document',
    2 => 'tax_gov_document2',
    3 => 'tax_gov_document3',
    4 => 'tax_gov_document4',
    5 => 'tax_gov_document5',
    6 => 'report.ap01.a',
    7 => 'tax_gov_document7',
    8 => 'tax_gov_document8',
    9 => 'tax_gov_document9',
    10 => 'tax_gov_document10'
];

// For reports without specific routes, use a default pattern
function getReportRoute($reportNumber) {
    global $routeMapping;
    
    if (isset($routeMapping[$reportNumber])) {
        return $routeMapping[$reportNumber];
    } else {
        return 'tax_gov_document';
    }
}

// Get direct URL for each report - updated with the correct paths from web.php
function getDirectUrl($reportNumber) {
    if ($reportNumber == 1) return '/minireportb1/tax-gov-p101';
    if ($reportNumber == 2) return '/minireportb1/application-form-for-property-rental-tax';
    if ($reportNumber == 3) return '/minireportb1/tax-gov-document3';
    if ($reportNumber == 4) return '/minireportb1/tax-gov-document4';
    if ($reportNumber == 5) return '/minireportb1/tax-gov-document5';
    if ($reportNumber == 6) return '/minireportb1/AP01-A';
    return '/minireportb1/tax-gov-p101';
}
@endphp

<div class="row">
@for ($i = 1; $i <= 69; $i++)
    <div class="col-md-6 report-item" data-title="Profit / Loss Report" data-report-id="tax_report_{{ $i }}" data-index="{{ $i }}">
        <div class="report-box">
            <a href="{{ getDirectUrl($i) }}" class="report-link">
                <span>@lang('minireportb1::minireportb1.tax_report_name_' . $i)</span>
            </a>
            <div class="icons">
                <i class="fas fa-star favorite-icon text-muted" onclick="toggleCompletion(this, 'tax_report_{{ $i }}')" data-report-id="tax_report_{{ $i }}"></i>
                <i class="fas fa-ellipsis-v"></i>
            </div>
        </div>
    </div>
@endfor
</div>

<!-- JavaScript for handling completion status -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load completion status from local storage and reorder items
    loadCompletionStatus();
    reorderItems();
    
    // Add debug information
    console.log("DOM fully loaded");
    console.log("Total report items: " + document.querySelectorAll('.report-item').length);
    console.log("Starred items: " + document.querySelectorAll('.favorite-icon.text-warning').length);
});

function toggleCompletion(icon, reportId) {
    console.log("Toggling completion for: " + reportId);
    
    // Toggle the 'text-warning' class (yellow star)
    if (icon.classList.contains('text-muted')) {
        icon.classList.remove('text-muted');
        icon.classList.add('text-warning');
        // Save to local storage
        localStorage.setItem(reportId, 'completed');
        console.log("Marked as completed: " + reportId);
    } else {
        icon.classList.remove('text-warning');
        icon.classList.add('text-muted');
        // Remove from local storage
        localStorage.removeItem(reportId);
        console.log("Unmarked as completed: " + reportId);
    }
    
    // Reorder the items after toggling
    setTimeout(reorderItems, 100); // Small delay to ensure DOM is updated
}

function loadCompletionStatus() {
    // Get all favorite icons
    const favoriteIcons = document.querySelectorAll('.favorite-icon');
    
    // For each icon, check if it's marked as completed in local storage
    favoriteIcons.forEach(icon => {
        const reportId = icon.getAttribute('data-report-id');
        if (localStorage.getItem(reportId) === 'completed') {
            icon.classList.remove('text-muted');
            icon.classList.add('text-warning');
        }
    });
}
</script>

<style>
.favorite-icon.text-warning {
    color: #ffc107 !important;
}

.report-box {
    border: 1px solid #e0e0e0;
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff;
    transition: all 0.2s ease;
}

.report-box:hover {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.report-link {
    color: #333;
    text-decoration: none;
    flex-grow: 1;
}

.icons {
    display: flex;
    gap: 10px;
}

.icons i {
    cursor: pointer;
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}
</style>
