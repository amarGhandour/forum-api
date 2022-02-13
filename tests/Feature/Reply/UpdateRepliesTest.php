<?php

namespace Tests\Feature\Reply;

use App\Http\Resources\UserResource;
use App\Models\Reply;
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

        $this->patchJson(route('replies.update', $reply), [])
            ->assertUnauthorized();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->patchJson(route('replies.update', $reply), [])
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
                'type' => 'replies',
                'id' => $reply->id,
                'attributes' => [
                    'body' => 'this is reply',
                    'owner' => UserResource::make($reply->owner)->response()->getData(),
                ]
            ]
        ];

        $this->patchJson(route('replies.update', $reply), $resourceObject)
            ->assertNoContent();
    }
}
