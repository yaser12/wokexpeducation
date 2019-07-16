<?php

namespace App\Models\WorkExperience;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;

class CompanySize extends Model
{
    public function work_exp_company()
    {
        return $this->hasMany(WorkExpCompany::class);
    }

    public function company_size_translation()
    {
        return $this->hasMany(CompanySizeTranslation::class);
    }
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
}
