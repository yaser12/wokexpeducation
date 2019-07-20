<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
    protected $table = 'company_location';

}
