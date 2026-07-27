<?php

namespace Tests\Feature;

use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SkillResourceTest extends TestCase
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

        Profile::factory()->create();
    }

    public function test_can_list_skills(): void
    {
        Skill::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get('/admin/skills')
            ->assertSuccessful()
            ->assertSee('Habilidades');
    }

    public function test_can_create_skill(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageSkills::class)
            ->callAction('create', [
                'name' => 'Laravel',
                'category' => 'backend',
                'sort_order' => 1,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('skills', [
            'name' => 'Laravel',
            'category' => 'backend',
        ]);
    }

    public function test_can_edit_skill(): void
    {
        $skill = Skill::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageSkills::class)
            ->callTableAction('edit', $skill->id, [
                'name' => 'React',
                'category' => 'frontend',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'React',
            'category' => 'frontend',
        ]);
    }

    public function test_can_delete_skill(): void
    {
        $skill = Skill::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageSkills::class)
            ->callTableAction('delete', $skill->id);

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_skills_are_grouped_by_category(): void
    {
        Skill::factory()->create(['name' => 'PHP', 'category' => 'backend', 'sort_order' => 1]);
        Skill::factory()->create(['name' => 'Vue', 'category' => 'frontend', 'sort_order' => 1]);
        Skill::factory()->create(['name' => 'Docker', 'category' => 'devops', 'sort_order' => 1]);

        $skills = Skill::query()->orderBy('category')->get();

        $this->assertEquals('PHP', $skills[0]->name);
        $this->assertEquals('Docker', $skills[1]->name);
        $this->assertEquals('Vue', $skills[2]->name);
    }

    public function test_skill_category_must_be_valid(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageSkills::class)
            ->callAction('create', [
                'name' => 'Invalid',
                'category' => 'invalid-category',
            ]);

        $this->assertDatabaseMissing('skills', ['name' => 'Invalid']);
    }
}
