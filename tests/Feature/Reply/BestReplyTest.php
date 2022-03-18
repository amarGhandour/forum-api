<?php

namespace Tests\Feature\Reply;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function route;

class BestReplyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create(['user_id' => $user->id]);

        $replies = Reply::factory(2)->create(['thread_id' => $thread->id]);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', $replies[1]));

        $this->assertTrue($replies[1]->fresh()->isBest());

    }

    public function test_only_the_thread_creator_may_mark_a_reply_as_best()
    {
        Sanctum::actingAs(User::factory()->create());

        $thread = Thread::factory()->create();

        $replies = Reply::factory(2)->create(['thread_id' => $thread->id]);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', $replies[1]))->assertForbidden();

        $this->assertFalse($replies[1]->fresh()->isBest());

    }

    public function test_if_a_best_reply_is_deleted_then_the_thread_updated_to_reflect_that()
    {

        Sanctum::actingAs($user = User::factory()->create());

        $reply = Reply::factory()->create(['user_id' => $user->id]);

        $reply->thread->markBestReply($reply);

        $this->assertTrue($reply->isBest());

        $this->deleteJson(route('replies.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);

    }
}
