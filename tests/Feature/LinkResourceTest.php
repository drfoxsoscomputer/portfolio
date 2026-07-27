<?php

namespace Tests\Feature;

use App\Filament\Resources\Links\Pages\ManageLinks;
use App\Models\Link;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinkResourceTest extends TestCase
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

    public function test_can_list_links(): void
    {
        Link::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get('/admin/links')
            ->assertSuccessful()
            ->assertSee('Redes Sociales');
    }

    public function test_can_create_link(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageLinks::class)
            ->callAction('create', [
                'label' => 'GitHub',
                'url' => 'https://github.com/drfoxsoscomputer',
                'sort_order' => 1,
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('links', [
            'label' => 'GitHub',
            'url' => 'https://github.com/drfoxsoscomputer',
        ]);
    }

    public function test_link_url_must_be_valid(): void
    {
        Livewire::actingAs($this->user)
            ->test(ManageLinks::class)
            ->callAction('create', [
                'label' => 'GitHub',
                'url' => 'not-a-url',
            ]);

        $this->assertDatabaseMissing('links', [
            'label' => 'GitHub',
        ]);
    }

    public function test_can_edit_link(): void
    {
        $link = Link::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageLinks::class)
            ->callTableAction('edit', $link->id, [
                'label' => 'LinkedIn',
                'url' => 'https://linkedin.com',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'label' => 'LinkedIn',
            'url' => 'https://linkedin.com',
        ]);
    }

    public function test_can_delete_link(): void
    {
        $link = Link::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageLinks::class)
            ->callTableAction('delete', $link->id);

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }
}
