<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\LanguageCollection;
use App\Http\Resources\LanguageResource;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Cache::remember(
            'languages.list',
            now()->addHours(1),
            fn () => Language::paginate(10)
        );

        return new LanguageCollection($languages);
    }

    public function store(LanguageRequest $request)
    {
         $language = Language::create($request->validated());
         Cache::forget('languages.list');
         Cache::forget('translations.export.active');
         return new LanguageResource($language);
    }

    public function toggle($id)
    {
        $language = Language::find($id);

         if (!$language) {
            return response()->json([
                'message' => 'Record does not exist',
            ], 404);
        }

        $language->update([
            'is_active' => ! $language->is_active,
        ]);

        // Cache::forget('translations.export');
        Cache::forget('languages.list');
        Cache::forget('translations.export.active');
        Cache::forget('translations.list');

        return new LanguageResource($language);
    }
}
