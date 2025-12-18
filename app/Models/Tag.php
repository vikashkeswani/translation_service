<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Translation;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}
