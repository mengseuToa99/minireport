{{-- Who owes you --}}
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('whoOwesyouSection')">
        <i class="fas fa-chevron-down"></i> Yearly Report
    </div>
    <div class="row report-container" id="whoOwesyouSection">


        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">

                    <div class="col-md-6 report-item" data-title="Balance Sheet">
                        <div class="report-box">
                            <a href="{{ route('sr_income_statment') }}" class="report-link"
                                style="text-decoration: none;">
                                <span> Yearly Income Statement </span>
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
