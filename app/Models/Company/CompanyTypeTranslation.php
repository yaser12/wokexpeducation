<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyTypeTranslation extends Model
{
    //
    protected $table = 'company_type_translations';
    public $timestamps = false;
    public function CompanyType()
    {
        return $this->belongsTo(CompanyType::class);
    }
}
