<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Models\User;
use App\Notifications\YouWereMentioned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyMentionedUsers
{

    /**
     * Handle the event.
     *
     * @param \App\Events\ThreadReceivedNewReply $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {

        User::whereIn('name', $event->reply->mentionedUsers())->get()
        ->each->notify(new YouWereMentioned($event->reply));

    }
}
