<?php

namespace Tests\Unit;

use App\Models\Like;
use App\Models\Reply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_liked_item()
    {
        $reply = Reply::factory()->create();

        $like = Like::factory()->create([
            'likeable_id' => $reply->id,
            'likeable_type' => get_class($reply),
        ]);

        $this->assertInstanceOf('\App\Models\Reply', $like->likeable);

    }
}
