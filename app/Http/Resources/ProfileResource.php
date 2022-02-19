<?php

namespace App\Http\Resources;

use App\Models\Activity;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'name' => $this->name,
                'email' => $this->email,
                'activities' => ActivityCollection::make(Activity::feed($this->resource))->groupBy(function ($activity) {
                    return $activity->created_at->format('Y-m-d');
                }),
            ]
        ];
    }
}
