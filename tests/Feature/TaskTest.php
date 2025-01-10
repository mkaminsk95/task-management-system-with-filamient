<?php

declare(strict_types=1);

namespace Feature;

use App\Filament\Resources\TaskResource\Pages\CreateTask;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class TaskTest extends TestCase
{
    public function test_create_task_returns_a_successful_response(): void
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make();

        Livewire::test(CreateTask::class)
            ->fillForm($task->toArray())
            ->call('create')
            ->assertStatus(200);
    }

    public function test_create_task_returns_validation_error_with_empty_name(): void
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make();
        $task->name = '';

        Livewire::test(CreateTask::class)
            ->fillForm($task->toArray())
            ->call('create')
            ->assertHasErrors(['data.name' => ['required']]);
    }

    public function test_create_task_returns_validation_error_with_too_short_name(): void
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make();
        $task->name = 'a';

        Livewire::test(CreateTask::class)
            ->fillForm($task->toArray())
            ->call('create')
            ->assertHasErrors(['data.name' => ['min:3']]);
    }

    public function test_create_task_returns_validation_error_with_wrong_dates(): void
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make();
        $task->start_date = now();
        $task->end_date = now()->subDay();

        Livewire::test(CreateTask::class)
            ->fillForm($task->toArray())
            ->call('create')
            ->assertHasErrors('data.end_date');
    }

    public function test_create_task_returns_validation_error_with_wrong_status(): void
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make();
        $task->status = 'Dummy Status';

        Livewire::test(CreateTask::class)
            ->fillForm($task->toArray())
            ->call('create')
            ->assertHasErrors('data.status');
    }
}
