<?php

namespace App\Models\Volunteers;

use Illuminate\Database\Eloquent\Model;
use App\Models\Resume;

class Volunteers extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
