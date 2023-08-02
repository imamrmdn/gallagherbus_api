<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\InformasiController;

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

//
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//
Route::group(['middleware' => 'auth:api'], function () {
    Route::patch('edit_profile', [AuthController::class, 'edit_profile']);
});

//
Route::prefix('jadwal')->group(function () {

    // router for submit jadwal acak/random to algoritma greedy
    Route::post('submit_to_algorithm_greedy', [JadwalController::class, 'proccess_to_algorithm']);

    // route get jadwal result from algoritma greedy
    Route::get('get', [JadwalController::class, 'get_jadwal']);

    // route get koridor
    Route::get('get_koridor', [JadwalController::class, 'get_koridor']);

    // route get all koridor name
    Route::get('get_all_koridor_name', [JadwalController::class, 'get_all_koridor_name']);

});

//
Route::get('get_informasi', [InformasiController::class, 'get_informasi']);
Route::post('post_informasi', [InformasiController::class, 'post_informasi']);