<?php

namespace App\Models\TranslatedLanguages;

use App\Models\ConferencesWorkshopSeminar\ConferenceTypeTranslation;
use App\Models\ContactInfo\PhoneTypeTranslation;
use App\Models\Country\CountryTranslation;
use App\Models\Education\DegreeLevelTranslation;
use App\Models\Education\MajorParentTranslation;
use App\Models\Education\MajorTranslation;
use App\Models\Education\MinorTranslation;
use App\Models\Education\UniversityTranslation;
use App\Models\Language\InternationalLanguageTrans;
use App\Models\Language\SelfAssessmentTrans;
use App\Models\PersonalInformation\MaritalStatusTranslation;
use App\Models\PersonalInformation\NationalityTranslation;
use App\Models\Resume;
use App\Models\WorkExperience\CompanyIndustryParentTrans;
use App\Models\WorkExperience\CompanyIndustryTranslation;
use App\Models\WorkExperience\EmpTypeParentTranslation;
use Illuminate\Database\Eloquent\Model;

class TranslatedLanguages extends Model
{
    protected $fillable = ['name'];

    public function resume()
    {
        return $this->hasMany(Resume::class);
    }

    public function maritalStatusTranslation()
    {
        return $this->hasMany(MaritalStatusTranslation::class);
    }

    public function nationalityTranslation()
    {
        return $this->hasMany(NationalityTranslation::class);
    }

    public function phoneTypeTranslation()
    {
        return $this->hasMany(PhoneTypeTranslation::class);
    }

    public function universityTranslation()
    {
        return $this->hasMany(UniversityTranslation::class);
    }

    public function majorTranslation()
    {
        return $this->hasMany(MajorTranslation::class);
    }

    public function minorTranslation()
    {
        return $this->hasMany(MinorTranslation::class);
    }

    public function degreeLevelTranslation()
    {
        return $this->hasMany(DegreeLevelTranslation::class);
    }

    public function companyIndustryTranslation()
    {
        return $this->hasMany(CompanyIndustryTranslation::class);
    }

    public function companyIndustryParentTranslation()
    {
        return $this->hasMany(CompanyIndustryParentTrans::class);
    }

    public function empTypeTranslation()
    {
        return $this->hasMany(EmpTypeParentTranslation::class);
    }

    public function countryTranslation()
    {
        return $this->hasMany(CountryTranslation::class);
    }

    public function conferenceTypeTranslation()
    {
        return $this->hasMany(ConferenceTypeTranslation::class);
    }

    public function internationalLanguageTranslation()
    {
        return $this->hasMany(InternationalLanguageTrans::class);
    }

    public function selfAssessmentTranslation()
    {
        return $this->hasMany(SelfAssessmentTrans::class);
    }

    public function majorParentTranslation()
    {
        return $this->hasMany(MajorParentTranslation::class);
    }
}
