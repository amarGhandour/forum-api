<?php

namespace Tests\Feature\Thread;

use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteThreadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthorized_user_can_not_delete_a_thread(): void
    {

        $thread = Thread::factory()->create();

        $this->deleteJson(route('threads.delete', $thread))->assertUnauthorized();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson(route('threads.delete', $thread))->assertForbidden();

        $this->assertDatabaseHas('threads', $thread->toArray());
    }

    public function test_an_authorized_user_can_delete_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $reply = Reply::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->deleteJson(route('threads.delete', $thread))->assertNoContent();

        $this->assertDatabaseMissing('threads', $thread->toArray());
        $this->assertDatabaseMissing('replies', $reply->toArray());
        $this->assertCount(0, Activity::all());
    }
}
