<?php

namespace Tests\Feature;

use App\Contracts\ContactNotifierInterface;
use App\Livewire\Portfolio\ContactForm;
use App\Models\ContactRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_MESSAGE = 'I want to hire you for a project.';

    public function test_contact_form_requires_name_email_and_message(): void
    {
        Livewire::test(ContactForm::class)
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_form_rejects_invalid_email(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'not-an-email')
            ->set('message', self::VALID_MESSAGE)
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    public function test_contact_form_rejects_short_message(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('message', 'Short')
            ->call('submit')
            ->assertHasErrors(['message']);
    }

    public function test_guest_can_submit_valid_contact_request(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('message', self::VALID_MESSAGE)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true)
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('message', '');

        $this->assertDatabaseHas('contact_requests', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => self::VALID_MESSAGE,
        ]);
    }

    public function test_submitting_contact_request_creates_notification_for_admin(): void
    {
        $admin = User::factory()->create(['email' => 'daprthefox@gmail.com']);

        Livewire::test(ContactForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('message', self::VALID_MESSAGE)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertCount(1, $admin->fresh()->notifications);
    }

    public function test_contact_request_can_be_marked_as_read(): void
    {
        $request = ContactRequest::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => self::VALID_MESSAGE,
        ]);

        $this->assertNull($request->read_at);

        $request->markAsRead();

        $this->assertNotNull($request->fresh()->read_at);
    }

    public function test_unread_scope_returns_only_unread_requests(): void
    {
        ContactRequest::create([
            'name' => 'Unread One',
            'email' => 'unread1@example.com',
            'message' => 'First unread message.',
        ]);

        $read = ContactRequest::create([
            'name' => 'Read One',
            'email' => 'read@example.com',
            'message' => 'This one was already read.',
        ]);
        $read->markAsRead();

        $unread = ContactRequest::unread()->get();

        $this->assertCount(1, $unread);
        $this->assertSame('Unread One', $unread->first()->name);
    }

    public function test_notifier_mark_as_read_and_get_all_notifications(): void
    {
        $admin = User::factory()->create(['email' => 'daprthefox@gmail.com']);

        $request = ContactRequest::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => self::VALID_MESSAGE,
        ]);

        $notifier = app(ContactNotifierInterface::class);

        $notifier->createNotification($request);

        $notifications = $notifier->getAllNotifications();

        $this->assertCount(1, $notifications);
        $this->assertSame('New contact message from John Doe', $notifications[0]['title']);

        $this->assertTrue($notifier->markAsRead($notifications[0]['id']));

        $this->assertTrue($admin->fresh()->notifications()->first()->read());
    }
}
