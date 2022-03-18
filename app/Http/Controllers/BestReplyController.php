<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Response;

class BestReplyController extends Controller
{
    public function store(Reply $reply)
    {

        $this->authorize('update', $reply->thread);

        $reply->thread->markBestReply($reply);

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }
}
