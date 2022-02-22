<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Thread;
use Illuminate\Http\Response;

class ThreadSubscriptionController extends Controller
{

    public function store(Channel $channel, Thread $thread)
    {

        $thread->subscribe();
        return response()->json(null, Response::HTTP_CREATED);
    }

    public function destroy(Channel $channel, Thread $thread)
    {

        $thread->unsubscribe();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
