<?php

namespace App\Models\SummarySec;

use Illuminate\Database\Eloquent\Model;
use App\Models\Resume;
class Summary extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }
    protected $fillable =[
        'resume_id',
        'description',
    ];
}
