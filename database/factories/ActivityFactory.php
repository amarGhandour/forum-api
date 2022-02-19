<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $subject = $this->subject();
        return [
            'user_id' => User::factory(),
            'subject_id' => $subject::factory(),
            'subject_type' => $subject,
            'type' => $this->type($subject),
        ];
    }

    private function subject()
    {
        return $this->faker->randomElement([
            Thread::class,
        ]);
    }

    private function type($subject)
    {
        $type = lcfirst((new \ReflectionClass($subject))->getShortName());
        $event = $this->faker->randomElement([
            'created',
            'deleted',
            'updated',
        ]);

        return "{$event}_$type";
    }

}
