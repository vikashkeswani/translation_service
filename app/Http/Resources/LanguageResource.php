<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'         => $this->id,
            'code'       => $this->code,
            'name'       => $this->name,
            'is_active' => (int) $this->is_active === 1 ? true : false,
            // 'created_at'=> $this->created_at?->format('Y-m-d H:i:s'),
            // 'updated_at'=> $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
