{{-- Sales and customers --}}
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('salesAndCustomersSection')">
        <i class="fas fa-chevron-down"></i> Sales and customers
    </div>
    <div class="row report-container" id="salesAndCustomersSection">
        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">

                    <!-- Sales Representative Report -->
                    <div class="col-md-6 report-item" data-title="Sales Representative Report">
                        <div class="report-box">
                            <a href="{{ route('sr_getSalesRepresentativeReport') }}" class="report-link">
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
                            <a href="{{ route('sr_getCustomerGroup') }}" class="report-link">
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
                            <a href="{{ route('sr_purchasePaymentReport') }}" class="report-link">
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
                            <a href="{{ route('sr_getPurchaseSell') }}" class="report-link">
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
                            <a href="{{ route('sr_sellPaymentReport') }}" class="report-link">
                                <span>Sell Payment Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_branchDataReport') }}" class="report-link">
                                <span>តារាងព័ត៌មានសាខា</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                </td>
                <td style="width: 20%;">
                </td>
            </tr>
        </table>
        <div class="sprite-image5"></div>

    </div>
@endcomponent

<style>
    .sprite-image5 {
        margin-left: auto;
        margin-top: -200px;
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px;
        background-position: 0 0;
        background-repeat: no-repeat;
    }
</style>
