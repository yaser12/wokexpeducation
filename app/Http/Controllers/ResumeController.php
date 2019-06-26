<?php

namespace App\Http\Controllers;

use App\Models\Achievements\Achievements;
use App\Models\Certifications\Certifications;
use App\Models\ConferencesWorkshopSeminar\ConferencesWorkshopSeminar;
use App\Models\ContactInfo\ContactInformation;
use App\Models\ContactInfo\ContactNumber;
use App\Models\ContactInfo\Email;
use App\Models\ContactInfo\InternetCommunication;
use App\Models\ContactInfo\PersonalLink;
use App\Models\Diploma\Diploma;
use App\Models\DrivingCategory\DrivingCategory;
use App\Models\DrivingLicense\Driving;
use App\Models\Education\Education;
use App\Models\Education\EducationProject;
use App\Models\HobbiesInterest\HobbiesInterest;
use App\Models\Language\Language;
use App\Models\Language\LanguageAssessment;
use App\Models\Membership\Membership;
use App\Models\ObjectiveSec\Objective;
use App\Models\PersonalInformation\CurrentLocation;
use App\Models\PersonalInformation\Nationality;
use App\Models\PersonalInformation\PersonalInformation;
use App\Models\PersonalInformation\PlaceOfBirth;
use App\Models\Portfolio\Portfolio;
use App\Models\Projects\Projects;
use App\Models\Publications\Publications;
use App\Models\ReReference\ReReference;
use App\Models\Skills\Skill;
use App\Models\SummarySec\Summary;
use App\Models\Training\Training;
use App\Models\TranslatedLanguages\TranslatedLanguages;
use App\Models\Volunteers\Volunteers;
use App\Models\WorkExperience\WorkExpCompany;
use App\Models\WorkExperience\EmploymentType;
use App\Models\WorkExperience\WorkExperience;
use Illuminate\Http\Request;
use App\Models\Resume;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class ResumeController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $translated_language =TranslatedLanguages::get();

        $user = auth()->user();
