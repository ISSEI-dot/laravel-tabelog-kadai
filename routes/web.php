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

Route::post('products/{product}/favorite', [ProductController::class, 'favorite'])->name('products.favorite');

Route::resource('products', ProductController::class)->middleware(['auth', 'verified']);
Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    // マイページ
    Route::get('/mypage', function () {
        return view('users.mypage');
    })->name('mypage');

    // 有料プラン登録ページ (サブスク未登録者のみ)
    Route::middleware('not.subscribed')->group(function () {
        Route::get('/subscription', [SubscriptionController::class, 'showSubscriptionPage'])->name('subscription.index');
        Route::post('/subscription', [SubscriptionController::class, 'processSubscription'])->name('subscription.process');
    });

    // お支払い方法編集と解約 (サブスク登録者のみ)
    Route::middleware('subscribed')->group(function () {
        Route::get('/subscription/edit', [SubscriptionController::class, 'showEditPaymentPage'])->name('subscription.edit');
        Route::post('/subscription/edit', [SubscriptionController::class, 'updatePaymentMethod'])->name('subscription.update');

        // 解約確認ページのルート (GET)
        Route::get('/subscription/cancel', function () {
            return view('subscription.cancel'); // 確認ページを表示
        })->name('subscription.cancel.view');

        // 解約処理のルート (POST)
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    
        //予約履歴ページ（サブスク登録者のみ）
        Route::get('/mypage/reservations', [ReservationController::class, 'index'])->name('mypage.reservations');

        // 予約フォームと予約処理（サブスク登録者のみ）
        Route::get('/products/{product}/reservations/create', [ReservationController::class, 'create'])
            ->name('reservations.create');
        Route::post('/products/{product}/reservations/store', [ReservationController::class, 'store'])
            ->name('reservations.store');
    });

    // サブスクリプション状態確認 (認証必須)
    Route::get('/subscription/status', [SubscriptionController::class, 'checkSubscriptionStatus'])->name('subscription.status');
});

Route::post('/webhook/stripe', [WebhookController::class, 'handleWebhook']);

Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

Route::get('/reservations/{reservation}/complete', [ReservationController::class, 'complete'])
    ->name('reservations.complete');

Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::delete('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel')->middleware('auth'); // 認証済みユーザーのみアクセス可能]

Route::get('/company', [CompanyInfoController::class, 'index'])->name('company.index');

Route::get('/public/products', [ProductController::class, 'index']);

Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

