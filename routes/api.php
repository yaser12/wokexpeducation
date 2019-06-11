<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'ApiAuth\AuthController@login');
    Route::post('logout', 'ApiAuth\AuthController@logout');
    Route::post('refresh', 'ApiAuth\AuthController@refresh');
    Route::post('me', 'ApiAuth\AuthController@me');

});
Route::resource('resume', 'ResumeController', ['except' => ['edit', 'create']]);
//////////////////////////
Route::resource('personalInformation', 'PersonalInformation\PersonalInformationController', ['except' => ['edit', 'create',]]);
Route::resource('summary', 'Summary\SummaryController', ['except' => ['edit', 'create', 'index']]);
Route::resource('objective', 'Objective\ObjectiveController', ['except' => ['edit', 'create', 'index']]);
Route::resource('hobbiesInterest', 'HobbiesInterest\HobbiesInterestController', ['except' => ['edit', 'create', 'index']]);
//////////////////////////
Route::resource('contactInfo', 'ContactInformation\ContactInfoController', ['except' => ['edit', 'create', 'index']]);
Route::resource('language', 'Language\LanguageController', ['except' => ['edit', 'create']]);
Route::Post('language/order/{resumeId}', 'Language\LanguageController@orderData');
/////////////////////////
Route::resource('driving', 'DrivingLicense\DrivingLicenseController', ['except' => ['edit', 'create']]);
//////////////////////////////////////
Route::resource('education', 'Education\EducationController', ['except' => ['edit', 'create']]);
Route::get('education/{resumeId}/{educationId}', 'Education\EducationController@getSingleEducation');
Route::Post('education/order/{resumeId}', 'Education\EducationController@orderData');
//////////////////////////////////////
Route::resource('achievements', 'Achievements\AchievementsController', ['except' => ['edit', 'create', 'index']]);
Route::get('resumes/{resume}/achievements', 'Achievements\AchievementsController@index');
Route::Post('achievements/order/{resumeId}', 'Achievements\AchievementsController@orderData');
/////////////////////////////////////
Route::resource('ConferencesWorkshopSeminar', 'ConferencesWorkshopSeminar\ConferencesWorkshopSeminarController', ['except' => ['edit', 'create']]);
Route::Post('ConferencesWorkshopSeminar/order/{resumeId}', 'ConferencesWorkshopSeminar\ConferencesWorkshopSeminarController@orderData');
Route::get('resumes/{resume}/ConferencesWorkshopSeminars', 'ConferencesWorkshopSeminar\ConferencesWorkshopSeminarController@index');
Route::get('ConferencesData/{resume}', 'ConferencesWorkshopSeminar\ConferencesWorkshopSeminarController@ConferencesData');



Route::resource('membership', 'Membership\MembershipController', ['except' => ['edit', 'create']]);
Route::Post('memberships/order/{resumeId}', 'Membership\MembershipController@orderData');

Route::resource('project', 'Projects\ProjectsController', ['except' => ['edit', 'create']]);
Route::Post('projects/order/{resumeId}', 'Projects\ProjectsController@orderData');

Route::resource('publication', 'Publications\PublicationsController', ['except' => ['edit', 'create']]);
Route::Post('publications/order/{resumeId}', 'Publications\PublicationsController@orderData');

Route::resource('volunteer', 'Volunteers\VolunteersController', ['except' => ['edit', 'create']]);
Route::Post('volunteers/order/{resumeId}', 'Volunteers\VolunteersController@orderData');

Route::resource('portfolio', 'Portfolio\PortfolioController', ['except' => ['edit', 'create']]);
Route::Post('portfolios/order/{resumeId}', 'Portfolio\PortfolioController@orderData');
Route::get('resumes/{resume}/portfolios', 'Portfolio\PortfolioController@index');

Route::resource('certifications', 'Certification\CertificationController', ['except' => ['edit', 'create']]);
Route::Post('certifications/order/{resumeId}', 'Certification\CertificationController@orderData');
Route::get('resumes/{resume}/certifications', 'Certification\CertificationController@index');

Route::resource('trainings', 'Training\TrainingController', ['except' => ['edit', 'create']]);
Route::Post('trainings/order/{resumeId}', 'Training\TrainingController@orderData');
Route::get('resumes/{resume}/trainings', 'Training\TrainingController@index');

Route::resource('references', 'ReReferences\ReReferencesController', ['except' => ['edit', 'create']]);
Route::Post('references/order/{resumeId}', 'ReReferences\ReReferencesController@orderData');
Route::get('resumes/{resume}/references', 'ReReferences\ReReferencesController@index');

Route::resource('workExperiences', 'WorkExperience\WorkExperienceController', ['except' => ['edit', 'create']]);
Route::Post('workExperiences/order/{resumeId}', 'WorkExperience\WorkExperienceController@orderData');
Route::get('resumes/{resume}/workExperiences', 'WorkExperience\WorkExperienceController@index');

Route::resource('skills', 'Skills\SkillsController', ['except' => ['edit', 'create']]);
Route::Post('skills/order/{resumeId}', 'Skills\SkillsController@orderData');
Route::get('resumes/{resume}/skills', 'Skills\SkillsController@index');

Route::get('resume/duplicate/{resumeId}', 'ResumeController@duplicate');
Route::post('resume/setActive/{resumeId}', 'ResumeController@setActive');
