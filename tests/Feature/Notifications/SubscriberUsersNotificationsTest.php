<?php

namespace Tests\Feature\Notifications;

use App\Models\Reply;
use App\Models\ThreadSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriberUsersNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_not_by_current_user()
    {

        $subscription = ThreadSubscription::factory()->create();

        $subscriber = $subscription->subscriber;

        $subscription->thread->addReply(Reply::factory()->make(['user_id' => $subscriber->id])->toArray());

        $this->assertCount(0, $subscriber->notifications);

        $subscription->thread->addReply(Reply::factory()->make()->toArray());

        $this->assertCount(1, $subscriber->fresh()->notifications);

    }

    public function test_an_authorized_user_can_mark_notification_as_read()
    {

        $subscription = ThreadSubscription::factory()->create();

        $subscriber = $subscription->subscriber;
        Sanctum::actingAs($subscriber);

        $subscription->thread->addReply(Reply::factory()->make()->toArray());

        $this->assertCount(1, $subscriber->unreadNotifications);

        $notificationId = $subscriber->unreadNotifications->first()->id;

        $this->deleteJson(route('thread-notifications.destroy', [$subscriber, $notificationId]))->assertNoContent();

        $this->assertCount(0, $subscriber->fresh()->unreadNotifications);


    }

    public function test_a_user_can_fetch_their_unread_notifications()
    {

        $subscription = ThreadSubscription::factory()->create();

        $subscriber = $subscription->subscriber;
        Sanctum::actingAs($subscriber);

        $subscription->thread->addReply(Reply::factory()->make()->toArray());
        $subscription->thread->addReply(Reply::factory()->make()->toArray());

        $subscriber->unreadNotifications()->first()->markAsRead();

        $this->getJson(route('thread-notifications.index', $subscriber))->assertOk()->assertJsonCount(1);

    }
}
