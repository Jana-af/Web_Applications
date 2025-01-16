<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\FileBackupController;
use App\Http\Controllers\FileActionsLogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Models\FileActionsLog;
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
    // 'middleware' => ''
], function () {

    Route::group([
        'controller' => UserController::class,
        // 'middleware' => ''
    ], function () {
        Route::get('', 'getAllUsers');
        Route::get('/count', 'getCount');
        Route::post('', 'store');
        Route::delete('/{id}', 'delete');
        Route::get('/user-invites', 'getMyInvites');
        Route::put('/action-on-invite', 'acceptOrRejectOrCancelInvite');
    });

    Route::group([
        'prefix' => '/groups',
        'controller' => GroupController::class,
        // 'middleware' => ''
    ], function () {
        Route::get('', 'getMyGroups');
    });
});

Route::group([
    'prefix' => '/files',
    // 'middleware' => ''
], function () {


    Route::group([
        'controller' => FileController::class,
        // 'middleware' => ''
    ], function () {
        Route::get('/get-diff', 'getDiff');
        Route::get('/count', 'getCount');
        Route::get('/get-all-requests', 'getFileRequests');
        Route::get('/user-checked-in', 'getUserCheckedInFiles');
        Route::post('/store', 'store');
        Route::get('/download/{id}', 'downloadFile');
        Route::get('/get-by-group', 'getFilesInGroup');
        Route::get('/{id}', 'findById');
        Route::put('/action-on-files', 'acceptOrRejectRequest');
        Route::post('/check-out', 'checkOut');
        Route::post('/check-in', 'checkIn');
        Route::post('/update/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    Route::group([
        'prefix' => '/file-versions',
        'controller' => FileBackupController::class,
        // 'middleware' => ''
    ], function () {
        Route::get('/{id}', 'getFileVersions');
        Route::get('/download/{id}', 'downloadFile');
    });

    Route::group([
        'prefix' => '/{id}/file-actions-log',
        'controller' => FileActionsLogController::class,
        // 'middleware' => ''
    ], function () {
        Route::get('/by-file-id', 'getByFileId');
        Route::get('/by-user-id', 'getByUserId');
        Route::get('/report-by-file-id', 'getExcelReportByFileId');
        Route::get('/report-by-user-id', 'getExcelReportByUserId');
    });
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
], function () {});

Route::group([
    'prefix' => '/groups',
    // 'middleware' => ''
], function () {

    Route::group([
        'controller' => UserController::class,
        'prefix' => '/users',
        // 'middleware' => ''
    ], function () {
        Route::post('/remove', 'removeUserFromGroup');
        Route::post('/invite', 'inviteUserToGroup');
        Route::get('', 'getUsersInGroup');
    });

    Route::group([
        'controller' => GroupController::class,
        // 'middleware' => ''
    ], function () {
        Route::get('/', 'getAll');
        Route::get('/count', 'getCount');
        Route::delete('/{id}', 'delete');
        Route::post('/store', 'store');
    });
});
