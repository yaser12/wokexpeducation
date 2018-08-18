<?php

namespace App\Models\Education;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function minor()
    {
        return $this->belongsTo(Minor::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function projects()
    {
        return $this->hasMany(EducationProject::class);
    }
}
