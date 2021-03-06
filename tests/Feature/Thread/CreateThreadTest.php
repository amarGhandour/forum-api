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
            ->hide(['data.id', 'data.author', 'data.channel', 'data.replies_count', 'data.slug'])
            ->response()
            ->getData(true);

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $response = $this->postJson('api/v1/threads', $resourceObject)
            ->assertCreated();

        $this->assertDatabaseHas('threads', [
            'title' => $thread->title,
            'body' => $thread->body,
        ]);

        $threadCreated = Thread::latest()->first();

        $resourceObject = ThreadResource::make($threadCreated);

        $response->assertResource($resourceObject->hide(['data.replies', 'data.replies_count', 'data.visits', 'data.slug']));

    }

    public function test_threads_that_contain_spam_not_be_created(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null,
            'body' => 'yahoo customer service'
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.channel', 'data.replies_count'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.body field contains spam.',
                        'source' => [
                            'pointer' => '/data/body'
                        ]
                    ]
                ]
            ]);

    }

    public function test_user_can_only_create_thread_a_maximum_of_once_per_minute()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null,
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.channel', 'data.replies_count'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertCreated();

        $resourceObject = ThreadResource::make(Thread::factory()->make())
            ->hide(['data.id', 'data.author', 'data.channel', 'data.replies_count'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->postJson('api/v1/threads', $resourceObject)
            ->assertStatus(429);

    }


    public function test_it_validates_that_the_title_is_required_when_creating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->make([
            'user_id' => null
        ]);

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.title', 'data.channel', 'data.replies_count'])
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
            ->hide(['data.id', 'data.author', 'data.channel', 'data.replies_count'])
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
            ->hide(['data.id', 'data.author', 'data.body', 'data.channel', 'data.replies_count'])
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
            ->hide(['data.id', 'data.author', 'data.channel', 'data.replies_count'])
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

    public function test_a_thread_must_have_a_unique_slug()
    {

        $thread = Thread::factory()->create(['title' => 'foo title']);

        Sanctum::actingAs(User::factory()->create());

        $thread = $this->postJson(route('threads.store'), [
            'data' => [
                'title' => $thread->title,
                'body' => $thread->body,
                'channel_id' => $thread->channel->id,
            ]
        ])->assertCreated()->json();

        $this->assertEquals("foo-title-{$thread['data']['id']}", $thread['data']['slug']);

    }

    public function test_a_thread_with_a_title_ends_with_a_number_should_generate_proper_one()
    {

        $thread = Thread::factory()->create(['title' => 'foo title 24']);

        Sanctum::actingAs(User::factory()->create());

        $thread = $this->postJson(route('threads.store'), [
            'data' => [
                'title' => $thread->title,
                'body' => $thread->body,
                'channel_id' => $thread->channel->id,
            ]
        ])->assertCreated()->json();

        $this->assertEquals("foo-title-24-{$thread['data']['id']}", $thread['data']['slug']);

    }


}
