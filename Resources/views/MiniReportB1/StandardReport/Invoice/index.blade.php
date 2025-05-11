<div class="card mb-3 section-card">
    <div class="card-header collapsed" id="headingInvoice" data-toggle="collapse" data-target="#collapseInvoice" aria-expanded="false" aria-controls="collapseInvoice">
        <div class="card-title mb-0">
            <span>វិក្កយបត្រ (Invoice & Receipts)</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
    <div id="collapseInvoice" class="collapse" aria-labelledby="headingInvoice" data-parent="#accordion">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 report-item" data-title="Office Receipt">
                    <div class="report-box">
                        <a href="{{ route('minireportb1.office_receipt') }}" class="report-link">
                            <span>វិក្កយបត្រការិយាល័យ (Office Receipt)</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>
                <!-- More report items will be added here in the future -->
            </div>
        </div>
    </div>
</div> 