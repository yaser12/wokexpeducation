<?php

namespace App\Models\ConferencesWorkshopSeminar;

use Illuminate\Database\Eloquent\Model;
use App\Models\Resume;

class ConferencesWorkshopSeminar extends Model
{
    protected $dates=['date'];
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
