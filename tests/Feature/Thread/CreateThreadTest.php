<?php

namespace Tests\Feature\Thread;

use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateThreadTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_guest_can_not_create_a_thread(): void
    {

        $this->postJson('api/v1/threads', [])->assertUnauthorized();
    }

    public function test_an_authenticated_user_may_create_a_thread_from_a_resource_object(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author'])
            ->response()
            ->getData('true');

        $response = $this->postJson('api/v1/threads', $resourceObject)
            ->assertCreated();

        $this->assertDatabaseHas('threads', [
            'title' => $thread->title,
            'body' => $thread->body,
            'slug' => $thread->slug,
        ]);

        $threadCreated = Thread::where('slug', $thread->slug)->first();

        $resourceObject = ThreadResource::make($threadCreated);

        $response->assertResource($resourceObject->hide(['data.replies']));

    }

}
