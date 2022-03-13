<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvatarUserController extends Controller
{

    public function update(User $user, Request $request)
    {

        $this->authorize('update', $user);

        $request->validate(['avatar' => ['required', 'image']]);

        auth()->user()->update(['avatar_path' => $request->file('avatar')->store('avatars', 'public')]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
