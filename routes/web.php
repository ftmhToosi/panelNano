<?php

use App\Http\Controllers\Api\v1\PurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('warranty/purchase', [PurchaseController::class, 'purchase'])->name('purchase');

Route::get('purchase/result', [PurchaseController::class, 'result'])->name('purchase.result');

Route::get('transactions', [PurchaseController::class, 'index']);


Route::get('/phpinfo', function () {
    phpinfo();
});
