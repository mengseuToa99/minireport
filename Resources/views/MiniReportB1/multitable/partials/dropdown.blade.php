@component('components.widget', ['class' => 'box-solid'])
    <!-- Use a flex container with nowrap and horizontal scrolling -->
    <div class="d-flex flex-nowrap overflow-auto" style="gap: 10px;">
        <!-- Add more 8 -->
        <a href="{{ route('minireportb1.payroll') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-primary tw-m-0.5">
            <i class="fas fa-plus"></i> @lang('Add more 8')
        </a>

        <!-- Sale -->
        <a href="{{ route('minireportb1.saleReport') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-success tw-m-0.5">
            <i class="fas fa-shopping-cart"></i> @lang('Sale')
        </a>

        <!-- Purchase -->
        <a href="{{ route('minireportb1.purchaseReport') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-warning tw-m-0.5">
            <i class="fas fa-shopping-bag"></i> @lang('Purchase')
        </a>

        <!-- Payroll -->
        <a href="{{ route('minireportb1.payroll') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-danger tw-m-0.5">
            <i class="fas fa-money-bill"></i> @lang('Payroll')
        </a>

        <!-- Product -->
        <a href="{{ route('minireportb1.productReport') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-info tw-m-0.5">
            <i class="fas fa-box"></i> @lang('Product')
        </a>

        <!-- Stock -->
        {{-- <a href="{{ route('minireportb1.stockReport') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-secondary tw-m-0.5">
            <i class="fas fa-warehouse"></i> @lang('Stock')
        </a> --}}

        <!-- Pay Components -->
        <a href="{{ route('minireportb1.payroll1') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-purple tw-m-0.5">
            <i class="fas fa-money-check-alt"></i> @lang('Pay Components')
        </a>

        <!-- Payroll Groups -->
        <a href="{{ route('minireportb1.payroll2') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-teal tw-m-0.5">
            <i class="fas fa-users"></i> @lang('payroll_groups')
        </a>

        <a href="{{ route('minireportb1.expenseReport') }}"
            class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-teal tw-m-0.5">
            <i class="fas fa-users"></i> expense
        </a>
    </div>
@endcomponent