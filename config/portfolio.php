<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portfolio Admin
    |--------------------------------------------------------------------------
    |
    | Credentials used by the database seeder to create the administrator
    | user, and by the contact notifier to resolve the recipient of the
    | admin notifications.
    |
    */

    'admin' => [
        'email' => env('ADMIN_EMAIL', 'daprthefox@gmail.com'),
        'password' => env('ADMIN_PASSWORD', 'asdf1234'),
    ],

];
