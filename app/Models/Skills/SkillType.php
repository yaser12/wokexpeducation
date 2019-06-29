<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class SkillType extends Model
{
    protected $fillable = ['skill_type_parent_id','id'];
    public function skill(){
        return $this->hasMany(Skill::class);
    }

    public function skill_type_parent(){
        return $this->belongsTo(SkillTypeParent::class);
    }

    public function skillTypeTrans(){
        return $this->hasMany(SkillTypeTrans::class);
    }


}
