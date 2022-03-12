<?php

namespace Tests\Feature\Notifications;

use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function route;

class MentionUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_mentioned_users_in_a_reply_are_notified(){

        $this->withoutExceptionHandling();

        $mentionedUser = User::factory()->create(['name' => 'JohnDoe']);
        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => "@JohnDoe look at this."
        ]);

        $resourceObject = ReplyResource::make($reply)->hide(['id', 'owner'])->response()->getData(true);

        $this->postJson(route('replies.store', $thread), $resourceObject)
            ->assertCreated();

        $this->assertCount(1, $mentionedUser->notifications);

    }
}
