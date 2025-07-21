<?php

namespace Tests\Unit;

use App\DTOs\CancelTravelData;
use App\DTOs\TravelData;
use App\DTOs\UpdateTravelStatusData;
use App\Events\TravelStatusUpdated;
use App\Models\Travel;
use App\Repositories\TravelRepository;
use App\Services\TravelService;
use App\TravelStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Mockery;
use PHPUnit\Framework\TestCase;

class TravelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TravelRepository $repository;
    protected TravelService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(TravelRepository::class);
        $this->service = new TravelService($this->repository);
    }

    public function test_handle_create_travel(): void
    {
        $data = new TravelData('João da Silva', 'Paris', '2025-08-01', '2025-08-10');

        $travel = Travel::factory()->make();

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($travel);

        $result = $this->service->handleCreate($data);

        $this->assertInstanceOf(Travel::class, $result);
    }

    public function test_handle_update_status_dispatches_event(): void
    {
        Event::fake();
        Gate::shouldReceive('authorize')->once()->with('update', Mockery::type(Travel::class));

        $travel = Travel::factory()->make([
            'user_id' => 999,
            'status' => TravelStatusEnum::REQUESTED
        ]);

        $dto = new UpdateTravelStatusData(
            id: 1,
            user_id: '1',
            newStatus: TravelStatusEnum::APPROVED->value
        );

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($travel);

        $travel->shouldReceive('update')->once()->with(['status' => TravelStatusEnum::APPROVED]);

        $result = $this->service->handleUpdateStatus($dto);

        $this->assertInstanceOf(Travel::class, $result);
        Event::assertDispatched(TravelStatusUpdated::class);
    }

    public function test_handle_cancel_valid_travel(): void
    {
        Event::fake();
        $user = Auth::loginUsingId(1);

        $travel = Travel::factory()->make([
            'id' => 10,
            'user_id' => 2,
            'status' => TravelStatusEnum::APPROVED
        ]);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(10)
            ->andReturn($travel);

        $travel->shouldReceive('save')->once();
        $travel->status = TravelStatusEnum::CANCELED;

        $dto = new CancelTravelData(id: 10);

        $result = $this->service->handleCancel($dto);

        $this->assertEquals(TravelStatusEnum::CANCELED, $result->status);
        Event::assertDispatched(TravelStatusUpdated::class);
    }

    public function test_handle_cancel_throws_if_user_is_owner(): void
    {
        $user = Auth::loginUsingId(1);
        $travel = Travel::factory()->make(['user_id' => 1]);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(10)
            ->andReturn($travel);

        $this->expectExceptionMessage('You cannot cancel your own travel request.');

        $this->service->handleCancel(new CancelTravelData(10, $user->id));
    }

    public function test_handle_cancel_throws_if_status_not_approved(): void
    {
        $user = Auth::loginUsingId(1);
        $travel = Travel::factory()->make(['user_id' => 2, 'status' => TravelStatusEnum::REQUESTED]);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(10)
            ->andReturn($travel);

        $this->expectExceptionMessage('Only approved orders can be canceled.');

        $this->service->handleCancel(new CancelTravelData(10, $user->id));
    }
}
