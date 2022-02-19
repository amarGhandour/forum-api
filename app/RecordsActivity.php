<?php

namespace App;

use App\Models\Activity;

trait RecordsActivity
{

    protected static function bootRecordsActivity()
    {
        foreach (static::getActivitiesEvent() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function ($model) {
            $model->activity()->delete();
        });

    }

    protected function recordActivity($event)
    {
        $this->activity()->create([
            'type' => $this->getActivityType($event),
            'user_id' => $this->user_id,
        ]);
    }

    protected function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected function getActivityType($event): string
    {
        $type = lcfirst((new \ReflectionClass($this))->getShortName());
        return "{$event}_$type";
    }

    protected static function getActivitiesEvent()
    {
        return ['created'];
    }
}
