<?php

namespace App\Models;

use App\Models\Achievements\Achievements;
use App\Models\ContactInfo\ContactInformation;
use App\Models\DrivingLicense\Driving;
use App\Models\Education\Education;
use App\Models\ObjectiveSec\Objective;
use App\Models\PersonalInformation\PersonalInformation;
use App\Models\SummarySec\Summary;
use App\Models\Language\Language;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function personalInformation(){
        return $this->hasOne(PersonalInformation::class);
    }
    public function objective(){
        return $this->hasOne(Objective::class);
    }
    public function summary(){
        return $this->hasOne(Summary::class);
    }
    public function contactInformation(){
        return $this->hasOne(ContactInformation::class);
    }
    public function languages(){
        return $this->hasMany(Language::class);
    }

    public function educations(){
        return $this->hasMany(Education::class);
    }

    public function drivingLicense(){
        return $this->hasMany(Driving::class);
    }

    public function achievements(){
        return $this->hasMany(Achievements::class);
    }

    protected $fillable=['user_id'];
}
