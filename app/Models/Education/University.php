<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    public function education()
    {
        return $this->hasMany(Education::class);
    }
}
