<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
{
    /**
     * @var array
     */
    private $withoutFields;

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
                'author' => UserResource::make($this->author),
                'replies' => ReplyResource::collection(
                    $this->whenLoaded('replies')
                ),
            ]
        ]);
    }

    public function hide(array $fields)
    {
        $this->withoutFields = $fields;
        return $this;
    }

    private function filterFields(array $data)
    {
        return collect($data)->except($this->withoutFields)->toArray();
    }

}
