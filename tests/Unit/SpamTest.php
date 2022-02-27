<?php

namespace Tests\Unit;

use App\Inspections\SpamDetector;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SpamTest extends TestCase
{

    public function test_it_validates_spam_keywords()
    {

        $this->expectException(ValidationException::class);

        app(SpamDetector::class)->detect('yahoo customer service');

    }

    public function test_it_validates_spam_held_down_key()
    {

        $this->expectException(ValidationException::class);

        app(SpamDetector::class)->detect('aaaaaaaaaaaaa');

    }

}
