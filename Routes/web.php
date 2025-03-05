<?php

use Illuminate\Support\Facades\Route;
use Modules\MiniReportB1\Entities\MiniReportB1;

use Modules\MiniReportB1\Http\Controllers\AdvanceTableController;
use Modules\MiniReportB1\Http\Controllers\MiniReportB1Controller;
use Modules\MiniReportB1\Http\Controllers\SaleController;
use Modules\MiniReportB1\Http\Controllers\SettingController;
use Modules\MiniReportB1\Http\Controllers\FolderController;
use Modules\MiniReportB1\Http\Controllers\IncomeForMonthController;
use Modules\MiniReportB1\Http\Controllers\LayoutController;
use Modules\MiniReportB1\Http\Controllers\MultiTableController;
use Modules\MiniReportB1\Http\Controllers\ProductHController;
use Modules\MiniReportB1\Http\Controllers\QuickBook\QuickBookReportController;
use Modules\MiniReportB1\Http\Controllers\ReportController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\BusinessOverviewController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\OperationalReportsController;
// use Modules\MiniReportB1\Http\Controllers\StandardReport\ProductReportController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\ProductReportController;
use Modules\MiniReportB1\Http\Controllers\StandardReport\SaleAndCustomerController;

Route::middleware('web', 'SetSessionData', 'auth', 'MiniReportB1Language', 'timezone', 'AdminSidebarMenu')->prefix('minireportb1')->group(function () {

    Route::get('/', [MiniReportB1Controller::class, 'dashboard'])->name('MiniReportB1.dashboard');
    Route::get('/MiniReportB1', [MiniReportB1Controller::class, 'index'])->name('MiniReportB1.index');
    Route::get('/MiniReportB1/{id}', [MiniReportB1Controller::class, 'show'])->name('MiniReportB1.show');
    Route::get('/create', [MiniReportB1Controller::class, 'create'])->name('MiniReportB1.create');

    Route::get('/install', [Modules\MiniReportB1\Http\Controllers\InstallController::class, 'index']);
    Route::post('/install', [Modules\MiniReportB1\Http\Controllers\InstallController::class, 'install']);
    Route::get('/install/uninstall', [Modules\MiniReportB1\Http\Controllers\InstallController::class, 'uninstall']);


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
    Route::get('/viewFile/{id}', [MultiTableController::class, 'multiViewManager'])
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


    Route::get('/create-report', [SaleController::class, 'sale'])->name('MiniReportB1.create-report');
    Route::post('/save-report-config', 'SaleController@saveReportConfig');
    Route::get('/report-config/{id}', 'SaleController@getReportConfig');
    Route::get('/user-reports', 'SaleController@getUserReports');

    Route::get('/get-folders', [MiniReportB1Controller::class, 'getFolders']);
    Route::post('/create-folder', 'FolderController@createFolder')->name('minireportb1.create.folder');
    Route::post('/create-file', [MiniReportB1Controller::class, 'store'])->name('MiniReportB1.createFile');
    Route::delete('/delete-folder', 'FolderController@deleteFolder')->name('minireportb1.delete.folder');
    Route::delete('/delete-file', 'FolderController@deleteFile')->name('minireportb1.delete.file');

    // Route::get('minireportb1/file/{id}', [MiniReportB1Controller::class, 'viewFile'])
    // ->name('MiniReportB1.viewFile');
    Route::get('/get-print-layout', [FolderController::class, 'getPrintLayout'])->name('minireportb1.get.print.layout');
    Route::post('/rename-file', 'FolderController@renameFile')->name('minireportb1.rename.file');
    Route::post('/move-file', 'FolderController@moveFile')->name('minireportb1.move.file');
    Route::post('createFile', [MiniReportB1Controller::class, 'createFile'])->name('MiniReportB1.createFile');
    Route::post('/store', [MiniReportB1Controller::class, 'store'])->name('MiniReportB1.store');
    Route::get('/folders/list', 'FolderController@getFoldersList')->name('minireportb1.folders.list');
    Route::post('/rename-folder', 'FolderController@renameFolder')->name('minireportb1.rename.folder');
    Route::post('/update-folder-order', 'FolderController@updateFolderOrder')->name('minireportb1.update.folder.order');

    // Dynamic Table Routes
    Route::get('/dynamic-table', [SaleController::class, 'index'])->name('dynamic-table.index');
    Route::get('/dynamic-table/{id}', [AdvanceTableController::class, 'show'])->name('dynamic-table.show');
    Route::post('/dynamic-table/store', [AdvanceTableController::class, 'store'])->name('dynamic-table.store');
    Route::get('/getData', [SaleController::class, 'getData'])->name('minireportb1.getData');

    //Standart Product from quickbooks
    Route::get('/monthly-stock', [ProductHController::class, 'getMonthlyStock'])->name('mini_monthly_stock');
    Route::get('/promotion-product', [ProductHController::class, 'getPromotionProduct'])->name('mini_promotion_product');
    Route::get('/promotion-product-all', [ProductHController::class, 'getPromotionProductAll'])->name('mini_promotion_product_all');
    Route::get('test123/{id}', [ProductHController::class, 'StockHistory'])->name('mini_test123');
    Route::get('product-by-group-price', [ProductHController::class, 'ProductByGroupPrice'])->name('mini_productgroupprice');
    Route::get('product-by-group-price-all', [ProductHController::class, 'ProductByGroupPriceAll'])->name('mini_productgrouppriceall');

    Route::get('/products/monthly-stock', [ProductHController::class, 'getMonthlyStock'])
        ->name('products.monthly-stock');



    // ***************************************************************************************************************************************
    //                                         standard report
    // ***************************************************************************************************************************************


    // operational report
    Route::get('standardreport/profit-loss', [OperationalReportsController::class, 'getProfitLoss'])->name('standardreport.profit_loss');
    Route::get('standardreport/tax-report', [OperationalReportsController::class, 'getTaxReport'])->name('standardreport.tax_report');
    Route::get('standardreport/expense-report', [OperationalReportsController::class, 'getExpenseReport'])->name('standardreport.get_expense');
    Route::get('standardreport/register-report', [OperationalReportsController::class, 'getRegisterReport'])->name('standardreport.get_register');
    Route::get('standardreport/activity-log', [OperationalReportsController::class, 'getTaxReport'])->name('standardreport.get_activitylog');

    // product report 
    Route::get('standardreport/price-list-by-group-price', [ProductReportController::class, 'priceListByGroupPrice'])->name('sr_pricelist');
    Route::get('standardreport/price-list-by-group-price-all', [ProductReportController::class, 'priceListByGroupPriceAll'])->name('sr_pricelist_all');
    Route::get('standardreport/monthly-stock', [ProductReportController::class, 'getMonthlyStock'])->name('sr_monthlystock');
    Route::get('standardreport/promotion-product', [ProductReportController::class, 'getPromotionProduct'])->name('sr_promotion_product');
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

    // customer report


});



Route::group(['prefix' => 'minireportb1', 'middleware' => ['web', 'auth']], function () {
    Route::get('/layout', 'MiniReportB1Controller@layout')->name('MiniReportB1.layout');
});
