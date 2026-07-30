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
     * Test that the Skill model can be created
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
     * Test that Skill belongs to User relationship
     */
    public function test_skill_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $skill->user);
        $this->assertEquals($user->id, $skill->user->id);
    }

    /**
     * Test that Skill has the correct fillable attributes
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
     * Test that Skill has the correct default ordering by sort_order (ascending)
     */
    public function test_skill_default_ordering(): void
    {
        $user = User::factory()->create();

        // Create skills with different sort_order values
        Skill::factory()->create(['user_id' => $user->id, 'name' => 'Skill A', 'category' => 'Category A', 'sort_order' => 5]);
        Skill::factory()->create(['user_id' => $user->id, 'name' => 'Skill B', 'category' => 'Category B', 'sort_order' => 1]);
        Skill::factory()->create(['user_id' => $user->id, 'name' => 'Skill C', 'category' => 'Category C', 'sort_order' => 3]);

        $skills = Skill::all();

        $this->assertEquals('Skill B', $skills[0]->name);
        $this->assertEquals('Skill C', $skills[1]->name);
        $this->assertEquals('Skill A', $skills[2]->name);
    }

    /**
     * Test that Skill has the correct category values
     */
    public function test_skill_category_validation(): void
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id]);

        $validCategories = ['Lenguajes', 'Frameworks', 'DB', 'Tools', 'Metodologías'];
        $this->assertContains($skill->category, $validCategories);
    }

    /**
     * Test factory creates realistic data
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