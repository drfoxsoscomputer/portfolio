<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Link;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a user with one record for every portfolio section.
     */
    private function createUserWithRelations(): User
    {
        $user = User::factory()->create([
            'name' => 'Denis Pina Test',
            'title' => 'Full Stack Developer',
            'summary' => 'Building things with Laravel.',
            'location' => 'Caracas, Venezuela',
        ]);

        Link::factory()->create([
            'user_id' => $user->id,
            'label' => 'GitHub',
            'url' => 'https://github.com/drfoxsoscomputer',
            'icon' => 'github',
            'sort_order' => 1,
        ]);

        Skill::factory()->create([
            'user_id' => $user->id,
            'name' => 'Laravel',
            'category' => 'Frameworks',
            'sort_order' => 1,
        ]);

        Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'GameWorld Ecommerce',
            'description' => 'An online store platform.',
            'tech_stack' => ['PHP', 'Laravel'],
            'url' => 'https://gameworld.example',
            'repo_url' => 'https://github.com/drfoxsoscomputer/gameworld',
        ]);

        Experience::factory()->create([
            'user_id' => $user->id,
            'company' => 'Acme Corp',
            'role' => 'Senior Developer',
            'location' => 'Remote',
            'start_date' => '2020-01-01',
            'end_date' => null,
            'is_current' => true,
        ]);

        Education::factory()->create([
            'user_id' => $user->id,
            'institution' => 'Central University',
            'degree' => 'Bachelor',
            'field' => 'Computer Science',
            'sort_order' => 1,
        ]);

        Language::factory()->create([
            'user_id' => $user->id,
            'name' => 'Spanish',
            'level' => 'Native',
            'sort_order' => 1,
        ]);

        Course::factory()->create([
            'user_id' => $user->id,
            'name' => 'Advanced Laravel',
            'institution' => 'Laracasts',
            'sort_order' => 1,
        ]);

        return $user;
    }

    public function test_home_page_returns_successful_response(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertOk();
    }

    public function test_home_page_renders_user_name_and_title(): void
    {
        $this->createUserWithRelations();

        $this->get('/')
            ->assertOk()
            ->assertSee('Denis Pina Test')
            ->assertSee('Full Stack Developer');
    }

    public function test_home_page_renders_hero_avatar_when_media_exists(): void
    {
        $user = User::factory()->create(['name' => 'Denis Pina Test']);

        $user->addMediaFromString(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='))
            ->usingFileName('avatar-test.webp')
            ->toMediaCollection('avatar');

        $this->get('/')->assertSee('avatar-test.webp');
    }

    public function test_home_page_renders_skills(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Laravel');
    }

    public function test_home_page_renders_projects_with_tech_stack_and_links(): void
    {
        $this->createUserWithRelations();

        $this->get('/')
            ->assertSee('GameWorld Ecommerce')
            ->assertSee('PHP')
            ->assertSee('https://gameworld.example')
            ->assertSee('https://github.com/drfoxsoscomputer/gameworld');
    }

    public function test_home_page_renders_experience(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Acme Corp');
    }

    public function test_home_page_renders_education(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Central University');
    }

    public function test_home_page_renders_languages(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Spanish');
    }

    public function test_home_page_renders_language_level_percentage(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('data-level-percent="100"', false);
    }

    public function test_home_page_renders_courses(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Advanced Laravel');
    }

    public function test_home_page_renders_social_links(): void
    {
        $this->createUserWithRelations();

        $this->get('/')
            ->assertSee('GitHub')
            ->assertSee('https://github.com/drfoxsoscomputer');
    }

    public function test_home_page_renders_project_screenshot_when_media_exists(): void
    {
        $user = $this->createUserWithRelations();

        $user->projects()->first()
            ->addMediaFromString('fake-screenshot-bytes')
            ->usingFileName('screenshot-test.webp')
            ->toMediaCollection('screenshots');

        $this->get('/')->assertSee('screenshot-test.webp');
    }

    public function test_home_page_renders_stats_with_expected_counts(): void
    {
        $user = $this->createUserWithRelations();

        // Make the experience span exactly 5 years so the calculation is deterministic.
        $user->experiences()->update(['start_date' => now()->subYears(5)->toDateString()]);

        $this->get('/')
            ->assertSee('data-stat="years"', false)
            ->assertSee('data-count="5"', false)
            ->assertSee('data-stat="projects"', false)
            ->assertSee('data-stat="skills"', false);
    }

    public function test_home_page_renders_without_any_relations(): void
    {
        User::factory()->create(['name' => 'Solo User']);

        $this->get('/')->assertOk()->assertSee('Solo User');
    }

    public function test_home_page_renders_dark_mode_toggle(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Toggle dark mode');
    }

    public function test_home_page_renders_owner_phone_as_whatsapp_link(): void
    {
        $user = $this->createUserWithRelations();

        $user->update(['phone' => '+58 414-516-9484']);

        $this->get('/')
            ->assertSee('+58 414-516-9484')
            ->assertSee('https://wa.me/584145169484', false);
    }
}
