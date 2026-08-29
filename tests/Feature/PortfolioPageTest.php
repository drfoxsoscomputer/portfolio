<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesPortfolioUser;
use Tests\TestCase;

class PortfolioPageTest extends TestCase
{
    use CreatesPortfolioUser, RefreshDatabase;

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

    public function test_home_page_renders_cv_download_button(): void
    {
        $this->createUserWithRelations();

        $this->get('/')
            ->assertOk()
            ->assertSee('Download CV')
            ->assertSee('href="'.route('cv').'"', false);
    }

    public function test_home_page_renders_dark_mode_toggle(): void
    {
        $this->createUserWithRelations();

        $this->get('/')->assertSee('Dark mode');
    }

    public function test_home_page_renders_owner_phone_as_whatsapp_link(): void
    {
        $user = $this->createUserWithRelations();

        $user->update(['phone' => '+58 414-516-9484']);

        $this->get('/')
            ->assertSee('+58 414-516-9484')
            ->assertSee('https://wa.me/584145169484', false);
    }

    public function test_home_page_includes_print_only_contact_header(): void
    {
        $user = $this->createUserWithRelations();

        $user->update(['phone' => '+58 414-516-9484']);

        $this->get('/')
            ->assertSee('print:block', false)
            ->assertSee('(WhatsApp)', false);
    }
}
