<?php

namespace Tests\Feature;

use App\Filament\Resources\Languages\Pages\ManageLanguages;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LanguageResourceTest extends TestCase
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

        // Propiedad basada en User - $this->user es el propietario
    }

    public function test_can_list_languages(): void
    {
        Language::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get('/admin/languages')
            ->assertSuccessful()
            ->assertSee('Idiomas');
    }

    public function test_can_create_language(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageLanguages::class)
            ->callAction('create', [
                'name' => 'Inglés',
                'level' => 'advanced',
                'sort_order' => 1,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('languages', [
            'name' => 'Inglés',
            'level' => 'advanced',
        ]);
    }

    public function test_can_edit_language(): void
    {
        $language = Language::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageLanguages::class)
            ->callTableAction('edit', $language->id, [
                'name' => 'Francés',
                'level' => 'intermediate',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'name' => 'Francés',
            'level' => 'intermediate',
        ]);
    }

    public function test_can_delete_language(): void
    {
        $language = Language::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageLanguages::class)
            ->callTableAction('delete', $language->id);

        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }

    public function test_level_must_be_valid(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageLanguages::class)
            ->callAction('create', [
                'name' => 'Alemán',
                'level' => 'invalid-level',
            ]);

        $this->assertDatabaseMissing('languages', ['name' => 'Alemán']);
    }
}
