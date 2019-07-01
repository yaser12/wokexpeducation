<?php

namespace App\Models\ReReference;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class ReReference extends Model
{
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function reference_info()
    {
        return $this->hasMany(ReferenceInformation::class);
    }


    protected $fillable = [

        'is_available',
        'resume_id'
    ];
}
