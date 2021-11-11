<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
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

Route::post('send-link', [RegisterController::class, 'sendInvitaionLink']);
Route::post('register/{code}', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::get('pin-verify', [RegisterController::class, 'PINVerifying']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('profile-update', [UserController::class, 'profile_Update']);
});
