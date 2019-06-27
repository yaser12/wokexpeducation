<?php

namespace App\Models\Certifications;

use Illuminate\Database\Eloquent\Model;

class ValidYear extends Model
{
    protected $fillable = ['id'];

    public function certification()
    {
        return $this->hasOne(Certifications::class);
    }

    public function validYearTranslation()
    {
        return $this->hasMany(ValidYearTranslation::class);
    }
}
