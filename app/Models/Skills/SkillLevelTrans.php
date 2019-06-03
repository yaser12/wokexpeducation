<?php

namespace App\Models\Skills;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class SkillLevelTrans extends Model
{
    protected $fillable = ['skill_level_id','translated_languages_id','name'];

    public function skillLevel(){
        return $this->belongsTo(SkillLevel::class);
    }

    public function translatedLanguages(){
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
