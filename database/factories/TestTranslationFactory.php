<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\Language;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        return [
            'key'         => 'translation_' . $this->faker->unique()->numberBetween(1, 10000),
            'value'       => $this->faker->sentence(3),
            'language_id' => Language::factory(), // ensures a language exists
        ];
    }

    /**
     * Attach tags after creating the translation
     */
    public function configure()
    {
        return $this->afterCreating(function (Translation $translation) {
            // Attach 1-3 random tags
            $tags = Tag::factory()->count(rand(1, 3))->create();
            $translation->tags()->attach($tags->pluck('id')->toArray());
        });
    }
}
