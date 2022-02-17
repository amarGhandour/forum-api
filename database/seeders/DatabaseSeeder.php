<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        Thread::factory(30)->create()->each(function ($thread) {
            Reply::factory(5)->create([
                'thread_id' => $thread->id,
            ])->each(function ($reply) {
                Like::factory(5)->create([
                    'likeable_id' => $reply->id,
                ]);
            });
        });

    }
}
