<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
         $this->call([
            LanguageSeeder::class,
            TagSeeder::class,
            TranslationSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'test User',
            'email' => 'test@example.com',
            'password' => Hash::make('test@123'),
        ]);
    }
}
