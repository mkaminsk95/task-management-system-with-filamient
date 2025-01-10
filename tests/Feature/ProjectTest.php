<?php

declare(strict_types=1);

namespace Feature;

use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    public function test_create_project_returns_a_successful_response(): void
    {
        $this->actingAs(User::factory()->create());
        $project = Project::factory()->make();

        Livewire::test(CreateProject::class)
            ->fillForm($project->toArray())
            ->call('create')
            ->assertStatus(200);
    }

    public function test_create_project_returns_validation_error_with_empty_name(): void
    {
        $this->actingAs(User::factory()->create());
        $project = Project::factory()->make();
        $project->name = '';

        Livewire::test(CreateProject::class)
            ->fillForm($project->toArray())
            ->call('create')
            ->assertHasErrors(['data.name' => ['required']]);
    }

    public function test_create_project_returns_validation_error_with_too_short_name(): void
    {
        $this->actingAs(User::factory()->create());
        $project = Project::factory()->make();
        $project->name = 'a';

        Livewire::test(CreateProject::class)
            ->fillForm($project->toArray())
            ->call('create')
            ->assertHasErrors(['data.name' => ['min:3']]);
    }

    public function test_create_project_returns_validation_error_with_wrong_dates(): void
    {
        $this->actingAs(User::factory()->create());
        $project = Project::factory()->make();
        $project->start_date = now();
        $project->end_date = now()->subDay();

        Livewire::test(CreateProject::class)
            ->fillForm($project->toArray())
            ->call('create')
            ->assertHasErrors('data.end_date');
    }
}
