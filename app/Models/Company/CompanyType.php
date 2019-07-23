<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    //
    protected $table = 'company_types';
    public $timestamps = false;
    public function companyTypeTranslation()
    {
        return $this->hasMany(CompanyTypeTranslation::class);
    }
    public function company()
    {
        return $this->hasOne(Company::class);
    }
}
