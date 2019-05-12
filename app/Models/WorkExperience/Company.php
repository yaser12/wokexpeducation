<?php

namespace App\Models\WorkExperience;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function work_experience()
    {
        return $this->belongsTo(WorkExperience::class);
    }

    protected $fillable = [
        'work_experience_id',
        'country',
        'city',
        'name',
        'company_size',
        'company_website',
        'company_description',
        'verified_by_google'
    ];
}
