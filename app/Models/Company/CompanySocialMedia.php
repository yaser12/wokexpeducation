<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanySocialMedia extends Model
{
    protected $table = 'company_social_media';

    public $timestamps = false;
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
    public function socialMedia()
    {
        return $this->hasOne(SocialMedia::class);
    }

}
