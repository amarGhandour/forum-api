<?php

use App\Http\Controllers\ThreadController;
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

Route::get('threads', [ThreadController::class, 'index']);
Route::get('threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::post('threads', [ThreadController::class, 'store'])->name('threads.store');
    Route::patch('threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.delete');

});
