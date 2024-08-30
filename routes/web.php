<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;


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
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');

Route::group(['middleware' => 'auth'], function() {
    Route::get('admin', [App\Http\Controllers\ProductController::class, 'index'])->name('admin');
    Route::get('operator', [App\Http\Controllers\OperatorController::class, 'index'])->name('operator');  
    Route::get('order', [App\Http\Controllers\OrderController::class, 'index'])->name('order');  
    Route::get('cust', [App\Http\Controllers\OrderController::class, 'index'])->name('cust');  
    Route::get('logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
        
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::post('operator', [App\Http\Controllers\OperatorController::class, 'store'])->name('operator.store');
    Route::resource('customers', CustomerController::class);

    Route::get('/report', [App\Http\Controllers\OperatorController::class, 'report'])->name('report');
});