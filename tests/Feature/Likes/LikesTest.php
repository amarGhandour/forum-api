<?php

namespace Tests\Feature\Likes;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_can_not_like_a_reply()
    {
        $likeable = Reply::factory()->create();

        $this->postJson(route('replies.likes', $likeable))->assertUnauthorized();
    }

    public function test_an_authenticated_user_may_like_a_reply()
    {

        $likeable = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson(route('replies.likes', $likeable))->assertCreated();

        $this->assertCount(1, $likeable->likes);
    }

    public function test_an_authenticated_user_can_like_a_reply_once_only()
    {

        $this->withoutExceptionHandling();

        $likeable = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        try {
            $this->postJson(route('replies.likes', $likeable))->assertCreated();
            $this->postJson(route('replies.likes', $likeable));
        } catch (\Exception $exception) {
            $this->fail('Did not expect to insert the same record twice.');
        }

        $this->assertCount(1, $likeable->likes);
    }

    public function test_an_authenticated_user_can_unlike_a_reply()
    {

        $this->withoutExceptionHandling();

        $reply = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reply->like();

        $this->deleteJson(route('replies.likes.destroy', $reply))->assertNoContent();

        $this->assertCount(0, $reply->likes);
    }
}
