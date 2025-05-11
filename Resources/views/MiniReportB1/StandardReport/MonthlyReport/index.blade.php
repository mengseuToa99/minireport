<!-- Business Overview Section -->
@component('components.widget', ['class' => 'box-solid'])
<div class="section-header" onclick="toggleSection('businessOverviewSection')">
    <i class="fas fa-chevron-down"></i> @lang('minireportb1::minireportb1.monthly_reports')
</div>
<div id="businessOverviewSection">

    <table class="table">
        <tr>
            <!-- Column 1 (40%) -->
            <td style="width: 80%;">

                {{-- <div class="col-md-6 report-item" data-title="Print All Reports">
                    <div class="report-box">
                        <a href="javascript:void(0);" onclick="printAllReports()" class="report-link">
                            <span><i class="fas fa-print"></i> @lang('minireportb1::minireportb1.print_all_reports')</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div> --}}


                <div class="col-md-6 report-item" data-title="Employee Contact List">
                    <div class="report-box">
                        <a href="{{ route('sr_exspend_month') }}" class="report-link">
                            @lang('minireportb1::minireportb1.expense_for_month')
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 report-item" data-title="Employee Contact List">
                    <div class="report-box">
                        <a href="{{ route('sr_income_month') }}" class="report-link">
                            <span> @lang('minireportb1::minireportb1.income_for_month')</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 report-item" data-title="Employee Contact List">
                    <div class="report-box">
                        <a href="{{ route('sr_monthlystock') }}" class="report-link">
                            <span>@lang('minireportb1::minireportb1.monthly_stock_report')</span>
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
                            <span>@lang('minireportb1::minireportb1.branch_data_report')</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 report-item" data-title="Balance Sheet">
                    <div class="report-box">
                        <a href="{{ route('sr_period_income_statment') }}" class="report-link"
                            style="text-decoration: none;">
                            <span> @lang('minireportb1::minireportb1.period_income_statement') </span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-6 report-item" data-title="Franchise Monthly Report">
                    <div class="report-box">
                        <a href="{{ route('sr_franchise_monthly_report') }}" class="report-link">
                            <span>របាយការណ៍ហ្វ្រែនឆាយប្រចាំខែ</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-md-6 report-item" data-title="Share Benefit Report">
                    <div class="report-box">
                        <a href="{{ route('sr_share_benefit_report') }}" class="report-link">
                            <span>របាយការណ៍ភាគលាភ</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-md-6 report-item" data-title="Print All Reports">
                    <div class="report-box">
                        <a href="javascript:void(0);" onclick="printAllReports()" class="report-link">
                            <span><i class="fas fa-print"></i> @lang('minireportb1::minireportb1.print_all_reports')</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div> --}}


                <div class="col-md-6 report-item" data-title="Employee Contact List">
                    <div class="report-box">
                        <a href="{{ route('sr_exspend_month') }}" class="report-link">
                            @lang('minireportb1::minireportb1.expense_for_month')
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 report-item" data-title="Employee Contact List">
                    <div class="report-box">
                        <a href="{{ route('sr_income_month') }}" class="report-link">
                            <span> @lang('minireportb1::minireportb1.income_for_month')</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 report-item" data-title="Employee Contact List">
                    <div class="report-box">
                        <a href="{{ route('sr_monthlystock') }}" class="report-link">
                            <span>@lang('minireportb1::minireportb1.monthly_stock_report')</span>
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
                            <span>@lang('minireportb1::minireportb1.branch_data_report')</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 report-item" data-title="Balance Sheet">
                    <div class="report-box">
                        <a href="{{ route('sr_period_income_statment') }}" class="report-link"
                            style="text-decoration: none;">
                            <span> @lang('minireportb1::minireportb1.period_income_statement') </span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-6 report-item" data-title="Franchise Monthly Report">
                    <div class="report-box">
                        <a href="{{ route('sr_franchise_monthly_report') }}" class="report-link">
                            <span>របាយការណ៍ហ្វ្រែនឆាយប្រចាំខែ</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="col-md-6 report-item" data-title="Share Benefit Report">
                    <div class="report-box">
                        <a href="{{ route('sr_share_benefit_report') }}" class="report-link">
                            <span>របាយការណ៍ភាគលាភ</span>
                        </a>
                        <div class="icons">
                            <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                </div> --}}


                <div class="col-md-6 report-item" data-title="Salary Slip">
                        <div class="report-box">
                            <a href="{{ route('sr_payroll_slip') }}" class="report-link">
                                <span>សាច់ប្រាក់បៀវត្សប្រចាំខែ (Salary Slip) just-layout</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 report-item" data-title="Profit / Loss Report">
                        <div class="report-box">
                            <a href="{{ route('minireportb1.standardReport.humanResource.payroll_allowance_deduction_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.payroll_allowance_deduction_report')</span>
                                                               


                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                        <div class="col-md-6 report-item" data-title="Monthly Payroll Report">
                        <div class="report-box">
                            <a href="{{ route('sr_monthly_payroll_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.monthly_salary_report')</span>
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

                         <div class="col-md-6 report-item" data-title="Profit / Loss Report">
                        <div class="report-box">
                                                     <a href="{{ route('minireportb1.standardReport.humanResource.monthly_tax_report') }}" class="report-link">

                                                                <span>@lang('minireportb1::minireportb1.monthly_tax_report')</span>


                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('sr_profit_product') }}" class="report-link">
                                <span>តារាងផលិតផលចំណេញជាភាគរយ</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>



                <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('sr_batch_groupprice') }}" class="report-link">
                                <span>តារាងតម្លៃផលិតផលជាកេះតាមក្រុមថ្លៃនិងមួយឯកតា</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('sr_pricelist_all') }}" class="report-link">
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
                            <a href="{{ route('sr_monthlystock') }}" class="report-link">
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
                            <a href="{{ route('sr_promotion_product') }}" class="report-link">
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
                            <a href="{{ route('sr_promotion_product_all') }}" class="report-link">
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
                            <a href="{{ route('sr_cashbook') }}" class="report-link">
                                <span>សៀវភៅសាច់ប្រាក់</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('sr_expense_list') }}" class="report-link">
                                <span>បញ្ជីចំណាយ</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

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


