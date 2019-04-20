<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use App\Models\Resume;

class Membership extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
