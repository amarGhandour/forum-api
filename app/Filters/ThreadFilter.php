<?php

namespace App\Filters;

class ThreadFilter extends Filters
{

    protected $filters = ['by', 'popular', 'unanswered'];

    protected function by($by)
    {
        $this->query->wherehas('author', function ($query) use ($by) {
            $query->where('name', $by);
        });
    }

    protected function popular()
    {
        $this->query->orderBy('replies_count', 'DESC');
    }

    protected function unanswered()
    {
        $this->query->where('replies_count', 0);
    }

}
