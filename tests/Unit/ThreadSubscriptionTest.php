<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\ThreadSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ThreadSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_Thread_can_be_subscribed_to()
    {

        $reply = Reply::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $reply->thread->subscribe();

        $this->assertCount(1, $reply->thread->subscriptions);
    }

    public function test_Thread_can_be_unsubscribed_to()
    {

        $subscription = ThreadSubscription::factory()->create();
        Sanctum::actingAs($subscription->subscriber);

        $this->assertCount(1, $subscription->thread->subscriptions);

        $subscription->thread->unsubscribe();
        $this->assertCount(0, $subscription->thread->fresh()->subscriptions);
    }

    public function test_Thread_can_be_subscribed_once_by_same_user()
    {
        $reply = Reply::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $reply->thread->subscribe();
        $reply->thread->subscribe();
        $this->assertCount(1, $reply->thread->subscriptions);

    }

    public function test_it_belongs_to_a_thread()
    {

        $subscription = ThreadSubscription::factory()->create();

        $this->assertInstanceOf('App\Models\Thread', $subscription->thread);
    }

    public function test_it_belongs_to_a_user()
    {

        $subscription = ThreadSubscription::factory()->create();

        $this->assertInstanceOf('App\Models\User', $subscription->subscriber);
    }
}
