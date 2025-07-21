<?php

namespace Tests\Feature;

use App\Models\Travel;
use App\Models\User;
use App\TravelStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_travel_request(): void
    {
        $user = User::factory()->create();

        $data = [
            'requester_name' => 'João da Silva',
            'destination' => 'Lisboa',
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
        ];

        $this->actingAs($user);

        $response = $this->postJson('/api/travels', $data);

        $response->assertCreated()
            ->assertJsonFragment(['destination' => 'Lisboa']);

        $this->assertDatabaseHas('travel', [
            'destination' => 'Lisboa',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_list_own_travel_requests(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Travel::factory()->create(['user_id' => $user->id, 'destination' => 'Paris']);
        Travel::factory()->create(['user_id' => $other->id, 'destination' => 'Londres']);

        $this->actingAs($user);

        $response = $this->getJson('/api/travels');

        $response->assertOk()
            ->assertJsonFragment(['destination' => 'Paris'])
            ->assertJsonMissing(['destination' => 'Londres']);
    }

    public function test_filter_travel_requests_by_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Travel::factory()->create(['user_id' => $user->id, 'status' => TravelStatusEnum::REQUESTED]);
        Travel::factory()->create(['user_id' => $user->id, 'status' => TravelStatusEnum::APPROVED]);

        $response = $this->getJson('/api/travels?status=approved');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('approved', $response->json('data')[0]['status']);
    }

    public function test_travel_request_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/travels', [
            'requester_name' => '',
            'destination' => '',
            'departure_date' => 'invalid-date',
            'return_date' => '2025-01-01',
        ]);

        $response->assertStatus(422);
    }
}
