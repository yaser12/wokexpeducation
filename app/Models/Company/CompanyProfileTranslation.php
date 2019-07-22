<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyProfileTranslation extends Model
{
    protected $table = 'company_profile_translations';

    public $timestamps = false;
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
}
