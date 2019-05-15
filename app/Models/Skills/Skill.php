<?php

namespace App\Models\Skills;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }

    public function skill_types(){
        return $this->belongsTo(SkillType::class);
    }
}
