<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\ThreadSubscription;
use App\Models\User;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_an_author(): void
    {

        $thread = Thread::factory()->create();

        $this->assertInstanceOf('\App\Models\User', $thread->author);
    }

    public function test_it_has_a_replies(): void
    {
        $thread = Thread::factory()->create();

        Reply::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertCount(1, $thread->replies);
    }

    public function test_it_belongs_to_a_channel(): void
    {

        $thread = Thread::factory()->create();

        $this->assertInstanceOf('\App\Models\Channel', $thread->channel);
    }

    public function test_it_has_feeds()
    {
        $thread = Thread::factory()->create();
        $this->assertCount(1, $thread->activity);

    }

    public function test_it_has_path()
    {

        $thread = Thread::factory()->create();

        $this->assertEquals(asset("api/v1/threads/{$thread->channel->slug}/$thread->slug"), $thread->path());
    }

    public function test_a_thread_can_add_a_reply_to()
    {

        $thread = Thread::factory()->create();

        $this->assertCount(0, $thread->replies);

        $thread->addReply(Reply::factory()->make()->toArray());

        $this->assertCount(1, $thread->fresh()->replies);
    }

    public function test_a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {

        Notification::fake();

        $subscription = ThreadSubscription::factory()->create();

        $subscription->thread->addReply(Reply::factory()->make()->toArray());

        Notification::assertSentTo($subscription->subscriber, ThreadWasUpdated::class);

    }


    public function test_a_thread_checks_if_authenticated_user_has_read_all_replies()
    {

        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create();

        $this->assertTrue($thread->hasUpdatesFor($user));

        $user->read($thread);

        $this->assertFalse($thread->hasUpdatesFor($user));

    }

    public function test_it_knows_if_thread_just_published()
    {

        $thread = Thread::factory()->create();

        $this->assertTrue($thread->wasJustPublished());

        $thread = Thread::factory()->create([
            'created_at' => now()->subMinute(),
        ]);

        $this->assertFalse($thread->wasJustPublished());
    }


}
