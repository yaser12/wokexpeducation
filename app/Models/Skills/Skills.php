<?php

namespace App\Models\Skills;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Skills extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }

    public function skills_types(){
        return $this->belongsTo(SkillsTypes::class);
    }
    protected $fillable = ['skills_types_id'];
}
