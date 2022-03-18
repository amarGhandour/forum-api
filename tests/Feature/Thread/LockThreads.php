<?php

namespace Tests\Feature\Thread;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LockThreads extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_non_administrators_can_not_lock_threads()
    {

        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create();

        $this->assertFalse($thread->locked);

        $this->postJson(route('locked-threads.store', $thread))->assertForbidden();

        $this->assertFalse($thread->fresh()->locked);

    }

    public function test_non_administrators_can_not_unlock_threads()
    {

        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create(['locked' => true]);

        $this->assertTrue($thread->locked);

        $this->deleteJson(route('locked-threads.destroy', $thread))->assertForbidden();

        $this->assertTrue($thread->fresh()->locked);

    }


    public function test_administrators_can_lock_threads()
    {

        Sanctum::actingAs($user = User::factory()->create(['admin' => true]));

        $thread = Thread::factory()->create();

        $this->assertFalse($thread->locked);

        $this->postJson(route('locked-threads.store', $thread))->assertNoContent();

        $this->assertTrue($thread->fresh()->locked);

    }

    public function test_administrators_can_unlock_threads()
    {

        Sanctum::actingAs($user = User::factory()->create(['admin' => true]));

        $thread = Thread::factory()->create(['locked' => true]);

        $this->assertTrue($thread->locked);

        $this->deleteJson(route('locked-threads.destroy', $thread))->assertNoContent();

        $this->assertFalse($thread->fresh()->locked);

    }


    public function test_admin_once_locked_a_thread_may_not_receive_any_new_replies()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create(['locked' => true]);

        $this->postJson(route('replies.store', $thread), [
            'data' => [
                'body' => 'Foo'
            ]
        ])->assertUnprocessable();
    }
}
