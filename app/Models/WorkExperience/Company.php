<?php

namespace App\Models\WorkExperience;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function work_experience()
    {
        return $this->hasMany(WorkExperience::class);
    }
}
