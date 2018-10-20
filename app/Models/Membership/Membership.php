<?php

namespace App\Model\Membership;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
