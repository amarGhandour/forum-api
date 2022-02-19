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
                'author' => UserResource::make($this->whenLoaded('author')),
                'replies' => ReplyResource::collection(
                    $this->whenLoaded('replies')
                ),
                'replies_count' => $this->replies_count,
                'channel' => ChannelResource::make($this->whenLoaded('channel')),
            ],
        ]);
    }

    public function hide(array $fields)
    {
        $this->withoutFields = $fields;
        return $this;
    }

    private function filterFields(array $data)
    {
        $result = collect($data)->except($this->withoutFields)->toArray();
        return $this->removeNullValues($result['data']);
    }

    public function removeNullValues(array $data)
    {
        $filtered_data = [];
        foreach ($data as $key => $value) {
            // if resource is empty
            if ($value instanceof JsonResource and $value->resource === null) {
                continue;
            }
            $filtered_data['data'][$key] = $this->when($value !== null, $value);
        }

        return $filtered_data;
    }

}
