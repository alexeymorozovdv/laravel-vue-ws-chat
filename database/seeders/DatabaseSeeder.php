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
            'name' => 'Alex',
            'email' => 'user@mail.com',
            'password' => '123123123',
        ]);

        User::factory()->create([
            'name' => 'Oleg',
            'email' => 'oleg@mail.com',
            'password' => '123123123',
        ]);

         User::factory()->create([
             'name' => 'Masha',
             'email' => 'masha@mail.com',
             'password' => '123123123',
         ]);
    }
}
