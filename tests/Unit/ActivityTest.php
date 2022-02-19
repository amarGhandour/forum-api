<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
   use RefreshDatabase;

    public function test_it_belongs_to_a_subject()
    {

        $activity = Activity::factory()->create([
            'subject_id' => Thread::factory(),
            'subject_type' => Thread::class
        ]);

        $this->assertInstanceOf('App\Models\Thread', $activity->subject);
    }

    public function test_it_fetches_all_feed_for_any_user()
    {

        $user = User::factory()->create();

        Thread::factory()->create([
            'user_id' => $user,
        ]);

        $this->assertCount(1, Activity::feed($user));

    }


}
