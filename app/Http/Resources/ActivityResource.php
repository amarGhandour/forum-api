<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $type = $this->getTypeResource();

        return [
            'type' => $this->type,
            'subject_type' => new $type($this->whenLoaded('subject')),
        ];
    }

    private function getTypeResource(): string
    {
        $subject = (new \ReflectionClass($this->subject_type))->getShortName();
        $type = '\App\Http\Resources\\' . $subject . 'Resource';
        return $type;
    }
}
