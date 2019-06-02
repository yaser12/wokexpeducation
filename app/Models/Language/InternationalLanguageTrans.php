<?php

namespace App\Models\Language;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class InternationalLanguageTrans extends Model
{
    protected $fillable=['international_language_id','translated_languages_id','name'];

    public function internationalLanguage(){
        return $this->belongsTo(InternationalLanguage::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
