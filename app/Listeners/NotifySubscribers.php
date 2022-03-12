<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Models\Reply;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySubscribers
{


    /**
     * Handle the event.
     *
     * @param  \App\Events\ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {

        $event->reply->thread->subscriptions->where('user_id', '!=', $event->reply->user_id)->each->notify($event->reply);

    }
}
