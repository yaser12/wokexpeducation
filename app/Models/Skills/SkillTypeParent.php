<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class SkillTypeParent extends Model
{
    protected $fillable = ['skill_type_basic_parent_id','id'];
    public function skill_types(){
        return $this->hasMany(SkillType::class);
    }


    public function skillTypeParentTrans(){
        return $this->hasMany(SkillTypeParentTrans::class);
    }
    public function skillTypeBasicParent(){
        return $this->belongsTo(SkillTypeBasicParent::class);
    }
}
