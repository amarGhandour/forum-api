<?php

namespace App\Models;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notify(Reply $reply)
    {
        $this->subscriber->notify(new ThreadWasUpdated($this->thread, $reply));
    }
}
