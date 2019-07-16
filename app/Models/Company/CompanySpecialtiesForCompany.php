<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanySpecialtiesForCompany extends Model
{
    protected $table = 'company_specialties_for_company';

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
    public function Specialty()
    {
        return $this->hasMany(Specialty::class);
    }
}
