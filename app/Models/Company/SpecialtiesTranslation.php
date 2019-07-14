<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class SpecialtiesTranslation extends Model
{
    protected $table = 'specialties_translation';
    public $timestamps = false;
    public function specialty()
    {
        return $this->hasOne(Specialty::class);
    }
}
