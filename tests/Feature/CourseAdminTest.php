<?php

namespace Tests\Feature;

use App\Filament\Resources\Course\Pages\ListCourses;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'daprthefox@gmail.com',
            'password' => 'asdf1234',
        ]);
    }

    public function test_can_list_courses(): void
    {
        Course::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get('/admin/courses')
            ->assertSuccessful()
            ->assertSee('Cursos');
    }

    public function test_can_create_course(): void
    {
        Livewire::actingAs($this->user)
            ->test(ListCourses::class)
            ->callAction('create', [
                'name' => 'Curso de Laravel',
                'institution' => 'Laracasts',
                'date' => '2024-06-15',
                'sort_order' => 1,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('courses', [
            'name' => 'Curso de Laravel',
            'institution' => 'Laracasts',
        ]);
    }

    public function test_can_edit_course(): void
    {
        $course = Course::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ListCourses::class)
            ->callTableAction('edit', $course->id, [
                'name' => 'Curso Actualizado',
                'institution' => 'Platzi',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Curso Actualizado',
            'institution' => 'Platzi',
        ]);
    }

    public function test_can_delete_course(): void
    {
        $course = Course::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ListCourses::class)
            ->callTableAction('delete', $course->id);

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_requires_name_and_institution(): void
    {
        $initialCount = Course::count();

        Livewire::actingAs($this->user)
            ->test(ListCourses::class)
            ->callAction('create', [
                'name' => '',
                'institution' => '',
            ]);

        $this->assertEquals($initialCount, Course::count());
    }
}
