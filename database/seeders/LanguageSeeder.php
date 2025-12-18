<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'is_active' => true,
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'is_active' => true,
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'is_active' => true,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'is_active' => true, // false inactive language
            ],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                [
                    'name' => $language['name'],
                    'is_active' => $language['is_active'],
                ]
            );
        }
    }
}
