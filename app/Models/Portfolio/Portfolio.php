<?php

namespace App\Models\Portfolio;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
