<?php

namespace App\Models;

use App\Events\ThreadReceivedNewReply;
use App\Filters\ThreadFilter;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory, RecordsActivity;

    protected $with = ['channel', 'author'];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each(function ($reply) {
                $reply->delete();
            });
        });
    }

    public function addReply(array $attributes)
    {
        $reply = $this->replies()->create($attributes);

        event(new ThreadReceivedNewReply($reply));

        $this->notifySubscribers($reply);

        return $reply;
    }

    public function subscribe()
    {
        if ((auth()->user() ?? false) && !$this->subscriptions()->where(['user_id' => auth()->id()])->exists())
            return $this->subscriptions()->create(['user_id' => auth()->id()]);
    }

    public function unsubscribe()
    {
        if (auth()->user() ?? false)
            $this->subscriptions()->where(['user_id' => auth()->id()])->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class, 'thread_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'thread_id', 'id')->withCount('likes');
    }

    public function scopeFilter($query, ThreadFilter $filters)
    {
        return $filters->apply($query);
    }

    public function notifySubscribers($reply): void
    {
        $this->subscriptions->where('user_id', '!=', $reply->user_id)->each->notify($reply);
    }

    public function hasUpdatesFor(User $user)
    {

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(now()->subMinute());
    }

}
