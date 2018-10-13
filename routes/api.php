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



Route::resource('contactInfo', 'ContactInformation\ContactInfoController',['except' => ['edit','create','index']]) ;
Route::resource('summary', 'Summary\SummaryController',['except' => ['edit','create','index']]) ;
Route::resource('objective', 'Objective\ObjectiveController',['except' => ['edit','create','index']]) ;
Route::resource('resume', 'ResumeController',['except' => ['edit','create']]) ;
Route::resource('personalInformation', 'PersonalInformation\PersonalInformationController', ['except' => ['edit','create','index']]);
Route::resource('education', 'Education\EducationController', ['except' => ['edit','create']]);
Route::resource('language', 'Language\LanguageController', ['except' => ['edit','create']]);
Route::resource('driving', 'DrivingLicense\DrivingLicenseController', ['except' => ['edit','create']]);
Route::Post('language/order/{resumeId}','Language\LanguageController@orderData');
Route::get('education/{resumeId}/{educationId}','Education\EducationController@getSingleEducation');
Route::Post('education/order/{resumeId}','Education\EducationController@orderData');

