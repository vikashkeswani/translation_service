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
        $languages = Language::paginate(10);
        return new LanguageCollection($languages);
    }

    public function store(LanguageRequest $request)
    {
        return new LanguageResource(Language::create($request->validated()));
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

        Cache::forget('translations.export');

        return new LanguageResource($language);
    }
}
