

<!-- monthly -->
@component('components.widget', ['class' => 'box-solid'])
<div class="section-header" onclick="toggleSection('businessOverviewSection')">
    <i class="fas fa-chevron-down"></i> @lang('minireportb1::minireportb1.monthly_reports')
</div>
<div id="businessOverviewSection">

    <table class="table">
        <tr>
            <!-- Column 1 (40%) -->
            <td style="width: 80%;">

            <div class="col-md-6 report-item" data-title="Financial Position">
                <div class="report-box">
                    <a href="{{ route('sr_financial_position_quickbooks') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>1.ស្ថានភាពហិរញ្ញវត្ថុ (BS Tax)</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>


            <div class="col-md-6 report-item" data-title="Period Income Statement">
                <div class="report-box">
                    <a href="{{ route('sr_period_income_statment') }}" class="report-link"
                        style="text-decoration: none;">
                        <span>2.របាយការណ៍ចំណូលតាមរយៈពេល (P&L)</span>
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
                        <span>3.របាយការណ៍ចំណូលប្រចាំឆ្នាំ (P&L by month)</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>

            

        

            <div class="col-md-6 report-item" data-title="Income For Month">
                <div class="report-box">
                    <a href="{{ route('sr_income_month') }}" class="report-link">
                        <span>4.ចំណូលប្រចាំខែ (Revenue)</span>
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
                        <span>5.ចំណាយប្រចាំខែ (Expense)</span>
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
                                <span>6.សៀវភៅសាច់ប្រាក់ (Cash Book)</span>
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
                                <span>7.តារាងតម្លៃផលិតផលជាកេះតាមក្រុមថ្លៃនិងមួយឯកតា (Cost Of Product)</span>
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
                                <span>8. Cash Recon</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <!-- <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('sr_batch_groupprice') }}" class="report-link">
                                <span>8.Cash Reconcilation</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-md-6 report-item" data-title="Bankbook">
                        <div class="report-box">
                            <a href="{{ route('sr_bankbook') }}" class="report-link">
                                <span>9.សៀវភៅធនាគារ (Bankbook)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Bank Reconciliation Report">
                        <div class="report-box">
                            <a href="{{ route('minireportb1.standardReport.humanResource.bank_reconciliation_report') }}" class="report-link">
                                <span>10.@lang('minireportb1::minireportb1.bank_reconciliation_report') (Bank Recon)</span>
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
                                <span>11.@lang('minireportb1::minireportb1.withholding_tax_report') (Withholding)</span>
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
                                <span>12.@lang('minireportb1::minireportb1.monthly_salary_report') (Salary Tax)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Monthly Payroll Report">
                        <div class="report-box">
                            <a href="{{ route('sr_payroll_slip') }}" class="report-link">
                                <span>13.Payroll Slip</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 report-item" data-title="Monthly Payroll Report">
                        <div class="report-box">
                            <a href="{{ route('minireportb1.office_receipt') }}" class="report-link">
                                <span>14.invoice rental</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <!-- <div class="col-md-6 report-item" data-title="Monthly Payroll Report">
                        <div class="report-box">
                            <a href="{{ route('sr_monthly_payroll_report') }}" class="report-link">
                                <span>14.invoice rental product</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-md-6 report-item" data-title="Office Rental Receipt">
                        <div class="report-box">
                            <!-- <a href="{{ route('sr_rental_invoice') }}" class="report-link"> -->
                            <a href="{{ route('minireportb1.office_receipt') }}" class="report-link">

                                <span>15.@lang('minireportb1::minireportb1.rental_invoice')</span>
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
                                <span>16.E-sale</span>
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
                                <span>17.E-purchase</span>
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

                                                                <span>18.E-salary</span>


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

                                                                <span>19.E-WHT</span>


                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
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




<!-- yearly -->
<!-- @component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('whoOwesyouSection')">
        <i class="fas fa-chevron-down"></i> @lang('minireportb1::minireportb1.yearly_report')
    </div>
    <div class="row report-container" id="whoOwesyouSection">


        <table class="table">
            <tr>
             
                <td style="width: 80%;">

                    <div class="col-md-6 report-item" data-title="Balance Sheet">
                        <div class="report-box">
                            <a href="{{ route('sr_income_statment') }}" class="report-link"
                                style="text-decoration: none;">
                                <span> @lang('minireportb1::minireportb1.yearly_income_statement') </span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                
                </td>
                <td style="width: 20%;">
                </td>
            </tr>
        </table>
        <div class="sprite-image2"></div>
    </div>

@endcomponent -->

<!-- 
<style>
    .sprite-image2 {
        margin-left: auto;
        margin-top: -200px;
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px;
        /* Total size of the sprite sheet (3x3 grid) */
        background-position: -300px 0;
        /* Position to show the second image (top-middle) */
        background-repeat: no-repeat;
    }
</style> -->
