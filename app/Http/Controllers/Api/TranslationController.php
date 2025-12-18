<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Translation;
use App\Services\TranslationService;
use App\Http\Requests\TranslationRequest;
use App\Http\Requests\TranslationSearchRequest;
use App\Http\Resources\TranslationCollection;
use App\Http\Resources\TranslationResource;
use Exception;

class TranslationController extends Controller
{
    public function __construct(private TranslationService $service) {}

    public function index()
    {
        $translations = Translation::with(['language', 'tag'])->paginate(20);
        return new TranslationCollection($translations);
    }

    public function show($id) 
    {
        $translation = Translation::with(['language', 'tag'])
                        ->find($id);

        if (! $translation) {
            return response()->json([
                'message' => 'Record does not exist',
            ], 404);
        }

        return new TranslationResource($translation);
    }

    public function store(TranslationRequest $request)
    {
        try{

            $translation = $this->service->create($request->validated());
            return new TranslationResource($translation);

        }catch(Exception $e){
             return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(TranslationRequest $request, $id)
    {
        $translation = Translation::find($id);
        
        if (!$translation) {
            return response()->json([
                'message' => 'Record does not exist',
            ], 404);
        }

        return new TranslationResource($this->service->update($translation, $request->validated()));
    }

    public function destroy($id)
    {   
        $translation = Translation::find($id);

         if (!$translation) {
            return response()->json([
                'message' => 'Record does not exist',
            ], 404);
        }
        
        $translation->delete();
        return response()->json(['message' => 'Record Deleted Successfully!']);
    }

    public function search(TranslationSearchRequest $request)
    {
        $translations = Translation::with(['language', 'tag'])
                        ->whereHas('language', fn ($q) => $q->where('is_active', true))
                        ->when($request->key, fn ($q) => $q->where('key', 'like', "%{$request->key}%"))
                        ->when($request->value, fn ($q) => $q->where('value', 'like', "%{$request->value}%"))
                        ->when($request->tag, fn ($q) => $q->whereHas('tag', fn ($t) => $t->where('name', $request->tag)))
                        ->paginate(20);

        return new TranslationCollection($translations);
        
    }
}
