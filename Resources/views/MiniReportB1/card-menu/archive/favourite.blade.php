    <!-- Favourites Section -->
    @component('components.widget', ['class' => 'box-solid'])

        <div class="section-header" onclick="toggleSection('favoritesSection')">
            <i class="fas fa-chevron-down"></i> Favourites
        </div>

        <div class="row report-container" id="favoritesSection">
            <div class="col-md-6 report-item" data-title="Accounts receivable ageing summary">
                <div class="report-box">
                    <a href="{{ route('account_receivable_ageing') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Accounts receivable ageing summary</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 report-item" data-title="Balance Sheet">
                <div class="report-box">
                    <a href="{{ route('balanceSheet_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>Balance Sheet</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 report-item" data-title="Profit and Loss">
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
            
        </div>
        <br><br>
        <!-- Trophy Icon -->
        <i class="fas fa-star  text-success"
            style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i>
@endcomponent