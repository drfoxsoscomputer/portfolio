<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Profile::factory()->create();
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
        $this->actingAs($this->user)
            ->get('/admin/languages/create')
            ->assertSuccessful();

        $response = $this->actingAs($this->user)
            ->post('/admin/languages', [
                'name' => 'Inglés',
                'level' => 'advanced',
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/languages');

        $this->assertDatabaseHas('languages', [
            'name' => 'Inglés',
            'level' => 'advanced',
        ]);
    }

    public function test_can_edit_language(): void
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($this->user)
            ->get("/admin/languages/{$language->id}/edit")
            ->assertSuccessful();

        $response = $this->actingAs($this->user)
            ->put("/admin/languages/{$language->id}", [
                'name' => 'Francés',
                'level' => 'intermediate',
            ])
            ->assertRedirect('/admin/languages');

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'name' => 'Francés',
            'level' => 'intermediate',
        ]);
    }

    public function test_can_delete_language(): void
    {
        $language = Language::factory()->create();

        $this->actingAs($this->user)
            ->delete("/admin/languages/{$language->id}")
            ->assertRedirect('/admin/languages');

        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }

    public function test_level_must_be_valid(): void
    {
        $this->actingAs($this->user)
            ->post('/admin/languages', [
                'name' => 'Alemán',
                'level' => 'invalid-level',
            ])
            ->assertSessionHasErrors(['level']);

        $this->assertDatabaseMissing('languages', ['name' => 'Alemán']);
    }
}
