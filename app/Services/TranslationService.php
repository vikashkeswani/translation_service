<?php


namespace App\Services;


use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;


class TranslationService
{
    public function create(array $data): Translation    
    {
        $translation = Translation::create($data);

        if (!empty($data['tag'])) {
            $tags = collect($data['tag'])->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id);
            $translation->tag()->sync($tags);
        }

        Cache::forget('translations.export');

        return $translation;
    }


    public function update(Translation $translation, array $data): Translation
    {
        $translation->update($data);

        Cache::forget('translations.export');

        $translation->load(['language', 'tag']);

        return $translation;
    }

}