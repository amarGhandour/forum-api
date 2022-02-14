<?php

namespace Tests\Feature\Thread;

use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_retrieve_threads()
    {

        $threads = Thread::factory(2)->create();

        $resources = new ThreadCollection($threads);

        $response = $this->getJson(route('threads.index'));

        $response->assertOk()
            ->assertResource($resources);
    }

    public function test_user_can_retrieve_specific_thread()
    {

        $thread = Thread::factory()->create();

        $resource = ThreadResource::make($thread);

        $this->getJson(route('threads.show', [$thread->channel, $thread]))
            ->assertOk()->assertResource($resource);

    }

    public function test_user_can_retrieve_replies_that_associated_with_a_thread(): void
    {

        $reply = Reply::factory()->create();

        $resourceObject = ThreadResource::make($reply->thread);

        $this->getJson(route('threads.show', [$reply->thread->channel, $reply->thread]))
            ->assertOk()
            ->assertResource($resourceObject);

    }

    public function test_user_can_sort_threads_by_channel()
    {

        $channel = Channel::factory()->create();

        $threads = Thread::factory(3)->create([
            'channel_id' => $channel->id
        ]);

        Thread::factory(3)->create();

        $this->getJson("api/v1/threads/$channel->slug")
            ->assertOk()
            ->assertResource(ThreadCollection::make($threads));
    }


}
