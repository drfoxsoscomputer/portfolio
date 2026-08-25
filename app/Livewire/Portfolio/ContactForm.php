<?php

namespace App\Livewire\Portfolio;

use App\Contracts\ContactNotifierInterface;
use App\Models\ContactRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    /**
     * Maximum number of submissions allowed per IP within the rate limit window.
     */
    private const MAX_ATTEMPTS = 5;

    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $sent = false;

    /**
     * Validate, store the contact request and notify the admin.
     */
    public function submit(ContactNotifierInterface $notifier): void
    {
        $this->ensureIsNotRateLimited();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $request = ContactRequest::create($validated);

        RateLimiter::hit($this->rateLimitKey());

        $notifier->createNotification($request);

        $this->reset('name', 'email', 'message');
        $this->sent = true;
    }

    /**
     * Guard against spam by limiting submissions per client IP.
     */
    private function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->rateLimitKey(), self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'message' => __('Too many messages sent. Please try again later.'),
            ]);
        }
    }

    /**
     * Build the rate limiter bucket key for the current client.
     */
    private function rateLimitKey(): string
    {
        return 'contact-form:'.(request()->ip() ?? 'unknown');
    }

    public function render(): View
    {
        return view('livewire.portfolio.contact-form');
    }
}
