<?php

namespace Tests\Unit;

use App\Models\Education;
use App\Models\Image;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_can_be_created(): void
    {
        $user = User::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $user->id,
            'imageable_type' => $user->getMorphClass(),
        ]);

        $this->assertModelExists($image);
        $this->assertInstanceOf(Image::class, $image);
        $this->assertNotEmpty($image->url);
        $this->assertIsString($image->type);
    }

    public function test_image_has_morph_to_relationship(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $education = Education::factory()->create(['user_id' => $user->id]);

        // Attach to User
        $userImage = Image::factory()->create([
            'imageable_id' => $user->id,
            'imageable_type' => $user->getMorphClass(),
        ]);
        $this->assertInstanceOf(User::class, $userImage->imageable);
        $this->assertEquals($user->id, $userImage->imageable->id);

        // Attach to Project
        $projectImage = Image::factory()->create([
            'imageable_id' => $project->id,
            'imageable_type' => $project->getMorphClass(),
        ]);
        $this->assertInstanceOf(Project::class, $projectImage->imageable);
        $this->assertEquals($project->id, $projectImage->imageable->id);

        // Attach to Education
        $educationImage = Image::factory()->create([
            'imageable_id' => $education->id,
            'imageable_type' => $education->getMorphClass(),
        ]);
        $this->assertInstanceOf(Education::class, $educationImage->imageable);
        $this->assertEquals($education->id, $educationImage->imageable->id);
    }

    public function test_image_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $user->id,
            'imageable_type' => $user->getMorphClass(),
        ]);

        $image->update([
            'url' => 'https://example.com/test.jpg',
            'alt_text' => 'Test alt text',
            'type' => 'avatar',
            'sort_order' => 5,
        ]);

        $this->assertEquals('https://example.com/test.jpg', $image->url);
        $this->assertEquals('Test alt text', $image->alt_text);
        $this->assertEquals('avatar', $image->type);
        $this->assertEquals(5, $image->sort_order);
    }

    public function test_image_default_ordering(): void
    {
        $user = User::factory()->create();
        $morph = $user->getMorphClass();

        Image::factory()->create(['imageable_id' => $user->id, 'imageable_type' => $morph, 'type' => 'avatar', 'sort_order' => 5]);
        Image::factory()->create(['imageable_id' => $user->id, 'imageable_type' => $morph, 'type' => 'screenshot', 'sort_order' => 1]);
        Image::factory()->create(['imageable_id' => $user->id, 'imageable_type' => $morph, 'type' => 'logo', 'sort_order' => 3]);

        $images = Image::all();

        $this->assertEquals('screenshot', $images[0]->type);
        $this->assertEquals('logo', $images[1]->type);
        $this->assertEquals('avatar', $images[2]->type);
    }

    public function test_image_type_validation(): void
    {
        $user = User::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $user->id,
            'imageable_type' => $user->getMorphClass(),
        ]);

        $validTypes = ['avatar', 'screenshot', 'logo', 'certificate'];
        $this->assertContains($image->type, $validTypes);
    }

    public function test_image_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $user->id,
            'imageable_type' => $user->getMorphClass(),
        ]);

        $this->assertNotEmpty($image->url);
        $this->assertIsString($image->type);
        $this->assertIsInt($image->sort_order);
    }
}
