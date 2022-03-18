<?php

namespace App\Models;

use App\Events\ThreadReceivedNewReply;
use App\Filters\ThreadFilter;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });

    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function path()
    {
        return route('threads.show', [$this->channel, $this]);
    }

    public function addReply(array $attributes)
    {
        $reply = $this->replies()->create($attributes);

        event(new ThreadReceivedNewReply($reply));

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

    public function hasUpdatesFor(User $user)
    {

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(now()->subMinute());
    }

    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);

        if (static::where('slug', $slug)->exists()) {
            $slug = "$slug-$this->id";
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

}
