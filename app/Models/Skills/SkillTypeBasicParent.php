<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class SkillTypeBasicParent extends Model
{
    public function skillTypeParents(){
        return $this->hasMany(SkillTypeParent::class);
    }


    public function skillTypeBasicParentTrans(){
        return $this->hasMany(SkillTypeBasicParentTrans::class);
    }
}
