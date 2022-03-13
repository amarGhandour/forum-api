<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_created_by_a_user()
    {

        $user = User::factory()->create();

        Thread::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->threads);

        $this->assertCount(1, $user->threads);
    }

    public function test_it_has_activity()
    {

        $user = User::factory()->create();

        Thread::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertCount(1, $user->activity);

    }

    public function test_user_can_fetch_most_recent_reply()
    {

        $reply = Reply::factory()->create([
            'user_id' => $user = User::factory()->create(),
            'created_at' => Carbon::now()->addMinute(),
        ]);

        Reply::factory()->create([
            'user_id' => $user,
        ]);

        $this->assertEquals($reply->created_at, $user->lastReply->created_at);

    }

    public function test_user_can_fetch_most_recent_thread()
    {

        $thread = Thread::factory()->create([
            'user_id' => $user = User::factory()->create(),
            'created_at' => Carbon::now()->addMinute(),
        ]);

        Thread::factory()->create([
            'user_id' => $user,
        ]);

        $this->assertEquals($thread->created_at, $user->lastThread->created_at);

    }

    public function test_it_returns_full_avatar_path()
    {

        $user = User::factory()->create();
        $this->assertEquals(asset('avatars/default.jpg'), $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';
        $this->assertEquals(asset('avatars/me.jpg'), $user->avatar_path);
    }
}
