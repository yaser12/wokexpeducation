<?php

namespace App\Models\Publications;

use Illuminate\Database\Eloquent\Model;

class Publications extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
