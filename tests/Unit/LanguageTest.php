<?php

namespace Tests\Unit;

use App\Models\Language;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Language model can be created
     */
    public function test_language_can_be_created(): void
    {
        $profile = Profile::factory()->create();
        $language = Language::factory()->create(['profile_id' => $profile->id]);

        $this->assertModelExists($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals($profile->id, $language->profile_id);
        $this->assertNotEmpty($language->name);
        $this->assertNotEmpty($language->level);
        $this->assertIsInt($language->sort_order);
    }

    /**
     * Test that Language belongs to Profile relationship
     */
    public function test_language_belongs_to_profile(): void
    {
        $profile = Profile::factory()->create();
        $language = Language::factory()->create(['profile_id' => $profile->id]);

        $this->assertInstanceOf(Profile::class, $language->profile);
        $this->assertEquals($profile->id, $language->profile->id);
    }

    /**
     * Test that Language has the correct fillable attributes
     */
    public function test_language_has_fillable_attributes(): void
    {
        $profile = Profile::factory()->create();
        $language = Language::factory()->create(['profile_id' => $profile->id]);

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
     * Test that Language has the correct default ordering by sort_order (ascending)
     */
    public function test_language_default_ordering(): void
    {
        $profile = Profile::factory()->create();

        // Create languages with different sort_order values
        Language::factory()->create(['profile_id' => $profile->id, 'name' => 'Language A', 'sort_order' => 5]);
        Language::factory()->create(['profile_id' => $profile->id, 'name' => 'Language B', 'sort_order' => 1]);
        Language::factory()->create(['profile_id' => $profile->id, 'name' => 'Language C', 'sort_order' => 3]);

        $languages = Language::all();

        $this->assertEquals('Language B', $languages[0]->name);
        $this->assertEquals('Language C', $languages[1]->name);
        $this->assertEquals('Language A', $languages[2]->name);
    }

    /**
     * Test valid language levels
     */
    public function test_language_level_validation(): void
    {
        $profile = Profile::factory()->create();
        $language = Language::factory()->create(['profile_id' => $profile->id]);

        $validLevels = ['Beginner', 'Intermediate', 'Advanced', 'Fluent', 'Native'];
        $this->assertContains($language->level, $validLevels);
    }

    /**
     * Test factory creates realistic data
     */
    public function test_language_factory_creates_realistic_data(): void
    {
        $profile = Profile::factory()->create();
        $language = Language::factory()->create(['profile_id' => $profile->id]);

        $this->assertNotEmpty($language->name);
        $this->assertNotEmpty($language->level);
        $this->assertIsString($language->name);
        $this->assertIsString($language->level);
        $this->assertIsInt($language->sort_order);
    }
}