<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\ContactRequests\Pages\ManageContactRequests;
use App\Models\ContactRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactRequestAdminTest extends TestCase
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

    public function test_can_list_contact_requests(): void
    {
        ContactRequest::factory()->create(['name' => 'Reclutador Interesado']);

        $this->actingAs($this->user)
            ->get('/admin/contact-requests')
            ->assertSuccessful()
            ->assertSee('Mensajes')
            ->assertSee('Reclutador Interesado');
    }

    public function test_unread_requests_show_badge_count_in_navigation(): void
    {
        ContactRequest::factory()->count(2)->create();
        ContactRequest::factory()->read()->create();

        $this->assertSame('2', ContactRequestResource::getNavigationBadge());
    }

    public function test_navigation_badge_hidden_when_no_unread_requests(): void
    {
        ContactRequest::factory()->read()->create();

        $this->assertNull(ContactRequestResource::getNavigationBadge());
    }

    public function test_can_mark_request_as_read_from_table(): void
    {
        $request = ContactRequest::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageContactRequests::class)
            ->callTableAction('markAsRead', $request->id)
            ->assertHasNoErrors();

        $this->assertNotNull($request->fresh()->read_at);
    }

    public function test_mark_as_read_action_hidden_for_already_read_request(): void
    {
        $request = ContactRequest::factory()->read()->create();

        Livewire::actingAs($this->user)
            ->test(ManageContactRequests::class)
            ->assertTableActionHidden('markAsRead', $request->id);
    }

    public function test_resource_is_read_only(): void
    {
        $this->assertFalse(ContactRequestResource::canCreate());

        $this->actingAs($this->user)
            ->get('/admin/contact-requests/create')
            ->assertNotFound();
    }

    public function test_can_delete_request_from_table(): void
    {
        $request = ContactRequest::factory()->create();

        Livewire::actingAs($this->user)
            ->test(ManageContactRequests::class)
            ->callTableAction('delete', $request->id);

        $this->assertDatabaseMissing('contact_requests', ['id' => $request->id]);
    }
}
