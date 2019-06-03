<?php

namespace App\Models\Skills;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class SkillTypeParentTrans extends Model
{
    protected $fillable = ['skill_type_parent_id','translated_languages_id','name'];

    public function skill_type_parents(){
        return $this->belongsTo(SkillTypeParent::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
