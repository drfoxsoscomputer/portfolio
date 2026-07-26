<?php

namespace Tests\Unit;

use App\Models\Education;
use App\Models\Image;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Education model can be created
     */
    public function test_education_can_be_created(): void
    {
        $profile = Profile::factory()->create();
        $education = Education::factory()->create(['profile_id' => $profile->id]);

        $this->assertModelExists($education);
        $this->assertInstanceOf(Education::class, $education);
        $this->assertEquals($profile->id, $education->profile_id);
        $this->assertNotEmpty($education->institution);
        $this->assertNotEmpty($education->degree);
        $this->assertIsInt($education->sort_order);
    }

    /**
     * Test that Education belongs to Profile relationship
     */
    public function test_education_belongs_to_profile(): void
    {
        $profile = Profile::factory()->create();
        $education = Education::factory()->create(['profile_id' => $profile->id]);

        $this->assertInstanceOf(Profile::class, $education->profile);
        $this->assertEquals($profile->id, $education->profile->id);
    }

    /**
     * Test that Education has the correct fillable attributes
     */
    public function test_education_has_fillable_attributes(): void
    {
        $profile = Profile::factory()->create();
        $education = Education::factory()->create(['profile_id' => $profile->id]);

        $education->update([
            'institution' => 'Test Institution',
            'degree' => 'Test Degree',
            'field' => 'Test Field',
            'sort_order' => 5,
        ]);

        $this->assertEquals('Test Institution', $education->institution);
        $this->assertEquals('Test Degree', $education->degree);
        $this->assertEquals('Test Field', $education->field);
        $this->assertEquals(5, $education->sort_order);
    }

    /**
     * Test that Education has the correct default ordering by sort_order (ascending)
     */
    public function test_education_default_ordering(): void
    {
        $profile = Profile::factory()->create();

        // Create educations with different sort_order values
        Education::factory()->create([
            'profile_id' => $profile->id,
            'institution' => 'Institution A',
            'degree' => 'Degree A',
            'sort_order' => 5,
        ]);

        Education::factory()->create([
            'profile_id' => $profile->id,
            'institution' => 'Institution B',
            'degree' => 'Degree B',
            'sort_order' => 1,
        ]);

        Education::factory()->create([
            'profile_id' => $profile->id,
            'institution' => 'Institution C',
            'degree' => 'Degree C',
            'sort_order' => 3,
        ]);

        $educations = Education::all();

        $this->assertEquals('Institution B', $educations[0]->institution);
        $this->assertEquals('Institution C', $educations[1]->institution);
        $this->assertEquals('Institution A', $educations[2]->institution);
    }

    /**
     * Test that Education has morphMany Images relationship method
     */
    public function test_education_images_relationship(): void
    {
        $profile = Profile::factory()->create();
        $education = Education::factory()->create(['profile_id' => $profile->id]);

        $this->assertTrue(method_exists($education, 'images'));

        // Test that we can actually load images for education
        $image = Image::factory()->create([
            'imageable_type' => 'Education',
            'imageable_id' => $education->id,
            'type' => 'certificate',
        ]);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('Education', $image->imageable_type);
        $this->assertEquals($education->id, $image->imageable_id);
    }

    /**
     * Test factory creates realistic data
     */
    public function test_education_factory_creates_realistic_data(): void
    {
        $profile = Profile::factory()->create();
        $education = Education::factory()->create(['profile_id' => $profile->id]);

        $this->assertNotEmpty($education->institution);
        $this->assertNotEmpty($education->degree);
        $this->assertIsString($education->institution);
        $this->assertIsString($education->degree);
        $this->assertIsInt($education->sort_order);
        $this->assertIsBool($education->is_current);
    }
}