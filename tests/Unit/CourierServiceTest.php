<?php

namespace Tests\Unit;

use App\Models\Courier;
use App\Models\User;
use App\Models\Vote;
use App\Services\CourierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\IncreaseViewsJob;
use Tests\TestCase;
use Illuminate\Http\Request;

class CourierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $courierService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courierService = new CourierService();
    }

    public function test_list_couriers_returns_paginated_data()
    {
        Courier::factory()->count(15)->create();

        $request = new Request([
            'search' => '',
            'sort' => 'created_at',
            'direction' => 'asc',
            'page' => 1,
        ]);

        $result = $this->courierService->listCouriers($request);

        $this->assertNotEmpty($result);
        $this->assertEquals(10, $result->perPage());
    }

    public function test_vote_creates_or_updates_vote()
    {
        $user = User::factory()->create();
        $courier = Courier::factory()->create();

        $vote = $this->courierService->vote($user->id, $courier->id, 'like');

        $this->assertInstanceOf(Vote::class, $vote);
        $this->assertEquals('like', $vote->type);

        $updatedVote = $this->courierService->vote($user->id, $courier->id, 'dislike');
        $this->assertEquals('dislike', $updatedVote->type);
    }

    public function test_remove_vote_deletes_user_vote()
    {
        $user = User::factory()->create();
        $courier = Courier::factory()->create();

        Vote::create([
            'user_id' => $user->id,
            'courier_id' => $courier->id,
            'type' => 'like',
        ]);

        $deleted = $this->courierService->removeVote($user->id, $courier->id);

        $this->assertEquals(1, $deleted);
        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'courier_id' => $courier->id,
        ]);
    }

    public function test_get_courier_details_returns_data_with_like_dislike_counts()
    {
        $courier = Courier::factory()->create();

        Cache::forget("Courier:{$courier->id}");

        $result = $this->courierService->getCourierDetails($courier);

        $this->assertEquals($courier->id, $result->id);
        $this->assertTrue(isset($result->likes));
        $this->assertTrue(isset($result->dislikes));
    }

    public function test_increase_views_dispatches_job()
    {
        Queue::fake();

        $courier = Courier::factory()->create();

        $this->courierService->increaseViews($courier);

        Queue::assertPushed(IncreaseViewsJob::class);
    }
}
