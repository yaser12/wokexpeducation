<?php

namespace App\Models\Certifications;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class ValidYearTranslation extends Model
{
    protected $fillable = ['valid_year_id', 'translated_languages_id', 'name'];

    public function validYear()
    {
        return $this->belongsTo(ValidYear::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
