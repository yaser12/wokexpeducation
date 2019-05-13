<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;

class SkillsTypes extends Model
{
    public function skills(){
        return $this->hasMany(Skills::class);
    }

    public function childs_types(){
        return $this->hasMany('App\Models\Skills\SkillsTypes' , 'parent_id' , 'id');
    }

    public function parents_types(){
        return $this->belongsTo('App\Models\Skills\SkillsTypes' , 'parent_id' , 'id');
    }
}
