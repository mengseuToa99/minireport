<!-- Business Overview Section -->
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('businessOverviewSection')">
        <i class="fas fa-chevron-down"></i> Business Overview
    </div>
    <div class="row report-container" id="businessOverviewSection">

        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">
        <div class="col-md-6 report-item" data-title="Audit Log">
            <div class="report-box">
                <a href="{{ route('activity_log_quickbooks') }}" class="report-link" style="text-decoration: none;">
                    <span>Audit Log</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet">
            <div class="report-box">
                <a href="{{ route('minireportb1_income_statment_quickbooks') }}" class="report-link" style="text-decoration: none;">
                    <span>Income Statement</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet">
            <div class="report-box">
                <a href="{{ route('minireportb1_financial_position_quickbooks') }}" class="report-link" style="text-decoration: none;">
                    <span>Financial Position</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet">
            <div class="report-box">
                <a href="{{ route('minireportb1_balanceSheet_quickbooks') }}" class="report-link" style="text-decoration: none;">
                    <span>Balance Sheet</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet Comparison">
            <div class="report-box">
                <a href="{{ route('balanceSheet_compare_report') }}" class="report-link" style="text-decoration: none;">
                    <span>Balance Sheet Comparison</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet Detail">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_detail') }}" class="report-link" style="text-decoration: none;">
                    <span>Balance Sheet Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Balance Sheet Summary">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_summary') }}" class="report-link" style="text-decoration: none;">
                    <span>Balance Sheet Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>

        <div class="row report-container" id="payrollSection">
            <div class="col-md-6 report-item" data-title="Employee Contact List">
                <div class="report-box">
                    <a href="{{ route('mini_quarterly_report') }}" class="report-link" style="text-decoration: none;">
                        <span>របាយការណ៍លក់ប្រចាំត្រីមាស</span>
                    </a>
                    <div class="icons">
                        <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>
            </div>
        </div>
        <td style="width: 20%;">
            <!-- Parent container with relative positioning -->
            <!-- Your existing content here -->
            {{-- <i class="fas fa-money-check text-success"
style="font-size: 100px; position: absolute; bottom: 5px; right: 15px;"></i> --}}

            <div class="sprite-image3"></div>



        </td>
    </tr>
</table>
    </div> <!-- Closing div for businessOverviewSection -->

  

    <br><br>
    <!-- Trophy Icon -->
    <i class="fas fa-briefcase text-success" style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i>
@endcomponent

  {{-- <div class="col-md-6 report-item" data-title="Budget Overview">
            <div class="report-box">
                <a href="{{ route('budget_report.index') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Budget Overview</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Budget vs. Actuals">
            <div class="report-box">
                <a href="{{ route('budget-actual.index') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Budget vs. Actuals</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-error" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Statement of Cash Flows">
            <div class="report-box">
                <a href="{{ route('cash_flow') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Statement of Cash Flows</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Business Snapshot">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Business Snapshot</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Custom Summary Report">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Custom Summary Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Statement of Changes in Equity">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Statement of Changes in Equity</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Profit and Loss">
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
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Profit and Loss by Customer">
            <div class="report-box">
                <a href="{{ route('profit_loss') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss by Customer</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Profit and Loss by Class">
            <div class="report-box">
                <a href="{{ route('profit_loss') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss by Class</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Profit and Loss by Month">
            <div class="report-box">
                <a href="{{ route('profit_loss_by_month') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss by Month</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-error" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Profit and Loss Comparison">
            <div class="report-box">
                <a href="{{ route('profit_loss_comparison') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss Comparison</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Profit and Loss Detail">
            <div class="report-box">
                <a href="{{ route('report_profit_loss_detail') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
  {{-- <div class="col-md-6 report-item" data-title="Profit and Loss as % of total income">
            <div class="report-box">
                <a href="{{ route('report_profit_loss_percentage') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss as % of total income</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Profit and Loss year-to-date comparison">
            <div class="report-box">
                <a href="{{ route('profit_loss_comparison_ytd') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Profit and Loss year-to-date comparison</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Quarterly Profit and Loss Summary">
            <div class="report-box">
                <a href="{{ route('quarterly_profit_loss_summary') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Quarterly Profit and Loss Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        
    </div> --}}
    <style>
        .sprite-image3 {
            margin-top: 50px;
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