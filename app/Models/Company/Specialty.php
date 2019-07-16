<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class   Specialty extends Model
{
    protected $table = 'specialties';
    public $timestamps = false;
    public function specialtiesTranslation()
    {
        return $this->hasMany(SpecialtiesTranslation::class);
    }
    public function CompanyIndustry()
    {
        return $this->hasOne(CompanyIndustry::class);
    }
}
