<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
    public function CompanyTypes()
    {
        return $this->hasMany(CompanyType::class);
    }
    public function CompanySizes()
    {
        return $this->hasMany(CompanySize::class);
    }

}
