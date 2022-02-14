<?php

namespace Tests\Feature\Thread;

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

        $threadToBeDeleted = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->deleteJson(route('threads.delete', $threadToBeDeleted))->assertNoContent();

        $this->assertDatabaseMissing('threads', $threadToBeDeleted->toArray());
    }
}
