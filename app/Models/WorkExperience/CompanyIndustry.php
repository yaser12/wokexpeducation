<?php

namespace App\Models\WorkExperience;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Specialty;
class CompanyIndustry extends Model
{
    public function work_experience()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function companyIndustryTranslation()
    {
        return $this->hasMany(CompanyIndustryTranslation::class);
    }
    public function specialties() // from yaser 15-7-2019
    {
        return $this->hasMany(Specialty::class);
    }
    public function companyIndustryParent()
    {
        return $this->belongsTo(CompanyIndustryParent::class);
    }
}
