<?php

use App\Http\Controllers\AvatarUserController;
use App\Http\Controllers\BestReplyController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\LockedThreadsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ThreadSubscriptionController;
use App\Http\Controllers\UserNotificationsController;
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

Route::prefix('v1')->group(function () {

    Route::get('threads', [ThreadController::class, 'index'])->name('threads.index');
    Route::get('threads/{channel:slug}', [ThreadController::class, 'index']);
    Route::get('threads/{channel:slug}/{thread}', [ThreadController::class, 'show'])->name('threads.show');

    // reply
    Route::get('threads/{channel:slug}/{thread}/replies', [RepliesController::class, 'index'])->name('replies.index');

    Route::middleware('auth:sanctum')->group(function () {

        // thread
        Route::post('threads', [ThreadController::class, 'store'])->name('threads.store');
        Route::patch('threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
        Route::delete('threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.delete');

        // thread subscriptions
        Route::post('threads/{channel:slug}/{thread}/subscriptions', [ThreadSubscriptionController::class, 'store'])
            ->name('threads.subscriptions.store');
        Route::delete('threads/{channel:slug}/{thread}/subscriptions', [ThreadSubscriptionController::class, 'destroy'])
            ->name('threads.subscriptions.destroy');

        // locked threads
        Route::group(['middleware' => 'admin'], function () {
            Route::post('locked-threads/{thread}', [LockedThreadsController::class, 'store'])->name('locked-threads.store');
            Route::delete('locked-threads/{thread}', [LockedThreadsController::class, 'destroy'])->name('locked-threads.destroy');
        });

        // reply
        Route::post('threads/{thread}/replies', [RepliesController::class, 'store'])->name('replies.store');
        Route::patch('replies/{reply}', [RepliesController::class, 'update'])->name('replies.update');
        Route::delete('replies/{reply}', [RepliesController::class, 'destroy'])->name('replies.destroy');

        //best reply
        Route::post('replies/{reply}/best', [BestReplyController::class, 'store'])->name('best-replies.store');

        // likes
        Route::post('replies/{reply}/likes', [LikesController::class, 'store'])->name('replies.likes');
        Route::delete('replies/{reply}/likes', [LikesController::class, 'destroy'])->name('replies.likes.destroy');

        // profile
        Route::get('profiles/{user:name}', [ProfileController::class, 'show'])->name('profile');

        // notifications
        Route::delete('profiles/{user:name}/notifications/{notification}', [UserNotificationsController::class, 'destroy'])
            ->name('thread-notifications.destroy');
        Route::get('profiles/{user:name}/notifications', [UserNotificationsController::class, 'index'])
            ->name('thread-notifications.index');

        //users
        Route::post('users/{user}/avatar', [AvatarUserController::class, 'update']);

    });

});
