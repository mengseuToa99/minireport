<?php

use Illuminate\Support\Facades\Route;
use Modules\MiniReportB1\Entities\MiniReportB1;

use Modules\MiniReportB1\Http\Controllers\AdvanceTableController;
use Modules\MiniReportB1\Http\Controllers\MiniReportB1Controller;
use Modules\MiniReportB1\Http\Controllers\SettingController;
use Modules\MiniReportB1\Http\Controllers\FolderController;
use Modules\MiniReportB1\Http\Controllers\IncomeForMonthController;
use Modules\MiniReportB1\Http\Controllers\LayoutController;
use Modules\MiniReportB1\Http\Controllers\MultiTableController;
use Modules\MiniReportB1\Http\Controllers\ProductHController;
use Modules\MiniReportB1\Http\Controllers\ReportController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\BusinessOverviewController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\OperationalReportsController;
// use Modules\MiniReportB1\Http\Controllers\StandardReport\ProductReportController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\ProductReportController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\CustomerReportsController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\HumanResourceController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\SalarySlipController;
use Modules\MiniReportB1\Http\Controllers\GovTaxPdfController;

Route::middleware('web', 'SetSessionData', 'auth', 'MiniReportB1Language', 'timezone', 'AdminSidebarMenu')->prefix('minireportb1')->group(function () {

    Route::get('/', [MiniReportB1Controller::class, 'dashboard'])->name('MiniReportB1.dashboard');
    Route::get('/MiniReportB1', [MiniReportB1Controller::class, 'index'])->name('MiniReportB1.index');
    Route::get('/MiniReportB1/{id}', [MiniReportB1Controller::class, 'show'])->name('MiniReportB1.show');
    Route::get('/create', [MiniReportB1Controller::class, 'create'])->name('MiniReportB1.create');

    // Language route - add this to force language refresh
    Route::get('/language/{locale}', function($locale) {
        // Update user's language preference in session
        if (in_array($locale, ['en', 'kh', 'km'])) {
            session()->put('user.language', $locale);
            if (auth()->user()) {
                auth()->user()->update(['your_language' => $locale]);
            }
            // Add a flash message to confirm language change
            session()->flash('status', ['success' => true, 'msg' => 'Language changed to ' . strtoupper($locale)]);
        }
        return redirect()->back()->withInput(['lang' => $locale]);
    })->name('MiniReportB1.language');

    Route::get('/install', [Modules\MiniReportB1\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\MiniReportB1\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\MiniReportB1\Http\Controllers\InstallController::class, 'uninstall']);

    Route::get('/supplier-report', [MiniReportB1Controller::class, 'supplier'])->name('minireportb1.supplier');
    Route::get('/employee-report', [MiniReportB1Controller::class, 'employee'])->name('minireportb1.employee');
    Route::get('/account-report', [MiniReportB1Controller::class, 'account'])->name('minireportb1.account');
    Route::get('/customer-report', [MiniReportB1Controller::class, 'customer'])->name('minireportb1.customer');
    Route::get('/follow-up-report', [MiniReportB1Controller::class, 'followup'])->name('minireportb1.followupReport');
    Route::get('/expense-report', [MiniReportB1Controller::class, 'expense'])->name('minireportb1.expenseReport');
    // Route::get('/stock-report', [MiniReportB1Controller::class, 'stock'])->name('minireportb1.stockReport');
    Route::get('/sale-report', [MiniReportB1Controller::class, 'saleReport'])->name('minireportb1.saleReport');
    Route::get('/product-report', [MiniReportB1Controller::class, 'productReport'])->name('minireportb1.productReport');
    Route::get('/purchase-report', [MiniReportB1Controller::class, 'purchaseReport'])->name('minireportb1.purchaseReport');
    Route::get('/testAll', [MiniReportB1Controller::class, 'testAll'])->name('minireportb1.testAll');
    Route::get('/payroll-report', [MiniReportB1Controller::class, 'payroll'])->name('minireportb1.payroll');
    Route::get('/payroll-report1', [MiniReportB1Controller::class, 'payroll1'])->name('minireportb1.payroll1');
    Route::get('/payroll-report2', [MiniReportB1Controller::class, 'payroll2'])->name('minireportb1.payroll2');
    Route::get('/product', [MiniReportB1Controller::class, 'product'])->name('minireportb1.product');
    Route::get('/viewFile/{id}', [MiniReportB1Controller::class, 'multiViewManager'])
        ->name('MiniReportB1.viewFile')
        ->middleware(['web', 'auth']);


    Route::get('/print', [LayoutController::class, 'getComponent'])->name('minireport_print');

    // ========> advance report
    // routes/web.php
    Route::match(['get', 'post'], '/advance', [AdvanceTableController::class, 'index'])->name('reports.index');
    Route::post('/get-join-query', [AdvanceTableController::class, 'getJoinQuery'])->name('get-join-query');
    Route::get('/reports/columns', [AdvanceTableController::class, 'getColumns'])->name('reports.columns');
    Route::post('/reports/generate', [AdvanceTableController::class, 'generateReport'])->name('reports.generate');


    // ========> create layout
    Route::delete('/layouts/{layout_name}', [LayoutController::class, 'deleteLayout']);
    Route::get('/layouts/{layout_name}/edit', [LayoutController::class, 'editLayout']);
    Route::get('/get-element', [LayoutController::class, 'getLayoutElement'])->name('minireportb1.getlayout');
    Route::get('/create-layout', [LayoutController::class, 'createlayout'])->name('minireportb1.createlayout');
    Route::post('/components', [LayoutController::class, 'store'])->name('minireport.components.store');
    Route::get('/layouts', [LayoutController::class, 'getAllLayouts'])->name('minireport.layout.show');
    Route::get('/component', [LayoutController::class, 'getComponent'])->name('minireport.components.show');
    Route::get('/get-layout-components/{type}', [LayoutController::class, 'getLayoutComponents'])
        ->name('minireport.components.get');

    Route::post('/create', [MiniReportB1Controller::class, 'store'])->name('MiniReportB1.store');
    Route::get('/edit/{id}', [MiniReportB1Controller::class, 'edit'])->name('MiniReportB1.edit');
    Route::put('/edit/{id}', [MiniReportB1Controller::class, 'update'])->name('MiniReportB1.update');
    Route::delete('/delete/{id}', [MiniReportB1Controller::class, 'destroy'])->name('MiniReportB1.destroy');



    Route::get('/get-folders', [MiniReportB1Controller::class, 'getFolders'])->name('minireport_getfolder');
    Route::post('/create-folder', 'FolderController@createFolder')->name('minireportb1.create.folder');
    Route::post('/create-file', [MiniReportB1Controller::class, 'store'])->name('MiniReportB1.createFile');
    Route::delete('/delete-folder', 'FolderController@deleteFolder')->name('minireportb1.delete.folder');
    Route::delete('/delete-file', 'FolderController@deleteFile')->name('minireportb1.delete.file');

    // Route::get('minireportb1/file/{id}', [MiniReportB1Controller::class, 'viewFile'])
    // ->name('MiniReportB1.viewFile');
    Route::view('/create-file', 'minireportb1::MiniReportB1.multitable.create_file')->name('minireport_createfile');
    Route::get('/get-print-layout', [FolderController::class, 'getPrintLayout'])->name('minireportb1.get.print.layout');
    Route::post('/rename-file', 'FolderController@renameFile')->name('minireportb1.rename.file');
    Route::post('/move-file', 'FolderController@moveFile')->name('minireportb1.move.file');
    Route::post('createFile', [MiniReportB1Controller::class, 'createFile'])->name('MiniReportB1.createFile');
    Route::post('/store', [MiniReportB1Controller::class, 'store'])->name('MiniReportB1.store');
    Route::get('/folders/list', 'FolderController@getFoldersList')->name('minireportb1.folders.list');
    Route::post('/rename-folder', 'FolderController@renameFolder')->name('minireportb1.rename.folder');
    Route::post('/update-folder-order', 'FolderController@updateFolderOrder')->name('minireportb1.update.folder.order');

    // Dynamic Table Routes
    




    // ***************************************************************************************************************************************
    //                                         standard report
    // ***************************************************************************************************************************************


    // operational report
    Route::get('standardreport/profit-loss', [OperationalReportsController::class, 'getProfitLoss'])->name('standardreport.profit_loss');
    Route::get('standardreport/tax-report', [OperationalReportsController::class, 'getTaxReport'])->name('standardreport.tax_report');
    Route::get('standardreport/expense-report', [OperationalReportsController::class, 'getExpenseReport'])->name('standardreport.get_expense');
    Route::get('standardreport/register-report', [OperationalReportsController::class, 'getRegisterReport'])->name('standardreport.get_register');
    Route::get('standardreport/activity-log', [OperationalReportsController::class, 'getTaxReport'])->name('standardreport.get_activitylog');

    // product report getProfitProduce
    Route::get('standardreport/profit-product-report', [ProductReportController::class, 'getProfitProduce'])->name('sr_profit_product');
    Route::get('standardreport/products-sale-report', [ProductReportController::class, 'getProductSalesReport'])->name('sr_product_sale_report');
    Route::get('standardreport/product-price-list-by-costgroup', [ProductReportController::class, 'productPriceListbyCostGroup'])->name('sr_pricelist_costgroup');
    Route::get('standardreport/product-pricelist-by-batch-costgroup', [ProductReportController::class, 'productPriceListByBatchCostGroup'])->name('sr_batch_groupprice');
    Route::get('standardreport/price-list-by-group-price', [ProductReportController::class, 'priceListByGroupPrice'])->name('sr_pricelist');
    Route::get('standardreport/price-list-by-group-price-all', [ProductReportController::class, 'priceListByGroupPriceAll'])->name('sr_pricelist_all');
    Route::get('standardreport/monthly-stock', [ProductReportController::class, 'getMonthlyStock'])->name('sr_monthlystock');
    Route::get('standardreport/promotion-product', [ProductReportController::class, 'getPromotionProduct'])->name('sr_promotion_product');
    Route::get('standardreport/group-specific-promotions', [ProductReportController::class, 'getGroupSpecificPromotions'])->name('sr_group_specific_promotions');
    Route::get('standardreport/promotion-product-all', [ProductReportController::class, 'getPromotionProductAll'])->name('sr_promotion_product_all');
    Route::get('standardreport/cashbook', [ProductReportController::class, 'getCashbook'])->name('sr_cashbook');
    Route::get('standardreport/quarterly-report', [ProductReportController::class, 'QuarterlyReport'])->name('sr_quarterly_report');
    Route::get('standardreport/exspend-list', [ProductReportController::class, 'getExpenseList'])->name('sr_expense_list');
    Route::get('standardreport/stock-report', [ProductReportController::class, 'getStockReport'])->name('sr_stock_report');
    Route::get('standardreport/stock-expiry', [ProductReportController::class, 'getStockExpiryReport'])->name('sr_stock_expiry');
    Route::get('standardreport/stock-adjustment-report', [ProductReportController::class, 'getStockAdjustmentReport'])->name('sr_stock_adjustment');
    Route::get('standardreport/trending-products', [ProductReportController::class, 'getTrendingProducts'])->name('sr_trending_products');
    Route::get('standardreport/items-report', [ProductReportController::class, 'itemsReport'])->name('sr_itemsReport');
    Route::get('standardreport/product-purchase-report', [ProductReportController::class, 'getproductPurchaseReport'])->name('sr_getproductPurchaseReport');
    Route::get('standardreport/product-sell-report', [ProductReportController::class, 'getproductSellReport'])->name('sr_getproductSellReport');
    Route::get('standardreport/customer-supplier', [ProductReportController::class, 'getCustomerSuppliers'])->name('sr_getCustomerSuppliers');

    //  Business Overview
    Route::get('standardreport/bankbook-report', [BusinessOverviewController::class, 'getBankbook'])->name('sr_bankbook');
    Route::get('standardreport/bank-reconciliation', [BusinessOverviewController::class, 'getBankReconciliation'])->name('sr_bank_reconciliation');
    Route::get('standardreport/exspend-for-month', [BusinessOverviewController::class, 'getExspenseForMonth'])->name('sr_exspend_month');
    Route::get('standardreport/income-for-month', [BusinessOverviewController::class, 'getIncomeForMonths'])->name('sr_income_month');
    Route::get('standardreport/period-income-statement', [BusinessOverviewController::class, 'PeriodIncomeStatement'])->name('sr_period_income_statment');
    Route::get('standardreport/income-statement/{year?}', [BusinessOverviewController::class, 'IncomeStatement'])->name('sr_income_statment');
    Route::get('standardreport/financial-position', [BusinessOverviewController::class, 'FinancialPosition'])->name('sr_financial_position_quickbooks');
    Route::get('standardreport/balance-sheet', [BusinessOverviewController::class, 'balanceSheet'])->name('sr_balanceSheet');

    // sale and customer
    Route::get('standardreport/branch-data-report', [SaleAndCustomerController::class, 'branchDataReport'])->name('sr_branchDataReport');
    Route::get('standardreport/sales-representative-report', [SaleAndCustomerController::class, 'getSalesRepresentativeReport'])->name('sr_getSalesRepresentativeReport');
    Route::get('standardreport/customer-group', [SaleAndCustomerController::class, 'getCustomerGroup'])->name('sr_getCustomerGroup');
    Route::get('standardreport/purchase-payment-report', [SaleAndCustomerController::class, 'purchasePaymentReport'])->name('sr_purchasePaymentReport');
    Route::get('standardreport/purchase-sell', [SaleAndCustomerController::class, 'getPurchaseSell'])->name('sr_getPurchaseSell');
    Route::get('standardreport/sell-payment-report', [SaleAndCustomerController::class, 'sellPaymentReport'])->name('sr_sellPaymentReport');
    Route::get('standardreport/customer-location-report-unmapped', [SaleAndCustomerController::class, 'customerwithoutmap'])->name('sr_customer_no_map');
    Route::get('standardreport/account-recieveable-unpaid', [SaleAndCustomerController::class, 'accountsReceivableUnpaid'])->name('sr_account_receivable_unpaid');
    Route::get('standardreport/customer-purchase-report', [SaleAndCustomerController::class, 'customerPurchaseReport'])->name('sr_customer_pruchase');
    Route::get('standardreport/customer-loan-report', [SaleAndCustomerController::class, 'customerLoanReport'])->name('sr_customer_loan');
    Route::get('standardreport/vat-sell-report', [SaleAndCustomerController::class, 'vatSalesReport'])->name('sr_vat_sale');
    Route::get('standardreport/monthly-purchase-ledger', [SaleAndCustomerController::class, 'monthlyPurchaseLedger'])->name('sr_monthly_purchase_ledger');
    Route::get('standardreport/withholding-tax-report', [SaleAndCustomerController::class, 'withholdingTaxReport'])->name('sr_withholding_tax_report');
    Route::get('standardreport/rental-invoice', [SaleAndCustomerController::class, 'rentalInvoice'])->name('sr_rental_invoice');
    Route::get('standardreport/expense-purchase-report', [SaleAndCustomerController::class, 'purchasesExpensesReport'])->name('sr_expense_purchase_report');
    Route::get('standardreport/customer-report-via-staff', [SaleAndCustomerController::class, 'customerReportViaStaff'])->name('sr_customer_report_via_staff');

    
    // customer report
    Route::get('standardreport/franchise-monthly-report', [CustomerReportsController::class, 'franchiseMonthlyReport'])->name('sr_franchise_monthly_report');
    Route::get('standardreport/share-benefit-report', [CustomerReportsController::class, 'shareBenefitReport'])->name('sr_share_benefit_report');

    // Human Resource
    Route::get('standardreport/monthly-payroll-report', [HumanResourceController::class, 'monthlyNssfTaxReport'])->name('sr_monthly_payroll_report');
    Route::get('standardreport/payroll-slip', [HumanResourceController::class, 'getSalarySlip'])->name('sr_payroll_slip');
    Route::get('standardreport/late-check-in-report', [HumanResourceController::class, 'lateCheckIn'])->name('sr_late_check_in_report');
    Route::get('standardreport/early-check-out-report', [HumanResourceController::class, 'earlyCheckOut'])->name('sr_early_check_out_report');
    Route::get('standardreport/monthly-attendance-report', [HumanResourceController::class, 'monthlyAttendance'])->name('sr_list_attendance_report');
    Route::get('standardreport/schedule-shift-report', [HumanResourceController::class, 'ShiftSchedule'])->name('sr_shift_schedule_report');
    Route::get('standardreport/monthly-tax-report', [HumanResourceController::class, 'monthlyTaxReport'])->name('minireportb1.standardReport.humanResource.monthly_tax_report');
    Route::get('standardreport/monthly-nssf-tax-report', [HumanResourceController::class, 'monthlyNssfTaxReport'])->name('minireportb1.standardReport.humanResource.monthly_nssf_tax_report');
    Route::get('standardreport/payroll-allowance-deduction-report', [HumanResourceController::class, 'payrollAllowanceDeductionReport'])->name('minireportb1.standardReport.humanResource.payroll_allowance_deduction_report');
    Route::get('standardreport/bank-reconciliation-report', [HumanResourceController::class, 'bankReconciliationReport'])->name('minireportb1.standardReport.humanResource.bank_reconciliation_report');

    // Office Receipt
    Route::get('standardreport/office-receipt', [\Modules\MiniReportB1\Http\Controllers\StandardReport\InvoiceController::class, 'getOfficeReceipt'])->name('minireportb1.office_receipt');
    Route::get('standardreport/transactions', [\Modules\MiniReportB1\Http\Controllers\StandardReport\InvoiceController::class, 'getTransactions'])->name('minireportb1.get_transactions');
    Route::view('standardreport/cash-count', 'minireportb1::MiniReportB1.StandardReport.mony.cashcount')->name('minireportb1.office_receipt');

    // New salary slip routes
    Route::get('standardreport/salary-slip', [HumanResourceController::class, 'getSalarySlip'])->name('minireportb1.salary_slip');

    // tax gov document
    // Route::view('tax-gov-document', 'minireportb1::MiniReportB1.gov_tax.p101_tax_form')->name('tax_gov_document');
    Route::view('tax-gov-document2', 'minireportb1::MiniReportB1.gov_tax.Application_Form_for_Property_Rental_Tax')->name('tax_gov_document2');
    Route::view('tax-gov-document3', 'minireportb1::MiniReportB1.gov_tax.Return_for_Tax_on_Advertisement')->name('tax_gov_document3');
    Route::view('tax-gov-document4', 'minireportb1::MiniReportB1.gov_tax.Application_Form_VAT_SUS_02')->name('tax_gov_document4');
    Route::view('tax-gov-document5', 'minireportb1::MiniReportB1.gov_tax.Application_Form_SUS_01')->name('tax_gov_document5');
    Route::view('AP01-A', 'minireportb1::MiniReportB1.gov_tax.ap01-a')->name('report.ap01.a');


    Route::view('gov-tax/p101-all-fields-prefill', 'minireportb1::MiniReportB1.gov_tax.p101_all_fields_prefill')->name('gov_tax.p101_all_fields_prefill');
});



Route::group(['prefix' => 'minireportb1', 'middleware' => ['web', 'auth']], function () {
    Route::get('/layout', 'MiniReportB1Controller@layout')->name('MiniReportB1.layout');
});

// P101 Tax Form Routes
Route::group(['prefix' => 'tax', 'middleware' => ['web', 'auth']], function () {
    Route::get('p101-form', 'StandardReport\SaleAndCustomerController@p101TaxForm')->name('p101.tax.form');
    Route::post('p101-form/process', 'StandardReport\SaleAndCustomerController@processP101TaxForm')->name('p101.tax.form.process');
    Route::get('p101-form/data', 'StandardReport\SaleAndCustomerController@getP101TaxFormData')->name('p101.tax.form.data');
});
