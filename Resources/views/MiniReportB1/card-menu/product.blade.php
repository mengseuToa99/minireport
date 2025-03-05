@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('productSection')">
        <i class="fas fa-chevron-down"></i> ផលិតផល
    </div>
    <div class="row report-container" id="productSection">

        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_productgroupprice') }}" class="report-link">
                                <span>តារាងតម្លៃតាមក្រុមថ្លៃ</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_productgrouppriceall') }}" class="report-link">
                                <span>តារាងតម្លៃរួមតាមក្រុមថ្លៃ</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_monthly_stock') }}" class="report-link">
                                <span>របាយការណ៍ស្តុកប្រចាំខែ</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_promotion_product') }}" class="report-link">
                                <span>របាយការណ៍ប្រូម៉ូសិនប្រចាំខែ</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_promotion_product_all') }}" class="report-link">
                                <span>របាយការណ៍ប្រូម៉ូសិនប្រចាំខែ (ប្រៀបធៀបថ្លៃដើម)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_income_month') }}" class="report-link">
                                <span>Income For Month</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_quarterly_report') }}" class="report-link">
                                <span>របាយការណ៍លក់ប្រចាំត្រីមាស</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('mini_exspend_list') }}" class="report-link">
                                <span>Exspend List</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Report -->
                    <div class="col-md-6 report-item" data-title="Stock Report">
                        <div class="report-box">
                            <a href="{{ route('getStockReport') }}" class="report-link">
                                <span>Stock Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Expiry Report -->
                    <div class="col-md-6 report-item" data-title="Stock Expiry Report">
                        <div class="report-box">
                            <a href="{{ route('getStockExpiryReport') }}" class="report-link">
                                <span>Stock Expiry Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Adjustment Report -->
                    <div class="col-md-6 report-item" data-title="Stock Adjustment Report">
                        <div class="report-box">
                            <a href="{{ route('getStockAdjustmentReport') }}" class="report-link">
                                <span>Stock Adjustment Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Trending Products Report -->
                    <div class="col-md-6 report-item" data-title="Trending Products Report">
                        <div class="report-box">
                            <a href="{{ route('getTrendingProducts') }}" class="report-link">
                                <span>Trending Products Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Items Report -->
                    <div class="col-md-6 report-item" data-title="Items Report">
                        <div class="report-box">
                            <a href="{{ route('itemsReport') }}" class="report-link">
                                <span>Items Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Product Purchase Report -->
                    <div class="col-md-6 report-item" data-title="Product Purchase Report">
                        <div class="report-box">
                            <a href="{{ route('getproductPurchaseReport') }}" class="report-link">
                                <span>Product Purchase Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Product Sell Report -->
                    <div class="col-md-6 report-item" data-title="Product Sell Report">
                        <div class="report-box">
                            <a href="{{ route('getproductSellReport') }}" class="report-link">
                                <span>Product Sell Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier & Customer Report -->
                    <div class="col-md-6 report-item" data-title="Supplier & Customer Report">
                        <div class="report-box">
                            <a href="{{ route('getCustomerSuppliers') }}" class="report-link">
                                <span>Supplier & Customer Report</span>
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

                    <div class="sprite-image1"></div>



                </td>
            </tr>
        </table>

    </div>
    <br><br>
@endcomponent

<style>
    .sprite-image1 {
        margin-top: 330px;
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px; /* Total size of the sprite sheet (3x3 grid) */
        background-position: -200px 0; /* Position to show the second image (top-middle) */
        background-repeat: no-repeat;
    }
</style>

{{-- <div class="col-md-6 report-item" data-title="Employee Contact List">
            <div class="report-box">
                <a href="{{ route('test123', ['id' => 3157]) }}" class="report-link">
                    <span>View History Product</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
<!-- Trophy Icon -->
{{-- <i class="fas fa-money-check text-success"
        style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i> --}}
