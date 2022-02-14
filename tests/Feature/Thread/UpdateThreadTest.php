<?php

namespace Tests\Feature\Thread;

use App\Http\Resources\ThreadResource;
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

        $resourceObject = ThreadResource::make($thread)
            ->hide(['data.id', 'data.author', 'data.replies', 'data.channel'])
            ->response()
            ->getData(true);

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)->assertUnauthorized();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->patchJson(route('threads.update', $thread), $resourceObject)->assertForbidden();

        $this->assertDatabaseHas('threads', $resourceObject['data']);
    }


    public function test_an_authorized_user_can_update_a_thread()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make())
            ->hide(['data.id', 'data.author', 'data.replies', 'data.channel'])
            ->response()
            ->getData(true);

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)->assertNoContent();

        $this->assertDatabaseHas('threads', $resourceObject['data']);

    }

    public function test_it_validates_that_the_title_is_required_when_updating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make())
            ->hide(['data.id', 'data.author', 'data.title', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
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

    public function test_it_validates_that_the_title_must_be_string_when_updating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make(['title' => 37483784]))
            ->hide(['data.id', 'data.author', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
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

    public function test_it_validates_that_the_body_is_required_when_updating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make())
            ->hide(['data.id', 'data.author', 'data.body', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
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

    public function test_it_validates_that_the_body_must_be_string_when_updating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make(['body' => 37483784]))
            ->hide(['data.id', 'data.author', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
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

    public function test_it_validates_that_the_slug_is_required_when_updating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make())
            ->hide(['data.id', 'data.author', 'data.slug', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
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

    public function test_it_validates_that_the_slug_must_be_unique_except_for_its_slug_when_updating_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherThread = Thread::factory()->create([
            'user_id' => $user->id,
        ]);

        $resourceObject = ThreadResource::make(Thread::factory()->make(['slug' => $otherThread->slug]))
            ->hide(['data.id', 'data.author', 'data.channel'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
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

        $resourceObject = ThreadResource::make(Thread::factory()->make(['slug' => $thread->slug]))
            ->hide(['data.id', 'data.author'])
            ->response()
            ->getData('true');

        $resourceObject['data']['channel_id'] = $thread->channel_id;

        $this->patchJson(route('threads.update', $thread), $resourceObject)
            ->assertNoContent();

    }


}
