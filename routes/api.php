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

/* No Authentication Route */

Route::get('tes', 'Api\AuthController@tes');

/* JWT Authentication */

Route::post('login', 'Api\AuthController@login');

/* End point module(s) */

Route::group(['middleware' => 'jwt.auth'], function () {
	  
	Route::get('/user', 'Api\AuthController@getUser');

	/**
     * Master Module(s)
     */

	Route::get('/group/{param}', 'Api\Master\GroupController@all');
	Route::get('/product/{param}', 'Api\Master\ProductController@all');
	Route::get('/store', 'Api\Master\StoreController@all');
	Route::get('/competitor/{param}', 'Api\Master\GroupCompetitorController@all');
	Route::get('/competitor/{param}/{param2}', 'Api\Master\GroupCompetitorController@allCategory');

	/**
     * Transaction Module(s)
     */

	Route::post('/sales/{param}', 'Api\Master\SalesController@store');

	

});
