<?php

namespace App\Models\Language;

use Illuminate\Database\Eloquent\Model;
use App\Models\Diploma\Diploma;
use App\Models\Resume;

class Language extends Model
{
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function diplomas()
    {
        return $this->hasMany(Diploma::class);
    }
}
