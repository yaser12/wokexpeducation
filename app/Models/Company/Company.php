<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
    public function CompanyTypes()
    {
        return $this->hasMany(CompanyType::class);
    }
    public function CompanySizes()
    {
        return $this->hasMany(CompanySize::class);
    }
    public function companyProfile()
    {
        return $this->hasMany(CompanyProfile::class);
    }
    public function companyIndustriesForCompany()
    {
        return $this->hasMany(CompanyIndustriesForCompany::class);
    }
    public function companySpecialtiesForCompany()
    {
        return $this->hasMany(CompanySpecialtiesForCompany::class);
    }
    public function  companySocialMedia()
    {
        return $this->hasMany(CompanySocialMedia::class);
    }
}
