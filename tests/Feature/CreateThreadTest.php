<?php

namespace Tests\Feature;

use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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

        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make();
        $resourceObject = ThreadResource::make($thread)->response()->getData('true');

        $response = $this->postJson('api/v1/threads', $resourceObject)
            ->assertCreated();

        $this->assertDatabaseHas('threads', [
            'title' => $thread->title,
            'body' => $thread->body,
            'slug' => $thread->slug,
        ]);

        $threadCreated = DB::table('threads')->where('slug', $thread->slug)->first();

        $response->assertResource(new ThreadResource($threadCreated));


    }

}
