<?php

namespace App\Models\Achievements;

use Illuminate\Database\Eloquent\Model;

use App\Models\Resume ;
 
class Achievements extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
