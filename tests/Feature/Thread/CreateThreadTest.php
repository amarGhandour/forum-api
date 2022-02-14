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
            'user_id' => $user->id
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.channel'])->response()->getData(true);

        $resourceObject['data']['channel_id'] = $thread->channel_id;

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

    public function test_it_validates_that_the_title_is_required_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.title', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.title field is required.',
                        'source' => [
                            'pointer' => '/data/title'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('threads', $resourceObject['data']);

    }

    public function test_it_validates_that_the_title_must_be_string_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null,
            'title' => 3874837,
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.title must be a string.',
                        'source' => [
                            'pointer' => '/data/title'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('threads', $resourceObject['data']);

    }

    public function test_it_validates_that_the_body_is_required_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.body', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.body field is required.',
                        'source' => [
                            'pointer' => '/data/body'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('threads', $resourceObject['data']);

    }

    public function test_it_validates_that_the_body_must_be_string_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null,
            'body' => 3874837,
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.body must be a string.',
                        'source' => [
                            'pointer' => '/data/body'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('threads', $resourceObject['data']);

    }

    public function test_it_validates_that_the_slug_is_required_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.slug', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.slug field is required.',
                        'source' => [
                            'pointer' => '/data/slug'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('threads', $resourceObject['data']);

    }

    public function test_it_validates_that_the_slug_must_be_unique_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $threadInDatabase = Thread::factory()->create();

        $thread = Thread::factory()->make([
            'user_id' => null,
            'slug' => $threadInDatabase->slug,
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.slug has already been taken.',
                        'source' => [
                            'pointer' => '/data/slug'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('threads', $resourceObject['data']);

    }


}
