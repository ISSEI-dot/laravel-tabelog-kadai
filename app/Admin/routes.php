<?php

use Illuminate\Routing\Router;
use App\Admin\Controllers\CategoryController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\MajorCategoryController;
use App\Admin\Controllers\UserController;
use App\Admin\Controllers\ReservationController;
use App\Admin\Controllers\CompanyInfoController;
use App\Models\CompanyInfo;

Admin::Routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('categories', CategoryController::class);
    $router->resource('products', ProductController::class);
    $router->resource('major-categories', MajorCategoryController::class);
    $router->resource('users', UserController::class);
    $router->post('products/import', [ProductController::class, 'csvImport']);
    $router->resource('reservations', ReservationController::class);
    $router->resource('company-info', CompanyInfoController::class);
});
