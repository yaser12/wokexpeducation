<?php

namespace App\Models\Certifications;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Certifications extends Model
{
    protected $dates = ['date'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
