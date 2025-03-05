<!-- Business Overview Section -->
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('businessOverviewSection')">
        <i class="fas fa-chevron-down"></i> Montly Report
    </div>
    <div id="businessOverviewSection">

        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">


                    <div class="col-md-6 report-item" data-title="Employee Contact List">
                        <div class="report-box">
                            <a href="{{ route('sr_exspend_month') }}" class="report-link">
                                <span>Exspend For Month</span>
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
                                <span>Income For Month</span>
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
