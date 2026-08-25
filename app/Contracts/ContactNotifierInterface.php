<?php

namespace App\Contracts;

use App\Models\ContactRequest;

interface ContactNotifierInterface
{
    public function createNotification(ContactRequest $request): void;

    public function markAsRead(string $id): bool;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllNotifications(): array;
}
