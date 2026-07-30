<?php

namespace Tests\Unit;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que el modelo Skill puede crearse
     */
    public function test_skill_can_be_created(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($skill);
        $this->assertInstanceOf(Skill::class, $skill);
        $this->assertEquals($user->id, $skill->user_id);
        $this->assertNotEmpty($skill->name);
        $this->assertNotEmpty($skill->category);
    }

    /**
     * Test que Skill pertenece a la relación User
     */
    public function test_skill_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $skill->user);
        $this->assertEquals($user->id, $skill->user->id);
    }

    /**
     * Test que Skill tiene los atributos fillable correctos
     */
    public function test_skill_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $skill->update([
            'name' => 'Test Skill',
            'category' => 'Test Category',
            'sort_order' => 5,
        ]);

        $this->assertEquals('Test Skill', $skill->name);
        $this->assertEquals('Test Category', $skill->category);
        $this->assertEquals(5, $skill->sort_order);
    }

    /**
     * Test que Skill tiene el ordenamiento correcto por sort_order (ascendente)
     */
    public function test_skill_default_ordering(): void
    {
        $user = User::factory()->create();

        // Crear habilidades con diferentes valores de sort_order
        Skill::factory()->create(['user_id' => $user->id, 'name' => 'Skill A', 'category' => 'Category A', 'sort_order' => 5]);
        Skill::factory()->create(['user_id' => $user->id, 'name' => 'Skill B', 'category' => 'Category B', 'sort_order' => 1]);
        Skill::factory()->create(['user_id' => $user->id, 'name' => 'Skill C', 'category' => 'Category C', 'sort_order' => 3]);

        $skills = Skill::all();

        $this->assertEquals('Skill B', $skills[0]->name);
        $this->assertEquals('Skill C', $skills[1]->name);
        $this->assertEquals('Skill A', $skills[2]->name);
    }

    /**
     * Test que Skill tiene valores de categoría válidos
     */
    public function test_skill_category_validation(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $validCategories = ['Lenguajes', 'Frameworks', 'DB', 'Tools', 'Metodologías'];
        $this->assertContains($skill->category, $validCategories);
    }

    /**
     * Test que el factory crea datos realistas
     */
    public function test_skill_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($skill->name);
        $this->assertNotEmpty($skill->category);
        $this->assertIsString($skill->name);
        $this->assertIsString($skill->category);
        $this->assertIsInt($skill->sort_order);
    }
}