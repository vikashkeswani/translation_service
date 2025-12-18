<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;
use App\Models\Language;
use App\Models\Tag;
use Faker\Factory as Faker;

class TranslationSeeder extends Seeder
{
    private static int $counter = 1;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $languages = Language::pluck('id')->all();
        $tags = Tag::pluck('id')->all();

        if (empty($languages) || empty($tags)) {
            $this->command->info('No languages or tags found. Please seed them first.');
            return;
        }

        // Get count from command line argument or default to 50
        $count = $this->command->ask('How many translations do you want to seed?', 50);

        for ($i = 0; $i < $count; $i++) {
            Translation::create([
                'key'         => 'translation_' . self::$counter++,
                'value'       => $faker->word,
                'language_id' => $faker->randomElement($languages),
                'tag_id'      => $faker->randomElement($tags),
            ]);
        }

        $this->command->info("$count translations seeded successfully!");
    }
}
