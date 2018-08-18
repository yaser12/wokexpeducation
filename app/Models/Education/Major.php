<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    public function minors(){
        return $this->hasMany(Minor::class);
    }

    public function education()
    {
        return $this->hasMany(Education::class);
    }
}
