{{-- Who owes you --}}
@component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('saleAndFrancierSection')">
        <i class="fas fa-chevron-down"></i> @lang('minireportb1::minireportb1.saleAndFrancier')
    </div>
    <div class="row report-container" id="saleAndFrancierSection">


        <table class="table">
            <tr>
                <!-- Column 1 (40%) -->
                <td style="width: 80%;">

                   
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
                            <a href="{{ route('sr_product_sale_report') }}" class="report-link">
                                <span>របាយការណ៍ការលក់ផលិតផល</span>
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
                            <a href="{{ route('sr_pricelist_costgroup') }}" class="report-link">
                                <span>តារាងតម្លៃផលិតផលជាកេះតាមក្រុមថ្លៃ</span>
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
