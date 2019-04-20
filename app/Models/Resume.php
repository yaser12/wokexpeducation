<?php

namespace App\Models;

use App\Models\Achievements\Achievements;
use App\Models\ContactInfo\ContactInformation;
use App\Models\DrivingLicense\Driving;
use App\Models\Education\Education;
use App\Models\Membership\Membership;
use App\Models\ObjectiveSec\Objective;
use App\Models\PersonalInformation\PersonalInformation;
use App\Models\Projects\Projects;
use App\Models\Publications\Publications;
use App\Models\SummarySec\Summary;
use App\Models\Language\Language;
use App\Models\Volunteers\Volunteers;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{


    //Model Events
    protected static function boot(){

        parent::boot();
        
        //When Deleting A Resume 
        static::deleting(function($resume){

            //delete each achievement associated with it 
            $resume->achievements->each->delete();
        });
    }
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
    public function memberships(){
        return $this->hasMany(Membership::class);
    }
    public function projects(){
    return $this->hasMany(Projects::class);
    }
    public function publications(){
        return $this->hasMany(Publications::class);
    }
    public function volunteers(){
        return $this->hasMany(Volunteers::class);
    }

    protected $fillable=['user_id'];
}
