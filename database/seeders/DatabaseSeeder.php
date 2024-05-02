<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '123123123',
        ]);

        User::factory()->create([
            'name' => 'user2',
            'email' => 'user2@mail.com',
            'password' => '123123123',
        ]);

         User::factory()->create([
             'name' => 'user3',
             'email' => 'user3@mail.com',
             'password' => '123123123',
         ]);
    }
}
