<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory, RecordsActivity;

    protected $with = ['owner', 'likes'];

    protected $guarded = [];

    public function path()
    {
        return route('threads.show', [$this->thread->channel, $this->thread]) . "#reply-$this->id";
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {

        $attributes = ['user_id' => auth()->id()];

        if (!$this->likes()->where($attributes)->exists()) {
            $this->likes()->create($attributes);
        }
    }

    public function unlike()
    {
        $attributes = ['user_id' => auth()->id()];

        if ($this->likes()->where($attributes)->exists()) {
            $this->likes()->where($attributes)->get()->each->delete();
        }
    }

    public function isLiked()
    {
        return !!$this->likes->where('user_id', auth()->id())->count();
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(now()->subMinute());
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($reply) {
            $reply->likes->each->delete();
        });

        static::created(function ($reply) {
            $reply->thread()->increment('replies_count');
        });

        static::deleted(function ($reply) {
            $reply->thread()->decrement('replies_count');
        });
    }

    public function mentionedUsers(){

        preg_match_all('/@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest()
    {
        return $this->id === $this->thread->best_reply_id;
    }

}
