<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = 'companies';

    public $timestamps = false;
    public function companyType()
    {
        return $this->belongsTo(CompanyType::class);
    }
    public function CompanySizes()
    {
        return $this->belongsTo(CompanySize::class);
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
