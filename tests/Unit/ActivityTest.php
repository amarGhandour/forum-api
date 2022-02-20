<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_belongs_to_a_subject()
    {

        $activity = Activity::factory()->create([
            'subject_id' => Thread::factory(),
            'subject_type' => Thread::class
        ]);

        $this->assertInstanceOf('App\Models\Thread', $activity->subject);
    }

    public function test_it_records_activity_when_a_thread_is_created()
    {

        $thread = Thread::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => $thread->user_id,
            'subject_id' => $thread->id,
            'subject_type' => get_class($thread),
        ]);

    }

    public function test_it_records_activity_when_a_reply_is_created()
    {

        Reply::factory()->create();

        $this->assertCount(2, Activity::all());

    }

    public function test_it_fetches_all_feed_for_any_user()
    {

        $user = User::factory()->create();

        Thread::factory()->create([
            'user_id' => $user,
        ]);

        $this->assertCount(1, Activity::feed($user));

    }


    public function test_it_delete_thread_activity_when_deleting_a_thread()
    {

        $thread = Thread::factory()->create();

        $thread->delete();

        $this->assertCount(0, $thread->activity);

    }

    public function test_it_delete_like_activity_when_unlike_a_reply()
    {

        $reply = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reply->like();

        $reply->unlike();

        $this->assertCount(0, Activity::feed($user));

    }

    public function test_it_delete_reply_activity_when_deleting_a_reply()
    {

        $reply = Reply::factory()->create();

        $reply->delete();

        $this->assertCount(0, $reply->activity);

    }

    public function test_it_delete_reply_and_likes_activities_when_deleting_a_reply()
    {

        $reply = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reply->like();

        $reply->delete();

        $this->assertCount(0, $reply->activity);
        $this->assertCount(0, Activity::feed($user));
    }

    public function test_it_delete_thread_and_replies_and_likes_activities_when_deleting_a_thread()
    {

        $reply = Reply::factory()->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reply->like();

        $reply->thread->delete();

        $this->assertCount(0, $reply->activity);
        $this->assertCount(0, Activity::feed($user));
        $this->assertCount(0, $reply->thread->activity);

    }

}
