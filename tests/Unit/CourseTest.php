<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_can_be_created(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($course);
        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals($user->id, $course->user_id);
        $this->assertNotEmpty($course->name);
        $this->assertNotEmpty($course->institution);
    }

    public function test_course_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $course->user);
        $this->assertEquals($user->id, $course->user->id);
    }

    public function test_course_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $course->update([
            'name' => 'Curso de PHP',
            'institution' => 'Platzi',
            'sort_order' => 3,
        ]);

        $this->assertEquals('Curso de PHP', $course->name);
        $this->assertEquals('Platzi', $course->institution);
        $this->assertEquals(3, $course->sort_order);
    }

    public function test_course_factory_creates_valid_data(): void
    {
        $course = Course::factory()->create();

        $this->assertNotEmpty($course->name);
        $this->assertNotEmpty($course->institution);
        $this->assertNotNull($course->user_id);
    }

    public function test_course_has_media_collections(): void
    {
        $course = Course::factory()->create();

        $this->assertTrue(method_exists($course, 'registerMediaCollections'));
    }
}
