<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SellController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index'])->name('home');

Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::post('/register', [RegisterController::class, 'store'])->name('register');

Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');

Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->middleware('auth')->name('purchase.show');
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');


Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/items/{item}/like', [ItemController::class, 'like'])->name('items.like');

Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('purchase.address.edit');
Route::put('/purchase/address/{item}', [AddressController::class, 'update'])->name('purchase.address.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [MypageController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [MypageController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [MypageController::class, 'update'])->name('mypage.profile.update');
    Route::get('/sell', [SellController::class, 'create'])->name('sell');
    Route::post('/sell', [SellController::class, 'store'])->name('items.store');
});