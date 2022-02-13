<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RepliesController extends Controller
{

    public function store(Request $request, Thread $thread)
    {

        $thread->replies()->create([
            'body' => $request->input('body'),
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(null, Response::HTTP_CREATED);

    }

    public function update(Request $request, Reply $reply)
    {

        if (auth()->user()->id !== $reply->owner->id)
            return abort(401);

        $attributes = [
            'body' => $request->input('data.attributes.body')
        ];

        $reply->update($attributes);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Reply $reply)
    {

        if (auth()->user()->id !== $reply->owner->id)
            return abort(401);

        $reply->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
