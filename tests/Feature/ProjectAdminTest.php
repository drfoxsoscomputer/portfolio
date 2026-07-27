<?php

namespace Tests\Feature;

use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'daprthefox@gmail.com',
            'password' => 'asdf1234',
        ]);

        Profile::factory()->create();
    }

    public function test_can_list_projects(): void
    {
        Project::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get('/admin/projects')
            ->assertSuccessful()
            ->assertSee('Proyectos');
    }

    public function test_can_create_project(): void
    {
        Livewire::actingAs($this->user)
            ->test(ListProjects::class)
            ->callAction('create', [
                'name' => 'Mi Proyecto',
                'description' => 'Un proyecto de ejemplo',
                'tech_stack' => ['Laravel', 'Vue'],
                'role' => 'Desarrollador',
                'team_size' => '5',
                'url' => 'https://example.com',
                'repo_url' => 'https://github.com/example/repo',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'is_current' => false,
                'is_featured' => false,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'name' => 'Mi Proyecto',
            'description' => 'Un proyecto de ejemplo',
        ]);
    }

    public function test_can_edit_project(): void
    {
        $project = Project::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ListProjects::class)
            ->callTableAction('edit', $project->id, [
                'name' => 'Proyecto Editado',
                'description' => 'Descripción editada',
                'tech_stack' => ['Laravel', 'Vue', 'PHP'],
                'role' => 'Senior Developer',
                'team_size' => '10',
                'url' => 'https://edited.com',
                'repo_url' => 'https://github.com/edited/repo',
                'start_date' => '2024-02-01',
                'end_date' => '2024-11-30',
                'is_current' => true,
                'is_featured' => true,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Proyecto Editado',
            'is_current' => true,
            'is_featured' => true,
        ]);
    }

    public function test_can_delete_project(): void
    {
        $project = Project::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ListProjects::class)
            ->callTableAction('delete', $project->id);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_create_project_requires_name(): void
    {
        Livewire::actingAs($this->user)
            ->test(ListProjects::class)
            ->callAction('create', [
                'name' => '',
                'start_date' => '',
            ]);

        $this->assertDatabaseCount('projects', 0);
    }
}
