<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ProfileSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'daprthefox@gmail.com',
            'password' => 'asdf1234',
        ]);

        $this->call([ProfileSeeder::class]);
    }
}
