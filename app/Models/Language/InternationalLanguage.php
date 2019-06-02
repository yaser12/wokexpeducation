<?php

namespace App\Models\Language;

use Illuminate\Database\Eloquent\Model;

class InternationalLanguage extends Model
{
    public function language(){
        return $this->hasMany(Language::class);

    }

    public function internationalLanguageTrans(){
        return $this->hasMany(InternationalLanguageTrans::class);
    }
}
