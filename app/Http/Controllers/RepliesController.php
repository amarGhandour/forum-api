<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplyStoreRequest;
use App\Http\Requests\ReplyUpdateRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Http\Response;

class RepliesController extends Controller
{

    public function store(ReplyStoreRequest $request, Thread $thread)
    {

        $reply = $thread->replies()->create([
            'body' => $request->input('data.body'),
            'user_id' => auth()->id(),
        ]);

        return response()->json(ReplyResource::make($reply), Response::HTTP_CREATED);

    }

    public function update(ReplyUpdateRequest $request, Reply $reply)
    {

        $this->authorize('update', $reply);

        $attributes = [
            'body' => $request->input('data.body')
        ];

        $reply->update($attributes);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
