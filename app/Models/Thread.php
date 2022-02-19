<?php

namespace App\Models;

use App\Filters\ThreadFilter;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory, RecordsActivity;

    protected $with = ['replies', 'channel', 'author'];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount', function ($query) {
            $query->withCount('replies');
        });

        static::deleting(function ($thread) {
            $thread->replies->each(function ($reply) {
                $reply->delete();
            });
        });


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

}
