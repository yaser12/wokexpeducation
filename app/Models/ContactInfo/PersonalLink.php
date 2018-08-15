<?php

namespace App\Models\ContactInfo;

use Illuminate\Database\Eloquent\Model;

class PersonalLink extends Model
{
    public function contactInformation(){
        return $this->belongsTo(ContactInformation::class);
    }

    protected $fillable=[
        'type',
        'url',
        'contact_information_id'
    ];
}
