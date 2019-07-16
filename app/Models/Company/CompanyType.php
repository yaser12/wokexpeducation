<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    //
    protected $table = 'company_types';
    public $timestamps = false;
    public function CompanyTypeTranslation()
    {
        return $this->hasMany(CompanyTypeTranslation::class);
    }
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
}
