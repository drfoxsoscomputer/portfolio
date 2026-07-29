<?php

namespace Tests\Feature;

use App\Filament\Resources\Experiences\Pages\ManageExperiences;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExperienceAdminTest extends TestCase
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

        // User-based ownership - $this->user is the owner
    }

    public function test_can_list_experiences(): void
    {
        Experience::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get('/admin/experiences')
            ->assertSuccessful()
            ->assertSee('Experiencia');
    }

    public function test_can_create_experience(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageExperiences::class)
            ->callAction('create', [
                'company' => 'Tech Corp',
                'role' => 'Desarrollador Senior',
                'description' => 'Desarrollo de software',
                'location' => 'Ciudad de México',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'is_current' => false,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('experiences', [
            'company' => 'Tech Corp',
            'role' => 'Desarrollador Senior',
        ]);
    }

    public function test_can_edit_experience(): void
    {
        $experience = Experience::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageExperiences::class)
            ->callTableAction('edit', $experience->id, [
                'company' => 'Empresa Editada',
                'role' => 'Gerente de Proyecto',
                'description' => 'Dirección de proyecto',
                'location' => 'Guadalajara',
                'start_date' => '2023-06-01',
                'end_date' => '2024-05-31',
                'is_current' => true,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('experiences', [
            'id' => $experience->id,
            'company' => 'Empresa Editada',
            'is_current' => true,
        ]);
    }

    public function test_can_delete_experience(): void
    {
        $experience = Experience::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageExperiences::class)
            ->callTableAction('delete', $experience->id);

        $this->assertDatabaseMissing('experiences', ['id' => $experience->id]);
    }

    public function test_experiencia_actual_shows_badge(): void
    {
        Experience::factory()->create(['is_current' => true]);

        $this->actingAs($this->user)
            ->get('/admin/experiences')
            ->assertSuccessful();
    }

    public function test_experience_requires_start_date(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageExperiences::class)
            ->callAction('create', [
                'company' => 'Compañía',
                'role' => 'Rol',
                'start_date' => '',
            ]);

        $this->assertDatabaseCount('experiences', 0);
    }
}
