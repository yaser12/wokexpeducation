<?php

namespace App\Models\ReReference;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class ReReference extends Model
{
    public function resume(){
        return $this->belongsTo(Resume::class);
    }

    protected $fillable=[
        'name',
        'position',
        'organization',
        'mobile',
        'country-code',
        'ref_email_address',
        'prefered_time_to_call',
        'is_available',
        'order',
        'resume_id'
    ];
}
