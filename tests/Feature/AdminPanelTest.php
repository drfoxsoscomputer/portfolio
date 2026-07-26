<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
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

    public function test_redirects_to_login_when_accessing_admin_panel_without_auth(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_allows_admin_user_to_login(): void
    {
        $this->post('/admin/login', [
            'email' => 'daprthefox@gmail.com',
            'password' => 'asdf1234',
        ])->assertSessionHasNoErrors();
    }

    public function test_shows_dashboard_after_login(): void
    {
        $this->actingAs($this->user)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Escritorio')
            ->assertSee('Portafolio');
    }

    public function test_displays_portfolio_brand_as_title(): void
    {
        $this->actingAs($this->user)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Portafolio');
    }

    public function test_shows_logout_button(): void
    {
        $this->actingAs($this->user)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Salir');
    }
}
