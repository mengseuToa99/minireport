   {{-- Employees --}}
   @component('components.widget', ['class' => 'box-solid'])
    <div class="section-header" onclick="toggleSection('employeesSection')">
        <i class="fas fa-chevron-down"></i> Employees
    </div>
    {{-- <div class="row report-container" id="employeesSection">
        <div class="col-md-6 report-item" data-title="Employee Contact List">
            <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Employee Contact List</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Recent/Edited Time Activities">
            <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Recent/Edited Time Activities</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 report-item" data-title="Time Activities by Employee Detail">
            <div class="report-box">
                <a href="{{ route('account_payable_ageing_quickbooks') }}" class="report-link"
                    style="text-decoration: none;">
                    <span>Time Activities by Employee Detail</span>
                </a>
                <div class="icons">
                    <i class="fas fa-star favorite-icon text-muted" onclick="toggleFavorite(this)"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
    </div> --}}
    <br><br>
    <!-- Trophy Icon -->
    <i class="fas fa-user-tie text-success"
        style="font-size: 40px; position: absolute; bottom: 10px; right: 10px;"></i>
@endcomponent
{{-- Employees --}}