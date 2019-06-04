<?php

namespace App\Models\Skills;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class SkillTypeBasicParentTrans extends Model
{
    protected $fillable = ['skill_type_basic_parent_id','translated_languages_id','name'];

    public function skillTypeBasicParent(){
        return $this->belongsTo(SkillTypeBasicParent::class);
    }

    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
