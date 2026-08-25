<?php

namespace App\Services;

use App\Contracts\ContactNotifierInterface;
use App\Models\ContactRequest;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class NotificationContactNotifier implements ContactNotifierInterface
{
    /**
     * Send a database notification to the admin about a new contact request.
     */
    public function createNotification(ContactRequest $request): void
    {
        $admin = $this->admin();

        if ($admin === null) {
            return;
        }

        Notification::make()
            ->title('New contact message from '.$request->name)
            ->body(Str::limit($request->message, 200))
            ->sendToDatabase($admin);
    }

    /**
     * Mark a database notification as read.
     */
    public function markAsRead(string $id): bool
    {
        $admin = $this->admin();

        if ($admin === null) {
            return false;
        }

        $notification = $admin->notifications()->find($id);

        if ($notification === null) {
            return false;
        }

        $notification->markAsRead();

        return true;
    }

    /**
     * Get all admin notifications as plain arrays.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllNotifications(): array
    {
        $admin = $this->admin();

        if ($admin === null) {
            return [];
        }

        return $admin->notifications()
            ->get()
            ->map(fn ($notification): array => [
                'id' => $notification->id,
                'title' => data_get($notification->data, 'title', ''),
                'body' => data_get($notification->data, 'body', ''),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ])
            ->all();
    }

    /**
     * Resolve the admin user (by configured email, falling back to the first user).
     */
    private function admin(): ?User
    {
        return User::where('email', config('portfolio.admin.email'))->first() ?? User::first();
    }
}
