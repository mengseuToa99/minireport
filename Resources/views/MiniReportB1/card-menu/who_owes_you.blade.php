 {{-- Who owes you --}}
 @component('components.widget', ['class' => 'box-solid'])
     <div class="section-header" onclick="toggleSection('whoOwesyouSection')">
         <i class="fas fa-chevron-down"></i> Who owes you
     </div>
     <div class="row report-container" id="whoOwesyouSection">


         <table class="table">
             <tr>
                 <!-- Column 1 (40%) -->
                 <td style="width: 80%;">

                     {{-- <div class="col-md-6 report-item" data-title="Accounts receivable ageing summary">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Accounts receivable ageing summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}

                     {{-- <div class="col-md-6 report-item" data-title="Accounts receivable ageing detail">
            <div class="report-box">
                <a href="{{ route('account_receivable_ageing_details_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Accounts receivable ageing detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-success" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}

                     {{-- <div class="col-md-6 report-item" data-title="Collections Report">
            <div class="report-box">
                <a href="{{ route('balanceSheet_compare_report') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Collections Report</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Customer Balance Summary">
            <div class="report-box">
                <a href="{{ route('report_balance_sheet_detail') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Customer Balance Summary</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div> --}}
                     <div class="col-md-6 report-item" data-title="Customer Balance Detail">
                         <div class="report-box">
                             <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                 style="text-decoration: none;">
                                 <span>Customer Balance Detail</span>
                             </a>
                             <div class="icons">
                                 <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                 <i class="fas fa-ellipsis-v"></i>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-6 report-item" data-title="Invoice List">
                         <div class="report-box">
                             <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                 style="text-decoration: none;">
                                 <span>Invoice List</span>
                             </a>
                             <div class="icons">
                                 <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                 <i class="fas fa-ellipsis-v"></i>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-6 report-item" data-title="Open Invoices">
                         <div class="report-box">
                             <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                 style="text-decoration: none;">
                                 <span>Open Invoices</span>
                             </a>
                             <div class="icons">
                                 <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                 <i class="fas fa-ellipsis-v"></i>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-6 report-item" data-title="Statement List">
                         <div class="report-box">
                             <a href="{{ route('report_balance_sheet_summary') }}" class="report-link"
                                 style="text-decoration: none;">
                                 <span>Statement List</span>
                             </a>
                             <div class="icons">
                                 <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                                 <i class="fas fa-ellipsis-v"></i>
                             </div>
                         </div>
                     </div>
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
                     <!-- Parent container with relative positioning -->
                     <!-- Your existing content here -->
                     {{-- <i class="fas fa-money-check text-success"
style="font-size: 100px; position: absolute; bottom: 5px; right: 15px;"></i> --}}

                     <div class="sprite-image2"></div>



                 </td>
             </tr>
         </table>
     </div>
     <br><br>
     <!-- Trophy Icon -->
     {{-- <i class="fas fa-hand-holding-usd text-success"
        style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i> --}}
 @endcomponent


 <style>
     .sprite-image2 {
         margin-top: 100px;
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
