<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Response;

class LockedThreadsController extends Controller
{
    public function store(Thread $thread)
    {

        $thread->update(['locked' => true]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Thread $thread)
    {

        $thread->update(['locked' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
