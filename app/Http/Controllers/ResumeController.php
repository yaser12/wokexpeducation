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
use App\Models\SummarySec\Summary;
use App\Models\Training\Training;
use App\Models\Volunteers\Volunteers;
use App\Models\WorkExperience\Company;
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
        $user = auth()->user();
        $resumes = $user->resumes;
        return $this->showAll($resumes);
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
        $this->validate($request, ['user_id' => 'required']);
        $resume = Resume::create(['user_id' => $request->user_id]);
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
        $resume = Resume::where('id', $resume_id)->with(['summary', 'objective',
            'contactInformation.emails',
            'contactInformation.contactNumbers',
            'contactInformation.internetCommunications',
            'contactInformation.personalLinks',
            'personalInformation.nationalities',
            'personalInformation.currentLocation',
            'personalInformation.placeOfBirth',
            'educations.university', 'educations.major', 'educations.minor', 'educations.projects',
            'languages.diplomas',
            'drivingLicense',
            'achievements', 'memberships', 'projects', 'publications', 'volunteers', 'hobbiesInterest',
            'Portfolio', 'certifications', 'trainings', 'ConferencesWorkshopSeminar', 'reReferences',
            'work_experiences', 'work_experiences.company', 'work_experiences.company_industry',
            'work_experiences.employment_types.employment_type_parent',
            'work_experiences.employment_types.employment_type_parent.parent_category',

//            'skills'
        ])->get();
        return $this->showAll($resume);


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
        $resumeDate = $resume;
        $resume->delete();
        return $resumeDate;
    }

    /**
     * @param $resume_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate($resume_id)
    {
        $resume = Resume::where('resumes.id', $resume_id)->first();
        $new_resume = $resume->replicate();
        $new_resume->save();
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
        }
        //////////// drivingLicense
        foreach ($resume->drivingLicense()->get() as $lan) {
            $drivingLicense = Driving::where('drivings.id', $lan->id)->first();
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

            $company = Company::where('work_experience_id', $work->id)->first();
            $newCompany = $company->replicate();
            $newCompany->work_experience_id = $newWorkExp->id;
            $newCompany->save();
        }
//
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

        $show_newresume = Resume::where('id', $new_resume->id)->with([
            'personalInformation.placeOfBirth',
            'personalInformation.nationalities',
            'summary',
            'objective',
            'contactInformation.emails', 'contactInformation.contactNumbers', 'contactInformation.internetCommunications', 'contactInformation.personalLinks',
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
            'reReferences'

        ])->get();

        return response()->json([
            $show_newresume,
        ], 200);
    }
}
