<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
