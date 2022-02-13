<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use Tests\TestCase;

class ThreadTest extends TestCase
{

    public function test_it_has_an_author(): void
    {

        $thread = Thread::factory()->create();

        $this->assertInstanceOf('\App\Models\User', $thread->author);
    }

    public function test_it_has_a_replies(): void
    {
        $this->withoutExceptionHandling();

        $thread = Thread::factory()->create();

        Reply::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertCount(1, $thread->replies);
    }

}