<?php

namespace Tests\Unit;

use App\Models\Thread;
use Tests\TestCase;

class ThreadTest extends TestCase
{

    public function test_it_has_an_author(): void
    {

        $thread = Thread::factory()->create();

        $this->assertInstanceOf('\App\Models\User', $thread->author);
    }
}
