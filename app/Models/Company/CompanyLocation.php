<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    protected $table = 'company_location';

    public $timestamps = false;
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }


}
