<?php

namespace App\Http\Resources;

use App\ResourceHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
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
                'title' => $this->title,
                'body' => $this->body,
                'slug' => $this->slug,
                'author' => UserResource::make($this->whenLoaded('author')),
                'replies' => ReplyResource::collection(
                    $this->whenLoaded('replies')
                ),
                'replies_count' => $this->replies_count,
                'channel' => ChannelResource::make($this->whenLoaded('channel')),
            ],
        ]);
    }

}
