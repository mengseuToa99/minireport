@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('payrollSection1')">
        <i class="fas fa-chevron-down"></i> Report
    </div>
    <div id="payrollSection1">
        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">
                    <!-- Profit / Loss Report -->
                    <div class="col-md-6 report-item" data-title="Profit / Loss Report">
                        <div class="report-box">
                            <a href="{{ route('profitLoss') }}" class="report-link">
                                <span>Profit / Loss Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Report -->
                    <div class="col-md-6 report-item" data-title="Tax Report">
                        <div class="report-box">
                            <a href="{{ route('getTaxReport') }}" class="report-link">
                                <span>Tax Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>


                    <!-- Expense Report -->
                    <div class="col-md-6 report-item" data-title="Expense Report">
                        <div class="report-box">
                            <a href="{{ route('getExpenseReport') }}" class="report-link">
                                <span>Expense Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Register Report -->
                    <div class="col-md-6 report-item" data-title="Register Report">
                        <div class="report-box">
                            <a href="{{ route('getRegisterReport') }}" class="report-link">
                                <span>Register Report</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>



                    <!-- Activity Log -->
                    <div class="col-md-6 report-item" data-title="Activity Log">
                        <div class="report-box">
                            <a href="{{ route('activityLog') }}" class="report-link">
                                <span>Activity Log</span>
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

                    <div class="sprite-image"></div>



                </td>
            </tr>
        </table>
    </div>
    <!-- Trophy Icon -->
@endcomponent

<style>
    .sprite-image {
        width: 200px;
        height: 180px;
        background-image: url("{{ asset('modules/minireportb1/image/icon.png') }}");
        background-size: 600px 600px;
        background-position: 0 0;
        background-repeat: no-repeat;
    }
</style>
