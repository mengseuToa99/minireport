<!-- Business Overview Section -->
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('businessOverviewSection1')">
        <i class="fas fa-chevron-down"></i> ទិដ្ឋភាពអាជីវកម្ម (Business Overview)
    </div>
    <div id="businessOverviewSection1">

        <!-- Add this button to your Business Overview section -->
        {{-- <div class="col-md-6 report-item" data-title="Print All Reports">
            <div class="report-box">
                <a href="javascript:void(0);" onclick="printAllReports()" class="report-link">
                    <span><i class="fas fa-print"></i> បោះពុម្ពរបាយការណ៍ទាំងអស់ (Print All Reports)</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}

        <table class="table">
            <tr>
                <!-- Column 1 (80%) -->
                <td style="width: 80%;">

                <div class="col-md-6 report-item" data-title="Bankbook">
                        <div class="report-box">
                            <a href="{{ route('sr_bankbook') }}" class="report-link">
                                <span>សៀវភៅធនាគារ (Bankbook)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Bank Reconciliation">
                        <div class="report-box">
                            <a href="{{ route('sr_bank_reconciliation') }}" class="report-link">
                                <span>ផ្ទៀងផ្ទាត់សាច់ប្រាក់​ធនាគារ (Bank Reconciliation) just-layout</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Expense For Month">
                        <div class="report-box">
                            <a href="{{ route('sr_exspend_month') }}" class="report-link">
                                <span>ចំណាយប្រចាំខែ (Expense For Month)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Income For Month">
                        <div class="report-box">
                            <a href="{{ route('sr_income_month') }}" class="report-link">
                                <span>ចំណូលប្រចាំខែ (Income For Month)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 report-item" data-title="Period Income Statement">
                        <div class="report-box">
                            <a href="{{ route('sr_period_income_statment') }}" class="report-link"
                                style="text-decoration: none;">
                                <span>របាយការណ៍ចំណូលតាមរយៈពេល (Period Income Statement)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-item" data-title="Yearly Income Statement">
                        <div class="report-box">
                            <a href="{{ route('sr_income_statment') }}" class="report-link" style="text-decoration: none;">
                                <span>របាយការណ៍ចំណូលប្រចាំឆ្នាំ (Yearly Income Statement)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-item" data-title="Financial Position">
                        <div class="report-box">
                            <a href="{{ route('sr_financial_position_quickbooks') }}" class="report-link"
                                style="text-decoration: none;">
                                <span>ស្ថានភាពហិរញ្ញវត្ថុ (Financial Position)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-item" data-title="Balance Sheet">
                        <div class="report-box">
                            <a href="{{ route('sr_balanceSheet') }}" class="report-link" style="text-decoration: none;">
                                <span>តារាងតុល្យការ (Balance Sheet)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Quarterly Report">
                        <div class="report-box">
                            <a href="{{ route('sr_quarterly_report') }}" class="report-link" style="text-decoration: none;">
                                <span>របាយការណ៍លក់ប្រចាំត្រីមាស (Quarterly Sales Report)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                <td style="width: 20%;">
                </td>
            </tr>
        </table>
        <div class="sprite-image3"></div>
    </div> <!-- Closing div for businessOverviewSection -->




    <!-- Trophy Icon -->
@endcomponent

<style>
    .sprite-image3 {
        margin-left: auto;
        margin-top: -200px;
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px;
        /* Total size of the sprite sheet (3x3 grid) */
        background-position: -300px -400px;
        /* Position to show the second image (top-middle) */
        background-repeat: no-repeat;
    }
</style>


