<?php

namespace App\Models\DrivingLicense;

use App\Models\Country\Country;
use App\Models\DrivingCategory\DrivingCategory;
use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Driving extends Model
{
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function categories()
    {
        return $this->hasMany(DrivingCategory::class,'driving_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    protected $fillable = [
        'country_id'

    ];
}
