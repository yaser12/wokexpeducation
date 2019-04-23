<?php

namespace App\Models\HobbiesInterest;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class HobbiesInterest extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }

    protected  $fillable = [
        'description',
        'resume_id'
    ];
}
