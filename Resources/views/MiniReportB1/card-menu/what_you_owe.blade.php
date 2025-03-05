    
    {{-- What you owe --}}
    @component('components.widget', ['class' => 'box-solid'])
        <div class="section-header" onclick="toggleSection('whatYouOwnSection')">
            <i class="fas fa-chevron-down"></i> What you owe
        </div>
        <div class="row report-container" id="whatYouOwnSection">
            {{-- <div class="col-md-6 report-item" data-title="Accounts payable ageing summary">
                <div class="report-box">
                    <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Accounts payable ageing summary</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 report-item" data-title="Accounts payable ageing detail">
                <div class="report-box">
                    <a href="{{ route('account_payable_ageing_details_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Accounts payable ageing detail</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 report-item" data-title="Bill Payment List">
                <div class="report-box">
                    <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Bill Payment List</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col-md-6 report-item" data-title="Unpaid Bills">
                <div class="report-box">
                    <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Unpaid Bills</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 report-item" data-title="Supplier Balance Summary">
                <div class="report-box">
                    <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Supplier Balance Summary</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 report-item" data-title="Supplier Balance Detail">
                <div class="report-box">
                    <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Supplier Balance Detail</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- Trophy Icon -->
        </div>
        <br><br>
        {{-- <i class="fas fa-file-invoice-dollar text-success"
            style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i> --}}
            
  @endcomponent