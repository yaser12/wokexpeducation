<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class MajorGroup extends Model
{
    public function majors(){
        return $this->hasMany(Major::class);
    }
}
