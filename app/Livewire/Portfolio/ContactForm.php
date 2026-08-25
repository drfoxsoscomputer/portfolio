<?php

namespace App\Livewire\Portfolio;

use App\Contracts\ContactNotifierInterface;
use App\Models\ContactRequest;
use Illuminate\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $sent = false;

    /**
     * Validate, store the contact request and notify the admin.
     */
    public function submit(ContactNotifierInterface $notifier): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $request = ContactRequest::create($validated);

        $notifier->createNotification($request);

        $this->reset('name', 'email', 'message');
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.portfolio.contact-form');
    }
}
