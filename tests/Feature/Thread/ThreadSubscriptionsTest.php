<?php

namespace Tests\Feature\Thread;

use App\Models\Thread;
use App\Models\ThreadSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ThreadSubscriptionsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_user_can_subscribe_to_a_thread()
    {

        $thread = Thread::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('threads.show', [$thread->channel, $thread]) . '/subscriptions')
            ->assertCreated();

        $this->assertCount(1, $thread->subscriptions);
    }

    public function test_a_user_can_unsubscribe_to_a_thread()
    {

        $subscription = ThreadSubscription::factory()->create();
        $thread = $subscription->thread;

        Sanctum::actingAs($subscription->subscriber);

        $this->assertCount(1, $thread->subscriptions);

        $this->delete(route('threads.show', [$thread->channel, $thread]) . "/subscriptions")
            ->assertNoContent();

        $this->assertCount(0, $thread->fresh()->subscriptions);
    }
}
