<?php

namespace App\Inspections;

interface IInspection
{

    public function detect(string $text);
}
