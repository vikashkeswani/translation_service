<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Language;
use App\Models\Tag;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'language_id','tag_id'];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

}
