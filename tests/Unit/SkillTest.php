<?php

namespace Tests\Unit;

use App\Models\Skill;
use App\Models\Profile;
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
        $profile = Profile::factory()->create();
        $skill = Skill::factory()->create(['profile_id' => $profile->id]);

        $this->assertModelExists($skill);
        $this->assertInstanceOf(Skill::class, $skill);
        $this->assertEquals($profile->id, $skill->profile_id);
        $this->assertNotEmpty($skill->name);
        $this->assertNotEmpty($skill->category);
    }

    /**
     * Test that Skill belongs to Profile relationship
     */
    public function test_skill_belongs_to_profile(): void
    {
        $profile = Profile::factory()->create();
        $skill = Skill::factory()->create(['profile_id' => $profile->id]);

        $this->assertInstanceOf(Profile::class, $skill->profile);
        $this->assertEquals($profile->id, $skill->profile->id);
    }

    /**
     * Test that Skill has the correct fillable attributes
     */
    public function test_skill_has_fillable_attributes(): void
    {
        $profile = Profile::factory()->create();
        $skill = Skill::factory()->create(['profile_id' => $profile->id]);

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
        $profile = Profile::factory()->create();

        // Create skills with different sort_order values
        Skill::factory()->create(['profile_id' => $profile->id, 'name' => 'Skill A', 'category' => 'Category A', 'sort_order' => 5]);
        Skill::factory()->create(['profile_id' => $profile->id, 'name' => 'Skill B', 'category' => 'Category B', 'sort_order' => 1]);
        Skill::factory()->create(['profile_id' => $profile->id, 'name' => 'Skill C', 'category' => 'Category C', 'sort_order' => 3]);

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
        $profile = Profile::factory()->create();
        $skill = Skill::factory()->create(['profile_id' => $profile->id]);

        $validCategories = ['Lenguajes', 'Frameworks', 'DB', 'Tools', 'Metodologías'];
        $this->assertContains($skill->category, $validCategories);
    }

    /**
     * Test factory creates realistic data
     */
    public function test_skill_factory_creates_realistic_data(): void
    {
        $profile = Profile::factory()->create();
        $skill = Skill::factory()->create(['profile_id' => $profile->id]);

        $this->assertNotEmpty($skill->name);
        $this->assertNotEmpty($skill->category);
        $this->assertIsString($skill->name);
        $this->assertIsString($skill->category);
        $this->assertIsInt($skill->sort_order);
    }
}