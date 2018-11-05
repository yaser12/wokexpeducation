<?php

namespace App\Models\Achievements;
use App\Models\Resume;

use Illuminate\Database\Eloquent\Model;


 
class Achievements extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
