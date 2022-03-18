<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'admin' => 'boolean',
    ];

    public function getAvatarPathAttribute($key)
    {

        return asset($this->attributes['avatar_path'] ?? 'avatars/default.jpg');
    }

    public function threads()
    {
        return $this->hasMany(Thread::class, 'user_id', 'id');
    }

    public function activity()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    public function read(Thread $thread)
    {
        cache()->forever($this->visitedThreadCacheKey($thread), Carbon::now());
    }

    public function visitedThreadCacheKey(Thread $thread): string
    {
        return sprintf('users.%s.visits.%s', $this->id, $thread->id);
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function lastThread()
    {
        return $this->hasOne(Thread::class, 'user_id')->latest();
    }


}
