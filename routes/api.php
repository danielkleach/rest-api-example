<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/token', 'Auth\AuthController@store');

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('products', 'ProductController', ['except' => 'create', 'edit']);
    Route::resource('user-products', 'UserProductController', [
        'except' => 'show', 'create', 'edit', 'update'
    ]);

    Route::post('products/{id}/image', 'ProductImageController@store');
});
