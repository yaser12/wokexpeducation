<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class SkillLevel extends Model
{
    public function skill(){
        return $this->hasMany(Skill::class);
    }

    public function skillLevelTranslation(){
        return $this->hasMany(SkillLevelTrans::class);
    }
}
