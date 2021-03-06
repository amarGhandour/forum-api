<?php

namespace Tests\Feature\Thread;

use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
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

        Sanctum::actingAs($user = User::factory()->create());

        $thread = Thread::factory()->create();

        Reply::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertTrue($thread->hasUpdatesFor($user));

        $response = $this->getJson(route('threads.show', [$thread->channel, $thread]))->assertOk();

        $thread = Thread::where('id', $thread->id)->first();
        $resource = ThreadResource::make($thread);

        $response->assertResource($resource);

        $this->assertFalse($thread->hasUpdatesFor($user));

    }

    public function test_user_can_fetches_replies()
    {

        $reply = Reply::factory()->create();

        $this->getJson(route('replies.index', [$reply->thread->channel, $reply->thread]))
            ->assertOk()
            ->assertJsonCount(1, 'data');
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

    public function test_user_can_sort_threads_by_author()
    {

        $user = User::factory()->create();

        Thread::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $threads = Thread::where('user_id', $user->id)->get();

        Thread::factory(3)->create();

        $this->getJson("api/v1/threads?by=$user->name")
            ->assertOk()
            ->assertExactResource(new ThreadCollection($threads));

    }

    public function test_user_can_sort_threads_by_popularity()
    {

        collect(Thread::factory(3)->create())->map(function ($thread) use (&$count) {
            Reply::factory()->count($count--)->create([
                'thread_id' => $thread->id,
            ]);
        });

        $threads = Thread::orderBy('replies_count', 'DESC')->get();

        $this->getJson("api/v1/threads?popular=1")
            ->assertOk()
            ->assertExactResource(new ThreadCollection($threads));

    }

    public function test_user_can_filter_thread_by_unanswered()
    {

        Reply::factory()->create();

        Thread::factory()->create();

        $response = $this->getJson('api/v1/threads?unanswered=1')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));

    }

    public function test_it_record_new_visit_each_time_the_thread_is_read()
    {
        $thread = Thread::factory()->create();

        $this->assertSame(0, $thread->visits);

        $this->getJson(route('threads.show', [$thread->channel, $thread]));

        $this->assertEquals(1, $thread->fresh()->visits);

    }

}
