<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Response;

class LikesController extends Controller
{

    public function store(Reply $reply)
    {

        $reply->like();
        return response()->json(null, Response::HTTP_CREATED);
    }
}
