<?php

namespace Tests\Feature\Reply;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateRepliesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthenticated_user_can_not_add_a_reply_to_a_thread()
    {

        $thread = Thread::factory()->create();

        $this->postJson(route('replies.store', $thread))->assertUnauthorized();

    }

    public function test_an_authenticated_user_may_add_a_reply_to_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make([
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $this->postJson(route('replies.store', $thread), $reply->toArray())
            ->assertCreated();

        $this->assertDatabaseHas('replies', $reply->toArray());

    }
}
