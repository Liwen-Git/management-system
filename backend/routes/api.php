<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::namespace('Api')->group(function () {
    Route::get('captcha', 'SelfController@captcha');
    Route::post('login', 'AuthController@login');
});

Route::namespace('Api')
    ->middleware('refresh.token')
    ->group(function() {
        Route::post('logout', 'AuthController@logout');
        Route::post('me', 'AuthController@me'); // test token

        // 获取 用户信息 权限
        Route::get('user/info', 'SelfController@userInfo');

        // 权限列表
        Route::get('permission/list', 'PermissionController@list');
        Route::post('permission/add', 'PermissionController@add');
        Route::post('permission/edit', 'PermissionController@edit');
        Route::post('permission/delete', 'PermissionController@delete');

        // 角色
        Route::get('role/list', 'RoleController@list');
        Route::post('role/add', 'RoleController@add');
        Route::post('role/edit', 'RoleController@edit');
        Route::post('role/delete', 'RoleController@delete');

        // 用户
        Route::get('user/list', 'UserController@list');
        Route::post('user/add', 'UserController@add');
        Route::post('user/edit', 'UserController@edit');
        Route::post('user/delete', 'UserController@delete');
        Route::post('user/change/password', 'UserController@changePassword');

        // 上传
        Route::post('upload/image', 'UploadController@uploadImage');
        Route::post('upload/file', 'UploadController@uploadFile');
        Route::post('upload/excel', 'UploadController@uploadAndReadExcel');

        // 导出
        Route::get('export/demo', 'ExportController@exportDemo');
    });
