<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanySpecialtiesForCompany extends Model
{
    public $timestamps = false;
    protected $table = 'company_specialties_for_company';


    public function Specialty()
    {
        return $this->hasMany(Specialty::class);
    }
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
}
