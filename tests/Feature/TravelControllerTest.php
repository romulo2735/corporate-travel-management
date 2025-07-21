<?php

namespace Tests\Feature;

use App\Models\Travel;
use App\Models\User;
use App\TravelStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_travels_with_filters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Travel::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Paris',
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
            'status' => TravelStatusEnum::APPROVED,
        ]);

        Travel::factory()->create([
            'user_id' => $user->id,
            'destination' => 'Londres',
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
            'status' => TravelStatusEnum::REQUESTED,
        ]);

        $response = $this->getJson('/api/travels?status=approved&destination=Paris');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Paris', $response->json('data')[0]['destination']);
    }

    public function test_user_can_view_own_travel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travel = Travel::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/travels/{$travel->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $travel->id]);
    }

    public function test_user_cannot_view_others_travel(): void
    {
        $this->actingAs(User::factory()->create());

        $other = User::factory()->create();

        $travel = Travel::factory()->create(['user_id' => $other->id]);

        $response = $this->getJson("/api/travels/{$travel->id}");

        $response->assertForbidden();
    }

    public function test_user_can_create_travel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'requester_name' => 'Ana',
            'destination' => 'Lisboa',
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
        ];

        $response = $this->postJson('/api/travels', $data);

        $response->assertCreated()
            ->assertJsonFragment(['destination' => 'Lisboa']);

        $this->assertDatabaseHas('travel', ['destination' => 'Lisboa']);
    }

    public function test_user_can_update_status_of_travel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'status' => TravelStatusEnum::REQUESTED,
            'user_id' => $user->id,
        ]);

        $response = $this->patchJson("/api/travels/{$travel->id}/status", [
            'status' => 'approved',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['status' => 'approved']);
    }

    public function test_user_can_cancel_own_approved_travel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'user_id' => $user->id,
            'status' => TravelStatusEnum::APPROVED,
        ]);

        $response = $this->postJson('/api/travels/cancel', [
            'travel_id' => $travel->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['status' => 'canceled']);
    }

    public function test_user_cannot_cancel_others_travel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'status' => TravelStatusEnum::APPROVED,
        ]);

        $response = $this->postJson('/api/travels/cancel', [
            'travel_id' => $travel->id,
        ]);

        $response->assertForbidden();
    }
}
