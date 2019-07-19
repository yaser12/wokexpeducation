<?php

namespace App\Models\Company;

use App\Models\TranslatedLanguages\TranslatedLanguages;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{

    protected $table = 'company_profiles';

    public $timestamps = false;
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
    public function translatedLanguages()
    {
        return $this->belongsTo(TranslatedLanguages::class);
    }
}
