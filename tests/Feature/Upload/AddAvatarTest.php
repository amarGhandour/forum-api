<?php

namespace Tests\Feature\Upload;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_only_authorized_members_can_add_avatar_photo()
    {

        $this->postJson('api/v1/users/1/avatar')->assertUnauthorized();

        $authorizedUser = User::factory()->create();

        $NotAuthorizedUser = User::factory()->create();
        Sanctum::actingAs($NotAuthorizedUser);

        Storage::fake('public');

        $this->postJson("api/v1/users/$authorizedUser->id/avatar",
            [
                'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
            ])->assertForbidden();

        Storage::disk('public')->assertMissing('avatars/' . $file->hashName());

    }

    public function test_a_valid_avatar_must_be_provided()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson("api/v1/users/$user->id/avatar", ['avatar' => 'not an image.'])
            ->assertStatus(422);

    }

    public function test_a_user_may_add_avatar_to_his_profile()
    {


        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Storage::fake('public');

        $this->postJson("api/v1/users/$user->id/avatar",
            [
                'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
            ])->assertNoContent();

        $this->assertEquals(asset('avatars/' . $file->hashName()), $user->avatar_path);

        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }

}
