<?php

namespace App\Models\ObjectiveSec;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }

    protected $fillable =[
        'resume_id',
        'description',
    ];
}
