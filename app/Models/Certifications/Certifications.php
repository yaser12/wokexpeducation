<?php

namespace App\Models\Certifications;

use App\Models\Country\Country;
use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Certifications extends Model
{
    protected $dates = ['date'];
    protected $fillable = ['valid_year_id',];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
    public function validYear()
    {
        return $this->belongsTo(ValidYear::class);
    }
}
