<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyIndustriesForCompany extends Model
{
    public $timestamps = false;
    protected $table = 'company_industries_for_company';

    public function companyIndustry()
    {
        return $this->hasMany(CompanyIndustry::class);
    }
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
}
