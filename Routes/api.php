<?php

use Illuminate\Support\Facades\Route;
use Modules\MiniReportB1\Http\Controllers\Api\MiniReportB1Controller;

Route::middleware('auth:api', 'timezone')->prefix('connector/api')->group(function () {
    Route::get('/MiniReportB1-field', [MiniReportB1Controller::class, 'modulefield']);

    Route::get('/MiniReportB1', [MiniReportB1Controller::class, 'index']);
    Route::get('/MiniReportB1/create', [MiniReportB1Controller::class, 'create']);
    Route::post('/MiniReportB1', [MiniReportB1Controller::class, 'store']);
    Route::get('/MiniReportB1/edit/{id}', [MiniReportB1Controller::class, 'edit']);
    Route::put('/MiniReportB1/edit/{id}', [MiniReportB1Controller::class, 'update']);
    Route::delete('/MiniReportB1/delete/{id}', [MiniReportB1Controller::class, 'destroy']);
    
    Route::get('/MiniReportB1-categories', [MiniReportB1Controller::class, 'getCategories']);
    Route::post('/MiniReportB1-categories', [MiniReportB1Controller::class, 'storeCategory']);
    Route::get('/MiniReportB1-categories/edit/{id}', [MiniReportB1Controller::class, 'editCategory']);
    Route::put('/MiniReportB1-categories/{id}', [MiniReportB1Controller::class, 'updateCategory']);
    Route::delete('/MiniReportB1-categories/{id}', [MiniReportB1Controller::class, 'destroyCategory']);

    

});