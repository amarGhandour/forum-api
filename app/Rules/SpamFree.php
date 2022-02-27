<?php

namespace App\Rules;

use App\Inspections\SpamDetector;
use Illuminate\Contracts\Validation\Rule;

class SpamFree implements Rule
{

    public function passes($attribute, $value)
    {
        try {
            return !app(SpamDetector::class)->detect($value);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field contains spam.';
    }
}
