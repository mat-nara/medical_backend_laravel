<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserRoleController;

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

Route::group(['middleware'=> ['cors']], function(){

    Route::post('login',                            [AuthenticationController::class, 'login']);
    Route::get('logout',                            [AuthenticationController::class, 'logout'])             ->middleware('auth:sanctum');
    Route::get('authenticated-user',                [AuthenticationController::class, 'authenticated_user']) ->middleware('auth:sanctum');
    Route::post('send-password-reset-link-email',   [AuthenticationController::class, 'sendPasswordResetLinkEmail']);
    Route::post('reset-password',                   [AuthenticationController::class, 'resetPassword'])->name('password.reset');

    Route::get('users',           [UserController::class, 'index'])     ->middleware(['auth:sanctum', 'ability:admin']);
    Route::post('users',          [UserController::class, 'store'])     ->middleware(['auth:sanctum', 'ability:admin']);
    Route::get('users/{user}',    [UserController::class, 'show'])      ->middleware(['auth:sanctum']);
    Route::put('users/{user}',    [UserController::class, 'update'])    ->middleware(['auth:sanctum']);
    Route::delete('users/{user}', [UserController::class, 'destroy'])   ->middleware(['auth:sanctum', 'ability:admin']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



#Route::apiResource('roles', RoleController::class)->except(['create', 'edit'])->middleware(['auth:sanctum', 'ability:admin,super-admin,user']);

