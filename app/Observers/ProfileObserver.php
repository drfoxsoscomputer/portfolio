<?php

namespace App\Observers;

use App\Models\Profile;

class ProfileObserver
{
    /**
     * Handle the Profile "creating" event.
     *
     * This observer enforces the singleton pattern by preventing
     * the creation of a second Profile record. The first Profile
     * is considered the canonical portfolio, and any subsequent
     * attempts to create a Profile will be rejected.
     *
     * @param  Profile  $profile
     * @return void
     * @throws \Exception
     */
    public function creating(Profile $profile): void
    {
        if (Profile::exists()) {
            throw new \Exception('Profile already exists - portfolio singleton enforced');
        }
    }

    /**
     * Handle the Profile "created" event.
     *
     * This event handler can be used for additional logic
     * that should execute after a Profile is successfully created.
     *
     * @param  Profile  $profile
     * @return void
     */
    public function created(Profile $profile): void
    {
        // Can be used for post-creation logic if needed
    }
}