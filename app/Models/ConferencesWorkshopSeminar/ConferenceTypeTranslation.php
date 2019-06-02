<?php

namespace App\Models\ConferencesWorkshopSeminar;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class ConferenceTypeTranslation extends Model
{
    protected $fillable = ['conference_type_id', 'translated_languages_id', 'name'];

    public function conferenceType()
    {
        return $this->belongsTo(ConferenceType::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
