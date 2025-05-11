 {{-- For my accountant --}}
 <div class="menu-card">
    <div class="section-header" onclick="toggleSection('forMyAccountSection')">
        <i class="fas fa-chevron-down"></i> For my accountant
    </div>
    {{-- <div class="row report-container" id="forMyAccountSection">
        <div class="col-md-6 report-item" data-title="Balance Sheet">
            <div class="report-box">

                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet Comparison">
            <div class="report-box">
                <a href="{{ route('balanceSheet_compare_report') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Balance Sheet Comparison</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Statement of Cash Flows">
            <div class="report-box">
                <a href="{{ route('balanceSheet_compare_report') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Statement of Cash Flows</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="General Ledger">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_detail') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>General Ledger</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Journal">
            <div class="report-box">
                <a href="{{ route('journal_report') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Journal</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Class List">
            <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Class List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Recurring Template List">
            <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Recurring Template List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-md-6 report-item" data-title="Profit and Loss">
            <div class="report-box">
                <a href="{{ route('profit_loss') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Profit and Loss Comparison">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss Comparison</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Recent Transactions">
            <div class="report-box">
                <a href="{{ route('profit_loss') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Recent Transactions</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Reconciliation Reports">
            <div class="report-box">
                <a href="{{ route('profit_loss') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Reconciliation Reports</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Trial Balance">
            <div class="report-box">
                <a href="{{ route('trialBalance_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Trial Balance</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Transaction Detail by Account">
            <div class="report-box">
                <a href="{{ route('trialBalance_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Transaction Detail by Account</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-md-6 report-item" data-title="Transaction List by Date">
            <div class="report-box">
                <a href="{{ route('trialBalance_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Transaction List by Date</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Recurring Template List">
            <div class="report-box">
                <a href="{{ route('trialBalance_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Recurring Template List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Transaction List with Splits">
            <div class="report-box">
                <a href="{{ route('trialBalance_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Transaction List with Splits</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
    </div> --}}
    <br><br>
    <!-- Trophy Icon -->
    <i class="fas fa-file-invoice text-success"
        style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i>
</div>