<script>
    function printAllReports() {
        // Array of report routes to print
        const reportRoutes = [
            '{{ route('sr_exspend_month') }}',
            '{{ route('sr_income_month') }}',
            '{{ route('sr_monthlystock') }}',
            '{{ route('sr_branchDataReport') }}',
            '{{ route('sr_period_income_statment') }}'
        ];

        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.innerHTML =
            '<div style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);z-index:9999;display:flex;justify-content:center;align-items:center;"><div><h3>Preparing reports for printing...</h3><div style="text-align:center;font-size:20px;">0/' +
            reportRoutes.length + ' reports loaded</div></div></div>';
        document.body.appendChild(loadingIndicator);

        // Create a single iframe for printing
        const printFrame = document.createElement('iframe');
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        // Load reports one at a time
        loadReport(0);

        function loadReport(index) {
            // Check if we've processed all reports
            if (index >= reportRoutes.length) {
                loadingIndicator.remove();
                printFrame.remove();
                return;
            }

            // Update loading indicator
            const countDisplay = loadingIndicator.querySelector('div > div');
            countDisplay.innerHTML = (index + 1) + '/' + reportRoutes.length + ' reports loaded';

            // Navigate iframe to the report URL
            printFrame.src = reportRoutes[index];

            // Once the report is loaded, prepare for printing
            printFrame.onload = function () {
                try {
                    // Add print styles
                    const iframeDoc = printFrame.contentWindow.document;
                    const styleElement = iframeDoc.createElement('style');
                    styleElement.innerHTML = `
                    @media print {
                        .no-print, .dataTables_filter, .dataTables_length, .dataTables_paginate, 
                        .dataTables_info, .filters-container, .main-footer, .main-sidebar,
                        .navbar, .content-header, button:not([data-label]) {
                            display: none !important;
                        }
                        
                        .dropdown-content {
                            display: block !important;
                        }
                        
                        body {
                            page-break-after: always;
                        }
                        
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                        }
                    }
                `;
                    iframeDoc.head.appendChild(styleElement);

                    // Handle DataTables if present
                    if (iframeDoc.getElementById('supplier_report_tbl') ||
                        iframeDoc.querySelector('.dataTables_wrapper')) {

                        // Ensure DataTables are fully rendered before printing
                        setTimeout(() => {
                            printCurrentReport(index);
                        }, 2000); // Wait 2 seconds for DataTables to initialize
                    } else {
                        // Print immediately for non-DataTable reports
                        printCurrentReport(index);
                    }
                } catch (error) {
                    console.error("Error preparing report", index, error);
                    // Skip to the next report
                    setTimeout(() => {
                        loadReport(index + 1);
                    }, 1000);
                }
            };

            // Handle loading errors
            printFrame.onerror = function () {
                console.error("Failed to load report", index);
                // Skip to the next report
                setTimeout(() => {
                    loadReport(index + 1);
                }, 1000);
            };
        }

        function printCurrentReport(index) {
            try {
                // Print the current report
                printFrame.contentWindow.print();

                // Move to the next report after printing
                setTimeout(() => {
                    loadReport(index + 1);
                }, 1500);
            } catch (error) {
                console.error("Error printing report", index, error);
                // Move to the next report despite the error
                setTimeout(() => {
                    loadReport(index + 1);
                }, 1000);
            }
        }
    }
</script>
