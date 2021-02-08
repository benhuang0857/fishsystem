<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('store/create-hand-over-histories-db/{id}/{income}/{payout}', 'StoreController@CreateHandOverHistoriesDB');
    $router->resource('store', StoreController::class);
    $router->resource('machine', MachineController::class);
    $router->resource('handover', HandOverHistoryController::class);
    $router->resource('jackpot', JackpotController::class);
});
