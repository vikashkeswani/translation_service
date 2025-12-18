<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Translation;
use App\Models\Language;
use App\Models\Tag;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    protected static $counter = 1; // ensures unique keys

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'key' => 'translation_' . self::$counter++, //$this->faker->unique()->word,
            // 'value' => $this->faker->word,
            // 'language_id' => Language::inRandomOrder()->value('id'),
            // 'tag_id' => Tag::inRandomOrder()->value('id'),

            'key'         => 'translation_' . self::$counter++, //'translation_'.$this->faker->unique()->numberBetween(1, 1000),
            'value'       => $this->faker->sentence(3),
            // 'language_id' => Language::factory(),
            // 'tag_id'      => Tag::factory(), // <-- assign a tag

            'tag_id' => Tag::inRandomOrder()->first()->id ?? Tag::factory(),
            'language_id' => Language::inRandomOrder()->first()->id ?? Language::factory(),
        ];
    }
}
