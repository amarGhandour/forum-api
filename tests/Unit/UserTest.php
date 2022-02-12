<?php

namespace Tests\Unit;

use App\Models\Thread;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_it_created_by_a_user()
    {

        $user = User::factory()->create();

        Thread::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->threads);

        $this->assertCount(1, $user->threads);
    }
}
