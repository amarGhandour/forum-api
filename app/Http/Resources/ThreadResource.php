<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
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
                'type' => 'threads',
                'id' => $this->id,
                'attributes' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'slug' => $this->slug,
                ]
            ]
        ];
    }
}
