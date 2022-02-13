<?php

namespace Tests\Unit;

use App\Models\Reply;
use Tests\TestCase;

class ReplyTest extends TestCase
{

    public function test_it_has_an_owner(): void
    {

        $reply = Reply::factory()->create();

        $this->assertInstanceOf('\App\Models\User', $reply->owner);
    }

    public function test_it_belongs_to_a_thread(): void
    {

        $reply = Reply::factory()->create();

        $this->assertInstanceOf('\App\Models\Thread', $reply->thread);
    }
}
