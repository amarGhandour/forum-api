<?php

namespace App\Inspections;

use Illuminate\Validation\ValidationException;

class KeyHeldDown implements IInspection
{

    public function detect(string $text)
    {
        if (preg_match('/(.)\\1{4,}/', $text))
            throw ValidationException::withMessages(['body' => ['body contains spam.']]);
    }
}
