<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SubscriptionController;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\FavoriteController;

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

Route::get('/',  [WebController::class, 'index'])->name('top');

Route::controller(UserController::class)->group(function () {
    Route::get('users/mypage', 'mypage')->name('mypage');
    Route::get('users/mypage/edit', 'edit')->name('mypage.edit');
    Route::put('users/mypage', 'update')->name('mypage.update');
    Route::get('users/mypage/password/edit', 'edit_password')->name('mypage.edit_password');
    Route::put('users/mypage/password', 'update_password')->name('mypage.update_password');
    Route::get('users/mypage/favorite', 'favorite')->name('mypage.favorite');
    Route::delete('users/mypage/delete', 'destroy')->name('mypage.destroy');
});

Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');

// 編集用
Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
Route::post('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');

// 削除用
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('products/{product}/favorite', [ProductController::class, 'favorite'])->name('products.favorite');

Route::resource('products', ProductController::class)->middleware(['auth', 'verified']);
Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
    Route::post('/subscription/store', [SubscriptionController::class, 'store'])->name('subscription.store');

    Route::middleware(['subscribed'])->group(function () {
        Route::get('/subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
        Route::post('/subscription/update', [SubscriptionController::class, 'update'])->name('subscription.update');
        Route::get('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::delete('/subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
    });
});

Route::post('/stripe/webhook',[WebhookController::class, 'handleWebhook']);

Route::resource('reservations', ReservationController::class);

Route::prefix('products/{product}')->group(function () {
    Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
});

Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

Route::get('/reservations/{reservation}/complete', [ReservationController::class, 'complete'])
    ->name('reservations.complete');

Route::get('/mypage/reservations', [ReservationController::class, 'index'])
    ->name('mypage.reservations')->middleware('auth'); // 認証済みユーザーのみアクセス可能

Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::delete('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel')->middleware('auth'); // 認証済みユーザーのみアクセス可能]

Route::get('/company', [CompanyInfoController::class, 'index'])->name('company.index');

Route::get('/public/products', [ProductController::class, 'index']);

Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

