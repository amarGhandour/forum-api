<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThreadStoreRequest;
use App\Http\Requests\ThreadUpdateRequest;
use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
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

    public function store(ThreadStoreRequest $request)
    {

        $thread = auth()->user()->threads()->create($request->validated()['data']);

        return response()->json(new ThreadResource($thread), Response::HTTP_CREATED);
    }

    public function update(ThreadUpdateRequest $request, Thread $thread)
    {

        $this->authorize('update', $thread);

        $thread->update($request->validated()['data']);

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }

    public function destroy(Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
