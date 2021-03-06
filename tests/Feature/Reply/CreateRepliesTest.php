<?php

namespace Tests\Feature\Reply;

use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateRepliesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthenticated_user_can_not_add_a_reply_to_a_thread()
    {

        $thread = Thread::factory()->create();

        $this->postJson(route('replies.store', $thread))->assertUnauthorized();

    }

    public function test_an_authenticated_user_may_add_a_reply_to_a_thread(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make([
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $resourceObject = ReplyResource::make($reply)->hide(['id', 'owner'])->response()->getData(true);

        $response = $this->postJson(route('replies.store', $thread), $resourceObject)
            ->assertCreated();

        $this->assertDatabaseHas('replies', $reply->only(['body']));

        $replyCreated = Reply::where('body', $reply->body)->first();

        $response->assertResource(ReplyResource::make($replyCreated));


    }

    public function test_replies_that_contain_spam_not_be_created(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Yahoo Customer Service'
        ]);

        $resourceObject = ReplyResource::make($reply)->hide(['id', 'owner'])->response()->getData(true);

        $this->postJson(route('replies.store', $thread), $resourceObject)
            ->assertStatus(422)->assertJson([
                'errors' => [
                    [
                        'title' => 'Validation Error',
                        'details' => 'The data.body field contains spam.',
                        'source' => [
                            'pointer' => '/data/body',
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('replies', $reply->only(['body']));

    }

    public function test_user_can_only_reply_a_maximum_of_once_per_minute()
    {

        Sanctum::actingAs(User::factory()->create());

        $thread = Thread::factory()->create();

        $this->postJson(route('replies.store', $thread), [
            'data' => [
                'body' => 'this is reply',
            ]
        ])->assertCreated();

        $this->postJson(route('replies.store', $thread), [
            'data' => [
                'body' => 'this is reply',
            ]
        ])->assertStatus(429);

    }

    public function test_it_validated_that_the_body_field_is_given_when_creating_a_reply()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();
        $reply = Reply::factory()->make([
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $resourceObject = ReplyResource::make($reply)
            ->hide(['data.body', 'data.id'])
            ->response()
            ->getData(true);

        $this->postJson(route('replies.store', $thread), $resourceObject)
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

        $this->assertDatabaseMissing('replies', $reply->only(['body']));

    }


}
