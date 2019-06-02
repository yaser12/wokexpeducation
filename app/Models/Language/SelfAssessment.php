<?php

namespace App\Models\Language;

use Illuminate\Database\Eloquent\Model;

class SelfAssessment extends Model
{
    public function languageAssessment()
    {
        return $this->hasMany(LanguageAssessment::class);
    }

    public function selfAssessmentTrans()
    {
        return $this->hasMany(SelfAssessmentTrans::class);
    }
}
