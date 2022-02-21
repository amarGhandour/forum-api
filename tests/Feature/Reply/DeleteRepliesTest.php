<?php

namespace Tests\Feature\Reply;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteRepliesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthorized_user_can_not_delete_a_reply()
    {

        $reply = Reply::factory()->create();

        $this->deleteJson(route('replies.destroy', $reply))->assertUnauthorized();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->deleteJson(route('replies.destroy', $reply))->assertForbidden();

    }


    public function test_an_authorized_user_may_delete_a_reply()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reply = Reply::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->deleteJson(route('replies.destroy', $reply))->assertNoContent();

        $this->assertEquals(0, $reply->thread->fresh()->replies_count);

    }

}
