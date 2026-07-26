<?php

namespace Tests\Unit;

use App\Models\Experience;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Experience model can be created
     */
    public function test_experience_can_be_created(): void
    {
        $profile = Profile::factory()->create();
        $experience = Experience::factory()->create(['profile_id' => $profile->id]);

        $this->assertModelExists($experience);
        $this->assertInstanceOf(Experience::class, $experience);
        $this->assertEquals($profile->id, $experience->profile_id);
        $this->assertNotEmpty($experience->company);
        $this->assertNotEmpty($experience->role);
    }

    /**
     * Test that Experience belongs to Profile relationship
     */
    public function test_experience_belongs_to_profile(): void
    {
        $profile = Profile::factory()->create();
        $experience = Experience::factory()->create(['profile_id' => $profile->id]);

        $this->assertInstanceOf(Profile::class, $experience->profile);
        $this->assertEquals($profile->id, $experience->profile->id);
    }

    /**
     * Test that Experience has the correct fillable attributes
     */
    public function test_experience_has_fillable_attributes(): void
    {
        $profile = Profile::factory()->create();
        $experience = Experience::factory()->create(['profile_id' => $profile->id]);

        $experience->update([
            'company' => 'Test Company',
            'role' => 'Test Role',
            'location' => 'Test Location',
        ]);

        $this->assertEquals('Test Company', $experience->company);
        $this->assertEquals('Test Role', $experience->role);
        $this->assertEquals('Test Location', $experience->location);
    }

    /**
     * Test that Experience has the correct default ordering by start_date (descending)
     */
    public function test_experience_default_ordering(): void
    {
        $profile = Profile::factory()->create();

        // Create experiences with different start_date values
        Experience::factory()->create([
            'profile_id' => $profile->id,
            'company' => 'Company A',
            'role' => 'Role A',
            'start_date' => '2024-01-15',
        ]);

        Experience::factory()->create([
            'profile_id' => $profile->id,
            'company' => 'Company B',
            'role' => 'Role B',
            'start_date' => '2023-06-20',
        ]);

        Experience::factory()->create([
            'profile_id' => $profile->id,
            'company' => 'Company C',
            'role' => 'Role C',
            'start_date' => '2024-03-10',
        ]);

        $experiences = Experience::all();

        // Descending order by start_date: Company C (2024-03-10) first, then Company A (2024-01-15), then Company B (2023-06-20)
        $this->assertEquals('Company C', $experiences[0]->company);
        $this->assertEquals('Company A', $experiences[1]->company);
        $this->assertEquals('Company B', $experiences[2]->company);
    }

    /**
     * Test factory creates realistic data
     */
    public function test_experience_factory_creates_realistic_data(): void
    {
        $profile = Profile::factory()->create();
        $experience = Experience::factory()->create(['profile_id' => $profile->id]);

        $this->assertNotEmpty($experience->company);
        $this->assertNotEmpty($experience->role);
        $this->assertIsString($experience->company);
        $this->assertIsString($experience->role);
        $this->assertInstanceOf(\DateTimeInterface::class, $experience->start_date);
        $this->assertIsBool($experience->is_current);
    }
}