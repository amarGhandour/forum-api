<?php

namespace App\Http\Resources;

use App\ResourceHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    use ResourceHelper;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $typeResource = $this->getTypeResource($this->subject_type);

        return [
            'type' => $this->type,
            'subject_type' => new $typeResource($this->whenLoaded('subject')),
        ];
    }

}
