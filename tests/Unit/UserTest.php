<?php

namespace Tests\Unit;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

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

}
