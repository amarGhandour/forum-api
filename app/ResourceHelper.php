<?php

namespace App;

use Illuminate\Http\Resources\Json\JsonResource;

trait ResourceHelper
{

    /**
     * @var array
     */
    protected $withoutFields;

    public function hide(array $fields)
    {
        $this->withoutFields = $fields;
        return $this;
    }

    protected function filterFields(array $data)
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
