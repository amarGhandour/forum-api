<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{

    protected $request;
    protected $query;
    protected $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($query)
    {
        $this->query = $query;

        foreach ($this->getFilters() as $filter => $value) {
            $this->$filter($value);
        }

        return $query;
    }

    /**
     * @return array
     */
    protected function getFilters(): array
    {
        return $this->request->only($this->filters);
    }
}
