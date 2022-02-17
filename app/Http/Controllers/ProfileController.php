<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function show(User $user)
    {

        $user->load('threads');

        return response()->json(ProfileResource::make($user), Response::HTTP_OK);
    }
}
