<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageProfile;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileAdminTest extends TestCase
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
    }

    public function test_profile_page_is_accessible(): void
    {
        Profile::factory()->create();

        $this->actingAs($this->user)
            ->get('/admin/profile')
            ->assertSuccessful()
            ->assertSee('Mi Perfil');
    }

    public function test_profile_page_shows_form_with_all_fields(): void
    {
        Profile::factory()->create();

        $this->actingAs($this->user)
            ->get('/admin/profile')
            ->assertSuccessful()
            ->assertSee('Nombre')
            ->assertSee('Título profesional')
            ->assertSee('Correo electrónico')
            ->assertSee('Guardar cambios');
    }

    public function test_profile_can_be_updated(): void
    {
        Profile::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageProfile::class)
            ->set('data.name', 'Denis Piña Actualizado')
            ->set('data.title', 'Senior Developer')
            ->set('data.email', 'daprthefox@gmail.com')
            ->set('data.summary', 'Nuevo resumen')
            ->set('data.location', 'Caracas, Venezuela')
            ->set('data.phone', '+584141234567')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('profiles', [
            'name' => 'Denis Piña Actualizado',
            'title' => 'Senior Developer',
            'summary' => 'Nuevo resumen',
        ]);
    }

    public function test_profile_form_requires_name_and_email(): void
    {
        Profile::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageProfile::class)
            ->set('data.name', '')
            ->set('data.email', '')
            ->call('save')
            ->assertHasErrors(['data.name', 'data.email']);
    }
}
