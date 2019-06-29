<?php

namespace App\Models\Skills;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class SkillTypeTrans extends Model
{
    protected $fillable = ['skill_type_id','translated_languages_id','name'];

    public function skill_type(){
        return $this->belongsTo(SkillType::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
