<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
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


Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);

//guard:users
Route::group(['middleware' => ['auth:users']], function () {
    Route::post('/user/signout', [UserController::class, 'logout']);
    Route::post('/user/updateProfile', [UserController::class, 'updateProfile']);
    Route::post('/user/editPassword', [UserController::class, 'editPassword']);
});



Route::post('/admin/register', [AdminController::class, 'register']);

Route::post('/admin/login', [AdminController::class, 'login']);

Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    Route::get('/admin/getAllUser', [AdminController::class, 'getAllUser']);
    Route::post('/admin/updateProfile', [AdminController::class, 'updateProfile']);
    Route::post('/admin/editPassword', [AdminController::class, 'editPassword']);
});



Route::post('/company/register', [CompanyController::class, 'register']);

Route::post('/company/login', [CompanyController::class, 'login']);

Route::group(['middleware' => ['auth:companies']], function () {
    Route::post('/company/logout', [CompanyController::class, 'logout']);
    Route::get('/company/getAllUser', [CompanyController::class, 'getAllUser']);
    Route::get('/company/getAllAdmin', [CompanyController::class, 'getAllAdmin']);
    Route::post('/company/forgot', [CompanyController::class, 'forgot']);
});
