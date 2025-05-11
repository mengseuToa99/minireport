{{-- Sales and customers --}}
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('salesAndCustomersSection')">
        <i class="fas fa-chevron-down"></i> @lang('minireportb1::minireportb1.sales_and_customers')
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
                                <span>@lang('minireportb1::minireportb1.sales_representative_report')</span>
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


                    <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_customer_no_map') }}" class="report-link">
                                <span>Customer Location Report – Unmapped Address</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_account_receivable_unpaid') }}" class="report-link">
                                <span>Accounts Receivable - Unpaid</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_customer_pruchase') }}" class="report-link">
                                <span>Customer Report - Purchase</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_customer_loan') }}" class="report-link">
                                <span>Customer Report - Loan</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_vat_sale') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.vat_sales_report')</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                       <div class="col-md-6 report-item" data-title="Sell Payment Report">
                        <div class="report-box">
                            <a href="{{ route('sr_monthly_purchase_ledger') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.monthly_purchase_ledger')</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Withholding Tax Report">
                        <div class="report-box">
                            <a href="{{ route('sr_withholding_tax_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.withholding_tax_report')</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Office Rental Receipt">
                        <div class="report-box">
                            <a href="{{ route('sr_rental_invoice') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.rental_invoice')</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Withholding Tax Report">
                        <div class="report-box">
                            <a href="{{ route('sr_expense_purchase_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.expense_purchase_report')</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Withholding Tax Report">
                        <div class="report-box">
                            <a href="{{ route('sr_customer_report_via_staff') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.customer_report_via_staff')</span>
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
