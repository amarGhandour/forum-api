<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function feed(User $user, $take = 50)
    {
        return static::where('user_id', $user->id)->latest()->take($take)->with(['subject' => function (MorphTo $morphTo) {
            $morphTo->constrain([
                Thread::class => function (Builder $query) {
                    $query->withoutGlobalScope('replyCount')->setEagerLoads([]);
                },
                Reply::class => function (Builder $query) {
                    $query->setEagerLoads([]);
                }
            ]);
        }])->get();
    }

    public function subject()
    {
        return $this->morphTo();
    }

}
