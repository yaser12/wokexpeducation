<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyIndustriesForCompany extends Model
{
    protected $table = 'company_industries_for_company';
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
    public function companyIndustry()
    {
        return $this->hasMany(CompanyIndustry::class);
    }
}
