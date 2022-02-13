<?php

namespace App\Http\Controllers;

use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ThreadController extends Controller
{
    public function index()
    {

        $threads = Thread::all();

        return response()->json(new ThreadCollection($threads));
    }

    public function show(Thread $thread)
    {
        return response()->json(new ThreadResource($thread));
    }

    public function store(Request $request)
    {
        $attributes = [
            'title' => $request->input('data.title'),
            'slug' => $request->input('data.slug'),
            'body' => $request->input('data.body'),
        ];


        $thread = auth()->user()->threads()->create($attributes);

        return response()->json(new ThreadResource($thread), Response::HTTP_CREATED);
    }

    public function update(Request $request, Thread $thread)
    {

        if ($thread->author->id !== auth()->user()->id) {
            return abort(Response::HTTP_UNAUTHORIZED);
        }

        $attributes = [
            'title' => $request->input('data.attributes.title'),
            'slug' => $request->input('data.attributes.slug'),
            'body' => $request->input('data.attributes.body'),
        ];

        $thread->update($attributes);

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }

    public function destroy(Thread $thread)
    {

        if ($thread->author->id !== auth()->user()->id) {
            return abort(Response::HTTP_UNAUTHORIZED);
        }

        $thread->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
