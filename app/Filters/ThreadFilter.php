<?php

namespace App\Filters;

class ThreadFilter extends Filters
{

    protected $filters = ['by'];

    protected function by($by)
    {
        $this->query->wherehas('author', function ($query) use ($by) {
            $query->where('name', $by);
        });
    }

}
