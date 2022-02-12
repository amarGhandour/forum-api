<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateThreadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthorized_user_can_not_update_a_thread(): void
    {

        $thread = Thread::factory()->create();

        $this->patchJson(route('threads.update', $thread), [])->assertUnauthorized();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->patchJson(route('threads.update', $thread), [])->assertUnauthorized();

        $this->assertDatabaseHas('threads', $thread->toArray());
    }


    public function test_an_authorized_user_can_update_a_thread()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id
        ]);

        $this->patchJson(route('threads.update', $thread),
            [
                'data' => [
                    'id' => $thread->id,
                    'type' => 'threads',
                    'attributes' => [
                        'title' => 'this is title of a thread',
                        'body' => $thread->body,
                        'slug' => $thread->slug,
                    ]
                ]
            ])->assertNoContent();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'this is title of a thread',
        ]);

    }
}
