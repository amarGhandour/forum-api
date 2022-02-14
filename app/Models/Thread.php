<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $with = ['replies'];

    protected $guarded = [];

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
        return $this->hasMany(Reply::class, 'thread_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies()->delete();
        });
    }

}
