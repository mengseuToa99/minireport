 {{-- Expenses and suppliers --}}
 @component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('expensesAndSuppliersSection')">
        <i class="fas fa-chevron-down"></i> Expenses and suppliers
    </div>
    <div class="row report-container" id="expensesAndSuppliersSection">
        <div class="col-md-6 report-item" data-title="Cheque Detail">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Cheque Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Purchases by Product/Service Detail">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Purchases by Product/Service Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Purchases by Class Detail">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Purchases by Class Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Open Purchase Order Detail">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Open Purchase Order Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Open Purchase Order List">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Open Purchase Order List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Transaction List by Supplier">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Transaction List by Supplier</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Purchases by Supplier Detail">
            <div class="report-box">
                {{-- <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Purchases by Supplier Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div> --}}
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Supplier Contact List">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Supplier Contact List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
        <div class="col-md-6 report-item" data-title="Expenses by Supplier Summary">
            {{-- <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Expenses by Supplier Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div> --}}
        </div>
    </div>
    <br><br>
    <!-- Trophy Icon -->
    <i class="fas fa-money-bill-wave text-success"
        style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i>
@endcomponent