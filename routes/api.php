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
//Resume
Route::resource('resume', 'ResumeController', ['except' => ['edit', 'create']]);
Route::get('resume/duplicate/{resumeId}', 'ResumeController@duplicate');
Route::get('resumeData/{resumeId}', 'ResumeController@resumeData');

//PersonalInfo
Route::resource('personalInformation', 'PersonalInformation\PersonalInformationController', ['except' => ['edit', 'create',]]);
Route::get('personalInfoData/{resume}', 'PersonalInformation\PersonalInformationController@personalInfoData');
//contactInfo
Route::get('contactInfoData/{resume}', 'ContactInformation\ContactInfoController@contactInfoData');
Route::resource('contactInfo', 'ContactInformation\ContactInfoController', ['except' => ['edit', 'create', 'index']]);

//eduction
Route::resource('education', 'Education\EducationController', ['except' => ['edit', 'create']]);
Route::get('education/{resumeId}/{educationId}', 'Education\EducationController@getSingleEducation');
Route::Post('education/order/{resumeId}', 'Education\EducationController@orderData');
Route::get('educationData/{resume}', 'Education\EducationController@educationData');

//workExperiences
Route::resource('workExperiences', 'WorkExperience\WorkExperienceController', ['except' => ['edit', 'create']]);


Route::Post('workExperiences/order/{resumeId}', 'WorkExperience\WorkExperienceController@orderData');
Route::get('resumes/{resume}/workExperiences', 'WorkExperience\WorkExperienceController@index');
Route::get('workExpData/{resume}', 'WorkExperience\WorkExperienceController@workExperiencesData');


///////////// yaser route

Route::resource('companyindustrycontroller', 'Company\CompanyIndustryController', ['except' => ['edit', 'create']]);
Route::resource('companytype', 'Company\CompanyTypeController', ['except' => ['edit', 'create']]);
Route::resource('companysize', 'Company\CompanySizeController', ['except' => ['edit', 'create']]);
Route::resource('specialty', 'Company\SpecialtyController', ['except' => ['edit', 'create']]);
Route::resource('companycontroller', 'Company\CompanyController', ['except' => ['edit', 'create']]);
Route::Post('companycontroller/upload_logo/{company_id}', 'Company\CompanyController@upload_logo');



