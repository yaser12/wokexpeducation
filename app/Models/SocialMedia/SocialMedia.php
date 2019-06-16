<?php

namespace App\Models\SocialMedia;

use App\Models\ContactInfo\PersonalLink;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    protected $fillable = ['id', 'name'];

    public function personalLink()
    {
        return $this->hasMany(PersonalLink::class);
    }

}
