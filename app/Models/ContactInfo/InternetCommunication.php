<?php

namespace App\Models\ContactInfo;

use Illuminate\Database\Eloquent\Model;

class InternetCommunication extends Model
{
    public function contactInformation(){
        return $this->belongsTo(ContactInformation::class);
    }
    protected $fillable=[
        'type',
        'address',
        'contact_information_id'
    ];
}
