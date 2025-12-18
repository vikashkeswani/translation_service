<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $version = $request->query('v', '1');

        return response()->stream(function () {
            echo '[';

            $first = true;

            Translation::query()
                ->select([
                    'translations.id',
                    'translations.key',
                    'translations.value',
                    'translations.language_id',
                    'translations.tag_id',
                    'tags.name as tag_name',
                    'translations.created_at',
                    'translations.updated_at',
                ])
                ->join('languages', 'languages.id', '=', 'translations.language_id')
                ->leftJoin('tags', 'tags.id', '=', 'translations.tag_id')
                ->where('languages.is_active', true)
                ->orderBy('translations.id')
                ->chunk(1000, function ($rows) use (&$first) {
                    foreach ($rows as $row) {
                        if (!$first) {
                            echo ',';
                        }

                        echo json_encode([
                            'id' => $row->id,
                            'key' => $row->key,
                            'value' => $row->value,
                            'language' => [
                                'id' => $row->language_id,
                            ],
                            'tag' => $row->tag_id ? [
                                'id' => $row->tag_id,
                            ] : null,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);

                        $first = false;
                    }
                });

            echo ']';
        }, 200, [
            'Content-Type'        => 'application/json',
            'Cache-Control'       => 'public, max-age=3600',
            'CDN-Cache-Control'   => 'public, max-age=86400',
        ]);
    }
}
