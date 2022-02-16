<?php

namespace Database\Factories;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $likeable = $this->likeable();

        return [
            'user_id' => User::factory(),
            'likeable_id' => $likeable::factory(),
            'likeable_type' => $likeable,
        ];
    }

    private function likeable()
    {
        return $this->faker->randomElement([
            Reply::class,
        ]);
    }
}
