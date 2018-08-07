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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login','UserController@login');
Route::post('/auth/register','UserController@store');
Route::middleware('auth:api')->get('/auth/logout','UserController@logout');
Route::middleware('auth:api')->get('/auth/testToken','UserController@testToken');
Route::middleware('auth:api')->post('/auth/changePassword','PasswordController@changePassword');
Route::get('/user/verify/{token}','UserController@activeUser');
Route::post('/sendMailResetpassword','PasswordController@sendMailForgotPassword');
Route::post('/password/reset/{token}','PasswordController@changePasswordByToken');

