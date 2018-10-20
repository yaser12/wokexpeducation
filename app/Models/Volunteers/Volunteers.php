<?php

namespace App\Models\Volunteers;

use Illuminate\Database\Eloquent\Model;

class Volunteers extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
