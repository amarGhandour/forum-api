<?php

namespace App\Inspections;

class SpamDetector implements IInspection
{

    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class
    ];

    public function detect(string $text)
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($text);
        }
        return false;
    }

}
