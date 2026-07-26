<?php

namespace Tests\Unit;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Profile model can be created
     */
    public function test_profile_can_be_created(): void
    {
        $profile = Profile::factory()->create();

        $this->assertModelExists($profile);
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertNotEmpty($profile->name);
    }

    /**
     * Test singleton enforcement - only one Profile should exist
     */
    public function test_singleton_enforcement_prevents_duplicate_creation(): void
    {
        // Create first profile (should succeed)
        $firstProfile = Profile::factory()->create();
        $this->assertModelExists($firstProfile);

        // Attempt to create second profile (should fail via observer)
        $this->expectException(\Exception::class);
        Profile::factory()->create();
    }

    /**
     * Test that Profile has the correct fillable attributes
     */
    public function test_profile_has_fillable_attributes(): void
    {
        $profile = Profile::factory()->create();

        $profile->update([
            'name' => 'John Doe',
            'title' => 'Developer',
        ]);

        $this->assertEquals('John Doe', $profile->name);
        $this->assertEquals('Developer', $profile->title);
    }

    /**
     * Test that Profile has the correct relationship method declarations
     */
    public function test_profile_has_relationship_methods(): void
    {
        $profile = Profile::factory()->create();

        $this->assertTrue(method_exists($profile, 'links'));
        $this->assertTrue(method_exists($profile, 'projects'));
        $this->assertTrue(method_exists($profile, 'experiences'));
        $this->assertTrue(method_exists($profile, 'skills'));
        $this->assertTrue(method_exists($profile, 'educations'));
        $this->assertTrue(method_exists($profile, 'languages'));
        $this->assertTrue(method_exists($profile, 'images'));
    }

    /**
     * Test that Profile has the image relationship method
     */
    public function test_profile_images_relationship(): void
    {
        $profile = Profile::factory()->create();

        // Method exists (Image model is created in PR 3)
        $this->assertTrue(method_exists($profile, 'images'));
    }

    /**
     * Test factory creates realistic CV data
     */
    public function test_profile_factory_creates_realistic_data(): void
    {
        $profile = Profile::factory()->create();

        $this->assertNotEmpty($profile->name);
        $this->assertIsString($profile->name);
    }

    /**
     * Test Profile model instantiation without database
     */
    public function test_profile_model_can_be_instantiated(): void
    {
        $profile = new Profile();
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertNull($profile->id);
    }
}
