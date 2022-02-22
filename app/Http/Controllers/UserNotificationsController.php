<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class UserNotificationsController extends Controller
{

    public function index()
    {

        $unreadNotifications = auth()->user()->unreadNotifications;

        return response()->json($unreadNotifications);
    }

    public function destroy(User $user, $notificationId)
    {

        auth()->user()->unreadNotifications()->findOrFail($notificationId)->markAsRead();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
