<?php

namespace App\Models\Language;

use Illuminate\Database\Eloquent\Model;

class LanguageAssessment extends Model
{
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    public function selfAssessment()
    {
        return $this->belongsTo(SelfAssessment::class);
    }
    protected $fillable = ['assessment_type','language_id','assessment_id'];

}
