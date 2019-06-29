<?php

namespace App\Models\Skills;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }

    public function skill_type(){
        return $this->belongsTo(SkillType::class);
    }

    public function skillLevel(){
        return $this->belongsTo(SkillLevel::class);
    }
}
