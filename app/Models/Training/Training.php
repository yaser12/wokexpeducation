<?php

namespace App\Models\Training;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $dates=['from', 'to'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
