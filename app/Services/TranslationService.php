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
        
        // if (!empty($data['tag'])) {
        //     $tags = collect($data['tag'])->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id);
        //     $translation->tag()->sync($tags);
        // }

        $this->clearCache($translation);
        
        return $translation;
    }


    public function update(Translation $translation, array $data): Translation
    {
        $translation->update($data);

        $this->clearCache($translation);

        $translation->load(['language', 'tag']);

        return $translation;
    }

    public function delete(Translation $translation)
    {
        $response = $translation->delete();
        $this->clearCache($translation);
    }

     protected function clearCache(Translation $translation): void
     {
         Cache::forget("translations.show.{$translation->id}");
         Cache::forget('translations.export.active');
         Cache::tags(['translations'])->flush();
     }

}