//        $resumes = $user->resumes;
//        return $this->showAll($resumes);
//        $translated_language =TranslatedLanguages::get();
//        $user = auth()->user();
        $resumes = $user->resumes;
        return response()->json([
            'resumes' => $resumes,
            'translated_language' =>$translated_language
        ]);

    }

    public function resumeData()
    {
        $translatedLanguages = TranslatedLanguages::get();
        return response()->json([
            'TranslatedLanguages' => $translatedLanguages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
//            'user_id' => 'required',
            'translated_languages_id' => 'required'
        ]);
        $resume = new Resume();
        $user = auth()->user();
        $resume->user_id = $user->id;
//        $resume->user_id = $request['user_id'];
        $resume->translated_languages_id = $request['translated_languages_id'];
        $resume->name = $request['name'];
        $resume->save();
        return $this->showOne($resume);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resume $resume
     * @return \Illuminate\Http\Response
     */
    public function show($resume_id)
    {
        $resume = Resume::where('resumes.id', $resume_id)->first();
//        resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $show_resume = Resume::where('id', $resume_id)->with([
            'personalInformation.placeOfBirth',
            'personalInformation.currentLocation',
            'summary',
            'objective',
            'contactInformation.emails',
            'contactInformation.contactNumbers', 'contactInformation.internetCommunications.internetCommunicationType', 'contactInformation.personalLinks.socialMedia',
            'educations.projects',
            'languages.diplomas',
            'drivingLicense.categories',
            'achievements',
            'work_experiences.company',
            'hobbiesInterest',
            'memberships',
            'projects',
            'publications',
            'volunteers',
            'ConferencesWorkshopSeminar',
            'Portfolio',
            'certifications',
            'trainings',
            'reReferences',
            'skills'
        ])->
        with(array('personalInformation.maritalStatus.maritalStatusTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))->
        with(array('personalInformation.nationalities.nationalityTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))
            ->with(array('contactInformation.contactNumbers.phoneType.PhoneTypeTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->
            with(array('contactInformation.contactNumbers.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('languages.internationalLanguage.internationalLanguageTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('languages.languageAssessment.selfAssessment.selfAssessmentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('drivingLicense.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('educations.major.majorTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('educations.minor.minorTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('educations.university.universityTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('educations.degreeLevel.degreeLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('work_experiences.company_industry.companyIndustryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('work_experiences.employment_types.employment_type_parent.empTypeParentTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('work_experiences.employment_types.employment_type_parent.parent_category.empTypeParentTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->
            with(array('ConferencesWorkshopSeminar.conferenceType.conferenceTypeTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('reReferences.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skills.skill_types.skillTypeTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skills.skill_types.skill_type_parents.skillTypeParentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skills.skillLevel.skillLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->first();
        return $this->showOne($show_resume);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resume $resume
     * @return \Illuminate\Http\Response
     */
    public function edit(Resume $resume)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Resume $resume
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resume $resume)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resume $resume
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resume $resume)
    {
        /*$resumeDate = $resume;
        $resume->delete();
        return $resumeDate;*/

        $deleted_resume = Resume::where('id', $resume->id)->with([
            'personalInformation.placeOfBirth',
            'personalInformation.currentLocation',
            'personalInformation.nationalities',
            'summary',
            'objective',
            'contactInformation.emails',
            'contactInformation.contactNumbers', 'contactInformation.internetCommunications.internetCommunicationType', 'contactInformation.personalLinks.socialMedia',
            'educations.projects',
            'languages.diplomas',
            'drivingLicense.categories',
            'achievements',
            'work_experiences.company',
            'hobbiesInterest',
            'memberships',
            'projects',
            'publications',
            'volunteers',
            'ConferencesWorkshopSeminar',
            'Portfolio',
            'certifications',
            'trainings',
            'reReferences',
            'skills'
        ])->first();

        //delete_personal_information
        foreach ($resume->personalInformation()->get() as $per) {
            $personalInformation = PersonalInformation::where('id', $per->id)->first();

            $placeOfBirth = PlaceOfBirth::where('personal_information_id', $personalInformation->id);
            $placeOfBirth->delete();

            $nationalities = DB::table('nationality_personal_information')->where('personal_information_id', $personalInformation->id)->get();
            foreach ($nationalities as $nationality) {
                DB::table('nationality_personal_information')->where('personal_information_id', $personalInformation->id)->delete();
            }
            $personalInformation->delete();
        }

        //delete_summary
        $summary = Summary::where('resume_id', $resume->id);
        $summary->delete();

        //delete_objectives
        $objective = Objective::where('resume_id', $resume->id);
        $objective->delete();

        //delete_hobbies_interests
        $hobbiesinterest = HobbiesInterest::where('resume_id', $resume->id);
        $hobbiesinterest->delete();

        //delete_languages
        foreach ($resume->languages()->get() as $lan) {
            $language = Language::where('languages.id', $lan->id)->first();
            foreach ($language->diplomas()->get() as $pro) {
                $diploma = Diploma::where('id', $pro->id)->first();
                $diploma->delete();
            }
            foreach ($language->languageAssessment()->get() as $ass) {
                $langAssess = LanguageAssessment::where('id', $ass->id)->first();
                $langAssess->delete();
            }
            $language->delete();
        }

        //delete_drivings
        $driving = Driving::where('resume_id', $resume->id);
        $driving->delete();

        //delete_achievements
        $achievement = Achievements::where('resume_id', $resume->id);
        $achievement->delete();

        //delete_memberships
        $membership = Membership::where('resume_id', $resume->id);
        $membership->delete();

        //delete_projects
        $project = Projects::where('resume_id', $resume->id);
        $project->delete();

        //delete_publications
        $publication = Publications::where('resume_id', $resume->id);
        $publication->delete();

        //delete_volunteers
        $volunteers = Volunteers::where('resume_id', $resume->id);
        $volunteers->delete();

        //delete_conferences_workshop_seminar
        $conferences_workshop_seminar = ConferencesWorkshopSeminar::where('resume_id', $resume->id);
        $conferences_workshop_seminar->delete();

        //delete_portfolios
        $portfolio = Portfolio::where('resume_id', $resume->id);
        $portfolio->delete();

        //delete_Certifications
        $certification = Certifications::where('resume_id', $resume->id);
        $certification->delete();

        //delete_trainings
        $trainings = Training::where('resume_id', $resume->id);
        $trainings->delete();

        //delete_references
        $reference = ReReference::where('resume_id', $resume->id);
        $reference->delete();

        //delete_educations
        $education = Education::where('resume_id', $resume->id);
        $education->delete();

        //delete_work_experience
        $work_exp = WorkExperience::where('resume_id', $resume->id);
        //$work_exp->employment_types()->delete();
        $work_exp->delete();

        //delete_skills
        $skills = Skill::where('resume_id', $resume->id);
        $skills->delete();

        //delete_contact_Information
        foreach ($resume->contactInformation()->get() as $con) {
            $contactInformation = ContactInformation::where('id', $con->id)->first();

            $personalLink = PersonalLink::where('contact_information_id', $contactInformation->id);
            $personalLink->delete();

            $contactInformation->delete();
        }

        $resume->delete();

        return response()->json([
            $deleted_resume,
        ], 200);
    }

    /**
     * @param $resume_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate($resume_id)
    {
        $resume = Resume::where('resumes.id', $resume_id)->first();
//        resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        $new_resume = $resume->replicate();
        $resume->count_copy++;
        $new_resume->name = $resume->name . '(' . $resume->count_copy . ')';
        $new_resume->save();
        $resume->save();
        //////////// personal_info
        foreach ($resume->personalInformation()->get() as $per) {
            $personalInformation = PersonalInformation::where('id', $per->id)->first();
            $newPersonalInformation = $personalInformation->replicate();
            $newPersonalInformation->resume_id = $new_resume->id;
            $newPersonalInformation->save();
            $placeOfBirth = PlaceOfBirth::where('personal_information_id', $personalInformation->id)->first();
            $newPlaceOfBirth = $placeOfBirth->replicate();
            $newPlaceOfBirth->personal_information_id = $newPersonalInformation->id;
            $newPlaceOfBirth->save();

            $currentLocation = CurrentLocation::where('personal_information_id', $personalInformation->id)->first();
            $newCurrentLocation = $currentLocation->replicate();
            $newCurrentLocation->personal_information_id = $newPersonalInformation->id;
            $newCurrentLocation->save();

            $nationalities = DB::table('nationality_personal_information')->where('personal_information_id', $personalInformation->id)->get();
            foreach ($nationalities as $nationality) {
                DB::table('nationality_personal_information')
                    ->Insert([
                        'nationality_id' => $nationality->nationality_id,
                        'personal_information_id' => $newPersonalInformation->id
                    ]);
            }
        }
        //////////// summary
        $summary = Summary::where('resume_id', $resume->id)->first();
        $newSummary = $summary->replicate();
        $newSummary->resume_id = $new_resume->id;
        $newSummary->save();

        //////////// objective
        $objective = Objective::where('resume_id', $resume->id)->first();
        $newObjective = $objective->replicate();
        $newObjective->resume_id = $new_resume->id;
        $newObjective->save();

        //////////// hobbiesInterest
        $hobbiesInterest = HobbiesInterest::where('resume_id', $resume->id)->first();
        $newHobbiesInterest = $hobbiesInterest->replicate();
        $newHobbiesInterest->resume_id = $new_resume->id;
        $newHobbiesInterest->save();

        //////////// language
        foreach ($resume->languages()->get() as $lan) {
            $language = Language::where('languages.id', $lan->id)->first();
            $newlanguage = $language->replicate();
            $newlanguage->resume_id = $new_resume->id;
            $newlanguage->save();
            foreach ($language->diplomas()->get() as $pro) {
                $diploma = Diploma::where('id', $pro->id)->first();
                $newDiploma = $diploma->replicate();
                $newDiploma->language_id = $newlanguage->id;
                $newDiploma->save();
            }
            foreach ($language->languageAssessment()->get() as $assessment) {
                $lang_assessment = LanguageAssessment::where('id', $assessment->id)->first();
                $newLang_assessment = $lang_assessment->replicate();
                $newLang_assessment->language_id = $newlanguage->id;
                $newLang_assessment->save();
            }
        }
        //////////// drivingLicense
        foreach ($resume->drivingLicense()->get() as $dr) {
            $drivingLicense = Driving::where('drivings.id', $dr->id)->first();
            $newDrivingLicense = $drivingLicense->replicate();
            $newDrivingLicense->resume_id = $new_resume->id;
            $newDrivingLicense->save();
            foreach ($drivingLicense->categories()->get() as $cat) {
                $category = DrivingCategory::where('id', $cat->id)->first();
                $newCatecory = $category->replicate();
                $newCatecory->driving_id = $newDrivingLicense->id;
                $newCatecory->save();
            }
        }
        //////////// achievment
        foreach ($resume->achievements()->get() as $ach) {
            $achievement = Achievements::where('achievements.id', $ach->id)->first();
            $newAchievement = $achievement->replicate();
            $newAchievement->resume_id = $new_resume->id;
            $newAchievement->save();
        }

        //////////// memberships
        foreach ($resume->memberships()->get() as $me) {
            $membership = Membership::where('memberships.id', $me->id)->first();
            $newMembership = $membership->replicate();
            $newMembership->resume_id = $new_resume->id;
            $newMembership->save();
        }

        //////////// projects
        foreach ($resume->projects()->get() as $p) {
            $project = Projects::where('projects.id', $p->id)->first();
            $newProject = $project->replicate();
            $newProject->resume_id = $new_resume->id;
            $newProject->save();
        }
        //////////// publications
        foreach ($resume->publications()->get() as $pu) {
            $publication = Publications::where('publications.id', $pu->id)->first();
            $newPublication = $publication->replicate();
            $newPublication->resume_id = $new_resume->id;
            $newPublication->save();
        }
        //////////// volunteers
        foreach ($resume->volunteers()->get() as $vo) {
            $volunteer = Volunteers::where('volunteers.id', $vo->id)->first();
            $newVolunteer = $volunteer->replicate();
            $newVolunteer->resume_id = $new_resume->id;
            $newVolunteer->save();
        }
        //////////// ConferencesWorkshopSeminar
        foreach ($resume->ConferencesWorkshopSeminar()->get() as $c) {
            $con_work_sem = ConferencesWorkshopSeminar::where('conferences_workshop_seminars.id', $c->id)->first();
            $newCon_work_sem = $con_work_sem->replicate();
            $newCon_work_sem->resume_id = $new_resume->id;
            $newCon_work_sem->save();
        }
        //////////// Portfolio
        foreach ($resume->Portfolio()->get() as $port) {
            $Portfolio = Portfolio::where('portfolios.id', $port->id)->first();
            $newPortfolio = $Portfolio->replicate();
            $newPortfolio->resume_id = $new_resume->id;
            $newPortfolio->save();
        }
        //////////// certifications
        foreach ($resume->certifications()->get() as $cert) {
            $Certification = Certifications::where('certifications.id', $cert->id)->first();
            $newCertification = $Certification->replicate();
            $newCertification->resume_id = $new_resume->id;
            $newCertification->save();
        }
        //////////// trainings
        foreach ($resume->trainings()->get() as $tr) {
            $training = Training::where('trainings.id', $tr->id)->first();
            $newTraining = $training->replicate();
            $newTraining->resume_id = $new_resume->id;
            $newTraining->save();
        }
        //////////// reReferences
        foreach ($resume->reReferences()->get() as $re) {
            $reReference = ReReference::where('re_references.id', $re->id)->first();
            $newReReference = $reReference->replicate();
            $newReReference->resume_id = $new_resume->id;
            $newReReference->save();
        }


        //////////// education
        foreach ($resume->educations()->get() as $edu) {
            $education = Education::where('education.id', $edu->id)->first();
            $neweducation = $education->replicate();
            $neweducation->resume_id = $new_resume->id;
            $neweducation->save();
            foreach ($education->projects()->get() as $pro) {
                $project = EducationProject::where('id', $pro->id)->first();
                $newEducationProject = $project->replicate();
                $newEducationProject->education_id = $neweducation->id;
                $newEducationProject->save();

            }
        }
        //////////// work experience
        foreach ($resume->work_experiences()->with(['company'])->get() as $work) {
            $work_exp = WorkExperience::where('id', $work->id)->first();
            $newWorkExp = $work_exp->replicate();
            $newWorkExp->resume_id = $new_resume->id;
            $newWorkExp->save();

            $company = WorkExpCompany::where('work_experience_id', $work->id)->first();
            $newCompany = $company->replicate();
            $newCompany->work_experience_id = $newWorkExp->id;
            $newCompany->save();
            foreach ($work_exp->employment_types()->get() as $emp_type) {
                $employment_type = EmploymentType::where('id', $emp_type->id)->first();
                $newEmploymentType = $employment_type->replicate();
                $newEmploymentType->work_experience_id = $newWorkExp->id;
                $newEmploymentType->save();
            }
        }
//
        //////////// skills
        foreach ($resume->skills()->get() as $skill) {
            $skill1 = Skill::where('id', $skill->id)->first();
            $newSkill = $skill1->replicate();
            $newSkill->resume_id = $new_resume->id;
            $newSkill->save();
        }
        //////////// contact_info
        foreach ($resume->contactInformation()->get() as $con) {
            $contact_info = ContactInformation::where('id', $con->id)->first();
            $newContactInfo = $contact_info->replicate();
            $newContactInfo->resume_id = $new_resume->id;
            $newContactInfo->save();
            foreach ($contact_info->emails()->get() as $em) {
                $email = Email::where('id', $em->id)->first();
                $newEmail = $email->replicate();
                $newEmail->contact_information_id = $newContactInfo->id;
                $newEmail->save();
            }
            foreach ($contact_info->contactNumbers()->get() as $num) {
                $contact_number = ContactNumber::where('id', $num->id)->first();
                $newContact_number = $contact_number->replicate();
                $newContact_number->contact_information_id = $newContactInfo->id;
                $newContact_number->save();
            }
            foreach ($contact_info->internetCommunications()->get() as $comm) {
                $internetCommunication = InternetCommunication::where('id', $comm->id)->first();
                $newInternetCommunication = $internetCommunication->replicate();
                $newInternetCommunication->contact_information_id = $newContactInfo->id;
                $newInternetCommunication->save();
            }
            foreach ($contact_info->personalLinks()->get() as $link) {
                $personalLink = PersonalLink::where('id', $link->id)->first();
                $newPersonalLink = $personalLink->replicate();
                $newPersonalLink->contact_information_id = $newContactInfo->id;
                $newPersonalLink->save();
            }

        }

        $show_new_resume = Resume::where('id', $new_resume->id)->with([
            'personalInformation.placeOfBirth',
            'personalInformation.currentLocation',
            'summary',
            'objective',
            'contactInformation.emails',
            'contactInformation.contactNumbers', 'contactInformation.internetCommunications.internetCommunicationType', 'contactInformation.personalLinks.socialMedia',
            'educations.projects',
            'languages.diplomas',
            'drivingLicense.categories',
            'achievements',
            'work_experiences.company',
            'hobbiesInterest',
            'memberships',
            'projects',
            'publications',
            'volunteers',
            'ConferencesWorkshopSeminar',
            'Portfolio',
            'certifications',
            'trainings',
            'reReferences',
            'skills'
        ])->
        with(array('personalInformation.maritalStatus.maritalStatusTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))->
        with(array('personalInformation.nationalities.nationalityTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))
            ->
            with(array('contactInformation.contactNumbers.phoneType.PhoneTypeTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->
            with(array('contactInformation.contactNumbers.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('languages.internationalLanguage.internationalLanguageTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('languages.languageAssessment.selfAssessment.selfAssessmentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('drivingLicense.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->
            with(array('educations.major.majorTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->
            with(array('educations.minor.minorTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->
            with(array('educations.university.universityTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('educations.degreeLevel.degreeLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('work_experiences.company_industry.companyIndustryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('work_experiences.employment_types.employment_type_parent.empTypeParentTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('work_experiences.employment_types.employment_type_parent.parent_category.empTypeParentTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->
            with(array('ConferencesWorkshopSeminar.conferenceType.conferenceTypeTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->
            with(array('reReferences.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skills.skill_types.skillTypeTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skills.skill_types.skill_type_parents.skillTypeParentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skills.skillLevel.skillLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->first();

        return response()->json([
            $show_new_resume,
        ], 200);
    }

    public function setActive(Request $request, $resume_id)
    { //true or false
        $resume = Resume::where('id', $resume_id)->update(['active' => $request['active']]);
        $show_resume = Resume::where('id', $resume_id)->first();
        return $this->showOne($show_resume);
    }

    public function rename(Request $request, $resume_id)
    {
        $resume = Resume::where('id', $resume_id)->first();
        $resume->name = $request['name'];
        $resume->save();
        return $this->showOne($resume);

    }


}
