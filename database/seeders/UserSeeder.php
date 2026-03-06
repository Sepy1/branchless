<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create or update an admin user
        User::updateOrCreate(
            ['email' => 'it@bkkjateng.co.id'],
            [
                'name' => 'itbkk',
                'password' => bcrypt('j@t3ng!@#'),
                'email_verified_at' => now(),
            ]
        );

        // Create additional dummy users via factory
        User::factory()->count(9)->create();
    }
}
