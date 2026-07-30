<?php

namespace Tests\Unit;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que el modelo Language puede crearse
     */
    public function test_language_can_be_created(): void
    {
        $user = User::factory()->create();
        $language = Language::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals($user->id, $language->user_id);
        $this->assertNotEmpty($language->name);
        $this->assertNotEmpty($language->level);
        $this->assertIsInt($language->sort_order);
    }

    /**
     * Test que Language pertenece a la relación User
     */
    public function test_language_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $language = Language::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $language->user);
        $this->assertEquals($user->id, $language->user->id);
    }

    /**
     * Test que Language tiene los atributos fillable correctos
     */
    public function test_language_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $language = Language::factory()->create(['user_id' => $user->id]);

        $language->update([
            'name' => 'Test Language',
            'level' => 'Test Level',
            'sort_order' => 5,
        ]);

        $this->assertEquals('Test Language', $language->name);
        $this->assertEquals('Test Level', $language->level);
        $this->assertEquals(5, $language->sort_order);
    }

    /**
     * Test que Language tiene el ordenamiento correcto por sort_order (ascendente)
     */
    public function test_language_default_ordering(): void
    {
        $user = User::factory()->create();

        // Crear idiomas con diferentes valores de sort_order
        Language::factory()->create(['user_id' => $user->id, 'name' => 'Language A', 'sort_order' => 5]);
        Language::factory()->create(['user_id' => $user->id, 'name' => 'Language B', 'sort_order' => 1]);
        Language::factory()->create(['user_id' => $user->id, 'name' => 'Language C', 'sort_order' => 3]);

        $languages = Language::all();

        $this->assertEquals('Language B', $languages[0]->name);
        $this->assertEquals('Language C', $languages[1]->name);
        $this->assertEquals('Language A', $languages[2]->name);
    }

    /**
     * Test de niveles de idioma válidos
     */
    public function test_language_level_validation(): void
    {
        $user = User::factory()->create();
        $language = Language::factory()->create(['user_id' => $user->id]);

        $validLevels = ['Beginner', 'Intermediate', 'Advanced', 'Fluent', 'Native'];
        $this->assertContains($language->level, $validLevels);
    }

    /**
     * Test que el factory crea datos realistas
     */
    public function test_language_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $language = Language::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($language->name);
        $this->assertNotEmpty($language->level);
        $this->assertIsString($language->name);
        $this->assertIsString($language->level);
        $this->assertIsInt($language->sort_order);
    }
}