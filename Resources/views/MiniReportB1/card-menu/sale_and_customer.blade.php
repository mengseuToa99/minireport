{{-- Sales and customers --}}
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('salesAndCustomersSection')">
        <i class="fas fa-chevron-down"></i> Sales and customer:s
    </div>
    <div class="row report-container" id="salesAndCustomersSection">
        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">
        {{-- <div class="col-md-6 report-item" data-title="Customer Contact List">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Customer Contact List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Income by Customer Summary">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Income by Customer Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Sales by Customer Summary">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Sales by Customer Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Sales by Customer Detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Sales by Customer Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Deposit Detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Deposit Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Estimates by Customer">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Estimates by Customer</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Inventory Valuation Detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Inventory Valuation Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Inventory Valuation Summary">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Inventory Valuation Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Product/Service List">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Product/Service List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Sales by Product/Service Summary">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Sales by Product/Service Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Sales by Product/Service Detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Sales by Product/Service Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Sales by Class Summary">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Sales by Class Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Sales by Class Detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Sales by Class Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Payment Method List">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Payment Method List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-md-6 report-item" data-title="Stock Take Worksheet">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Stock Take Worksheet</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Time Activities by Customer Detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Time Activities by Customer Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Transaction List by Customer">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Transaction List by Customer</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}

        <!-- Sales Representative Report -->
        <div class="col-md-6 report-item" data-title="Sales Representative Report">
            <div class="report-box">
                <a href="{{ route('getSalesRepresentativeReport') }}" class="report-link">
                    <span>Sales Representative Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>

            <!-- Customer Groups Report -->
        <div class="col-md-6 report-item" data-title="Customer Groups Report">
            <div class="report-box">
                <a href="{{ route('getCustomerGroup') }}" class="report-link">
                    <span>Customer Groups Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>

          <!-- Purchase Payment Report -->
          <div class="col-md-6 report-item" data-title="Purchase Payment Report">
            <div class="report-box">
                <a href="{{ route('purchasePaymentReport') }}" class="report-link">
                    <span>Purchase Payment Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>

           <!-- Purchase & Sale Report -->
           <div class="col-md-6 report-item" data-title="Purchase & Sale Report">
            <div class="report-box">
                <a href="{{ route('getPurchaseSell') }}" class="report-link">
                    <span>Purchase & Sale Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>


        <!-- Sell Payment Report -->
        <div class="col-md-6 report-item" data-title="Sell Payment Report">
            <div class="report-box">
                <a href="{{ route('sellPaymentReport') }}" class="report-link">
                    <span>Sell Payment Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>

         </td>
                <td style="width: 20%;">
                    <!-- Parent container with relative positioning -->
                    <!-- Your existing content here -->
                    {{-- <i class="fas fa-money-check text-success"
                        style="font-size: 100px; position: absolute; bottom: 5px; right: 15px;"></i> --}}

                    <div class="sprite-image5"></div>



                </td>
            </tr>
        </table>



    </div>
@endcomponent

<style>
    .sprite-image5 {
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px;
        background-position: 0 0;
        background-repeat: no-repeat;
    }
</style>
