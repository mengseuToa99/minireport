@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('payrollSection1')">
        <i class="fas fa-chevron-down"></i> @lang('report.human_resources')
    </div>
    <div id="payrollSection1">
        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">
                    <!-- Profit / Loss Report -->

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

                    <div class="col-md-6 report-item" data-title="Salary Slip">
                        <div class="report-box">
                            <a href="{{ route('sr_payroll_slip') }}" class="report-link">
                                <span>សាច់ប្រាក់បៀវត្សប្រចាំខែ (Salary Slip)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Late Check In Report">
                        <div class="report-box">
                            <a href="{{ route('sr_late_check_in_report') }}" class="report-link">
                                <span>ការពិន័យ​ចូលការងារយឺត (Late Check In Report)</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Profit / Loss Report">
                        <div class="report-box">
                            <a href="{{ route('sr_early_check_out_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.fast_checkout_report')</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Profit / Loss Report">
                        <div class="report-box">
                            <a href="{{ route('sr_list_attendance_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.Attendance_Report')</span>

                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 report-item" data-title="Profit / Loss Report">
                        <div class="report-box">
                            <a href="{{ route('sr_shift_schedule_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.Time_Shift_Report')</span>

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

                    <div class="col-md-6 report-item" data-title="Bank Reconciliation Report">
                        <div class="report-box">
                            <a href="{{ route('minireportb1.standardReport.humanResource.bank_reconciliation_report') }}" class="report-link">
                                <span>@lang('minireportb1::minireportb1.bank_reconciliation_report')</span>
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
            {{-- <div class="sprite-image"></div> --}}
        </div>
    <!-- Trophy Icon -->
@endcomponent

<style>
    .sprite-image {
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
