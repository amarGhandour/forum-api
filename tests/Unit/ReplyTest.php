<?php

namespace Tests\Unit;

use App\Models\Like;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_an_owner(): void
    {

        $reply = Reply::factory()->create();

        $this->assertInstanceOf('\App\Models\User', $reply->owner);
    }

    public function test_it_belongs_to_a_thread(): void
    {

        $reply = Reply::factory()->create();

        $this->assertInstanceOf('\App\Models\Thread', $reply->thread);
    }

    public function test_it_has_likes()
    {

        $reply = Reply::factory()->create();

        Like::factory()->create([
            'likeable_id' => $reply->id,
            'likeable_type' => get_class($reply),
        ]);

        $this->assertCount(1, $reply->likes);
    }

    public function test_has_user_liked_a_reply()
    {
        $reply = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Like::factory()->create([
            'user_id' => $user->id,
            'likeable_id' => $reply->id,
            'likeable_type' => get_class($reply),
        ]);

        $this->assertTrue($reply->isLiked());

        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $this->assertFalse($reply->isLiked());

    }

    public function test_it_unlike_a_reply()
    {
        $reply = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Like::factory()->create([
            'user_id' => $user->id,
            'likeable_id' => $reply->id,
            'likeable_type' => get_class($reply),
        ]);

        $reply->unlike();

        $this->assertCount(0, $reply->likes);

    }
}
