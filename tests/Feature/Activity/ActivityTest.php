<?php

namespace Tests\Feature\Feeds;

use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
{
   use RefreshDatabase, WithFaker;

    public function test_it_records_activity_when_a_thread_is_created()
    {

        $thread = Thread::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => $thread->user_id,
            'subject_id' => $thread->id,
            'subject_type' => get_class($thread),
        ]);

    }

    public function test_it_records_activity_when_a_reply_is_created()
    {

        Reply::factory()->create();

        $this->assertCount(2, Activity::all());

    }
}
