<?php

namespace Tests\Feature;

use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_retrieve_threads()
    {

        $this->withoutExceptionHandling();

        $threads = Thread::factory(2)->create();

        $resources = new ThreadCollection($threads);

        $response = $this->getJson("api/threads");

        $response->assertOk()
            ->assertResource($resources);
    }

    public function test_user_can_retrieve_specific_thread()
    {

        $thread = Thread::factory()->create();

        $resource = ThreadResource::make($thread);

        $this->getJson(route('threads.show', $thread))
            ->assertOk()->assertResource($resource);

    }


}
