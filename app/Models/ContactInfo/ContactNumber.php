<?php

namespace App\Models\ContactInfo;

use Illuminate\Database\Eloquent\Model;

class ContactNumber extends Model
{
    public function contactInformation(){
        return $this->belongsTo(ContactInformation::class);
    }
    protected $fillable=[
        'phone_type',
        'country_code',
        'phone_number',
        'contact_information_id'
    ];
}
