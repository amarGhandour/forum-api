<?php

namespace App\Http\Resources;

use App\ResourceHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
        return $this->filterFields([
            'data' => [
                'id' => $this->id,
                'body' => $this->body,
                'owner' => UserResource::make($this->whenLoaded('owner')),
                'likes_count' => $this->likes_count,
            ],
        ]);
    }

}
