<?php

namespace App\Models\ReReference;

use App\Models\Country\Country;
use Illuminate\Database\Eloquent\Model;


class ReferenceInformation extends Model
{
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function re_reference()
    {
        return $this->belongsTo(ReReference::class);

    }

    protected $fillable = [
        'name',
        'position',
        'organization',
        'mobile',
        'country_id',
        're_reference_id',
        'ref_email_address',
        'preferred_time_to_call',
        'is_available',
        'resume_id'
    ];
}
