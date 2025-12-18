<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Translation;
use Illuminate\Support\LazyCollection;

class ExportController extends Controller
{
    public function __invoke()
    {
        return response()->stream(function () {
            echo json_encode($this->getTranslations());
        }, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store',
        ]);
    }

    // Extracted method
    public function getTranslations(): array
    {
        return Cache::remember(
            'translations.export.active',
            now()->addMinutes(30),
            function () {
                return Translation::with(['language', 'tag'])
                    ->whereHas('language', fn ($q) => $q->where('is_active', true))
                    ->cursor()
                    ->map(function ($translation) {
                        return [
                            'id' => $translation->id,
                            'key' => $translation->key,
                            'value' => $translation->value,
                            'created_at' => $translation->created_at?->toDateTimeString(),
                            'updated_at' => $translation->updated_at?->toDateTimeString(),

                            'language' => [
                                'id' => $translation->language->id,
                                'code' => $translation->language->code,
                                'name' => $translation->language->name,
                            ],
                            'tag' => [
                                'id' => $translation->tag->id,
                                'name' => $translation->tag->name,
                            ],
                        ];
                    })
                    ->all();
        });
    }

}
