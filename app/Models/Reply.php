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

        if (!$this->likes()->where('user_id', auth()->id())->exists()) {
            $this->likes()->create($attributes);
        }
    }

    public function isLiked()
    {
        return !!$this->likes->where('user_id', auth()->id())->count();
    }

}
