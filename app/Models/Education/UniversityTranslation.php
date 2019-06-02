<?php

namespace App\Models\Education;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class UniversityTranslation extends Model
{
    protected $fillable = ['university_id', 'translated_languages_id', 'name'];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
