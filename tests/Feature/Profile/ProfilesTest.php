<?php

namespace Tests\Feature\Profile;

use App\Http\Resources\ProfileResource;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_has_a_profile()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Thread::factory(2)->create([
            'user_id' => $user->id,
        ]);

         Thread::factory()->create();

        $this->getJson(route('profile', $user))
            ->assertOk()->assertResource(ProfileResource::make($user));

    }
}
