<?php

namespace App\Inspections;

use Illuminate\Validation\ValidationException;

class InvalidKeywords implements IInspection
{
    protected $invalidKeywords = ['Yahoo Customer Service'];

    public function detect(string $text)
    {
        foreach ($this->invalidKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                throw ValidationException::withMessages(['body' => ['body contains spam.']]);
            }
        }

    }
}
