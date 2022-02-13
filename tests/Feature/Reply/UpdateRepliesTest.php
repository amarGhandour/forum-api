<?php

namespace Tests\Feature\Reply;

use App\Http\Resources\ReplyResource;
use App\Http\Resources\UserResource;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateRepliesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthorized_user_can_not_update_a_reply()
    {

        $reply = Reply::factory()->create();

        $resourceObject = ReplyResource::make($reply)->response()->getData(true);

        $this->patchJson(route('replies.update', $reply), $resourceObject)
            ->assertUnauthorized();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->patchJson(route('replies.update', $reply), $resourceObject)
            ->assertUnauthorized();

    }

    public function test_an_authorized_user_may_update_a_reply(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reply = Reply::factory()->create([
            'user_id' => $user->id
        ]);

        $resourceObject = [
            'data' => [
                'id' => $reply->id,
                'body' => 'this is reply',
                'owner' => UserResource::make($reply->owner)->response()->getData(),
            ],
        ];

        $this->patchJson(route('replies.update', $reply), $resourceObject)
            ->assertNoContent();
    }


    public function test_it_validated_that_the_identifier_field_is_given_when_updating_a_reply()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $resourceObject = ReplyResource::make($reply)
            ->hide(['data.id'])
            ->response()
            ->getData(true);

        $this->patchJson(route('replies.update', $reply), $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.id field is required.',
                        'source' => [
                            'pointer' => '/data/id',
                        ]
                    ]
                ]
            ]);;

        $this->assertDatabaseMissing('replies', [
            'id' => null,
            'body' => $reply->body,
        ]);

    }

    public function test_it_validated_that_the_body_field_is_given_when_updating_a_reply()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $resourceObject = ReplyResource::make($reply)
            ->hide(['data.body'])
            ->response()
            ->getData(true);

        $this->patchJson(route('replies.update', $reply), $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.body field is required.',
                        'source' => [
                            'pointer' => '/data/body',
                        ]
                    ]
                ]
            ]);;

        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id,
            'body' => null,
        ]);

    }

    public function test_it_validated_that_the_body_field_is_string_when_updating_a_reply()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        $this->patchJson(route('replies.update', $reply), [
            'data' => [
                'id' => $reply->id,
                'body' => 347837483,
            ]
        ])
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.body must be a string.',
                        'source' => [
                            'pointer' => '/data/body',
                        ]
                    ]
                ]
            ]);;

        $this->assertDatabaseMissing('replies', [
            'body' => 347837483,
        ]);

    }

}
