{{-- Who owes you --}}
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('whoOwesyouSection')">
        <i class="fas fa-chevron-down"></i> AccountsReceivable
    </div>
    <div class="row report-container" id="whoOwesyouSection">


        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">

                    <div class="col-md-6 report-item" data-title="Terms List">
                        <div class="report-box">
                            <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                style="text-decoration: none;">
                                <span>Terms List</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-item" data-title="Unbilled charges">
                        <div class="report-box">
                            <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                style="text-decoration: none;">
                                <span>Unbilled charges</span>
                            </a>
                            <div class="icons">
                                <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-item" data-title="Unbilled time">
                        <div class="report-box">
                            <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                style="text-decoration: none;">
                                <span>Unbilled time</span>
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
        <div class="sprite-image2"></div>
    </div>

@endcomponent


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
</style>
