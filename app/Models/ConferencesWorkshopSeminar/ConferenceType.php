<?php

namespace App\Models\ConferencesWorkshopSeminar;

use Illuminate\Database\Eloquent\Model;

class ConferenceType extends Model
{
    protected $fillable = ['id'];

    public function conferencesWorkshopSeminar()
    {
        return $this->hasOne(ConferencesWorkshopSeminar::class);
    }

    public function conferenceTypeTranslation()
    {
        return $this->hasMany(ConferenceTypeTranslation::class);
    }
}
