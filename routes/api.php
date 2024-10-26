<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\FileBackupController;
use App\Http\Controllers\FileActionsLogController;
use App\Http\Controllers\FileController;
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

#################### AUTH ####################
Route::group([
    'prefix'        => 'auth',
    'controller'    => AuthController::class
], function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('/profile', 'getUserProfile');
    });
});
#################### END AUTH #################



Route::group([
    'prefix' => '/users',
    'controller' => UserController::class,
    // 'middleware' => ''
], function () {
});

Route::group([
    'prefix' => '/files',
    'controller' => FileController::class,
    // 'middleware' => ''
], function () {
});

Route::group([
    'prefix' => '/file-actions-logs',
    'controller' => FileActionsLogController::class,
    // 'middleware' => ''
], function () {
    Route::get('/', 'getAll');
});

Route::group([
    'prefix' => '/file-backups',
    'controller' => FileBackupController::class,
    // 'middleware' => ''
], function () {
});

Route::group([
    'prefix' => '/groups',
    'controller' => GroupController::class,
    // 'middleware' => ''
], function () {
});

