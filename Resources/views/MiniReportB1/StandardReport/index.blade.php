

<div class="tab-pane " id="standard" role="tabpanel" aria-labelledby="standard-tab"  style="margin-top:16px; max-height: 80vh; overflow-y: auto;">

    @include('minireportb1::MiniReportB1.StandardReport.MonthlyReport.new_report')

    @include('minireportb1::MiniReportB1.StandardReport.MonthlyReport.index')
    @include('minireportb1::MiniReportB1.StandardReport.YearlyReport.index')
    @include('minireportb1::MiniReportB1.StandardReport.HumanResource.index')
    @include('minireportb1::MiniReportB1.StandardReport.SaleAndFrancier.index')
    @include('minireportb1::MiniReportB1.StandardReport.Salesandcustomers.index')
    @include('minireportb1::MiniReportB1.StandardReport.BusinessOverview.index')
    @include('minireportb1::MiniReportB1.StandardReport.OperationalReports.index')
    @include('minireportb1::MiniReportB1.StandardReport.ProductReports.index')



</div>

    {{-- @include('minireportb1::MiniReportB1.card-menu.favourite'); --}}

    {{-- no working report --}}

    {{-- @include('minireportb1::MiniReportB1.StandardReport.WhoOwesYou.index') --}}

    {{-- @include('minireportb1::MiniReportB1.card-menu.what_you_owe') --}}

    {{-- @include('minireportb1::MiniReportB1.card-menu.expense_and_supplier') --}}

    {{-- @include('minireportb1::MiniReportB1.card-menu.employee'); --}}

    {{-- @include('minireportb1::MiniReportB1.card-menu.accountant'); --}}

    {{-- @include('minireportb1::MiniReportB1.card-menu.payroll'); --}}

    {{-- @include('minireportb1::MiniReportB1.components.loading') --}}