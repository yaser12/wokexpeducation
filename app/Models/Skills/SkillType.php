<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class SkillType extends Model
{
    public function skill_type_parents(){
        return $this->belongsTo(SkillTypeParent::class);
    }

    public function skill(){
        return $this->hasMany(Skill::class);
    }

}
