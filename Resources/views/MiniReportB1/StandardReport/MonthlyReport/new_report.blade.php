<!-- 
@component('components.widget', ['class' => 'box-solid'])
<div class="section-header" onclick="toggleSection('newReportSection')">
    <i class="fas fa-chevron-down"></i> new report
</div>
<div id="newReportSection">

    <table class="table">
        <tr>
            
            <td style="width: 80%;">

                  <div class="col-md-6 report-item" data-title="Salary Slip">
                        <div class="report-box">
                        
                                <span>សាច់ប្រាក់បៀវត្សប្រចាំខែ (Salary Slip) just-layout</span>
                            <                          <div class="icons">
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


             


            <td style="width: 20%;">
            </td>
        </tr>
    </table>
    <div class="sprite-image3"></div>
</div> 

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
 -->
