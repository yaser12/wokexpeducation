<?php

namespace App\Models\Language;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class SelfAssessmentTrans extends Model
{
    public function selfAssessment()
    {
        return $this->belongsTo(SelfAssessment::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
