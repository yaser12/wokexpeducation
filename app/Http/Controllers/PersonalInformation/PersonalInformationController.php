<?php

namespace App\Http\Controllers\PersonalInformation;

use App\Models\PersonalInformation\CurrentLocation;
use App\Models\PersonalInformation\Nationality;
use App\Models\PersonalInformation\PersonalInformation;
use App\Models\PersonalInformation\PlaceOfBirth;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class PersonalInformationController extends ApiController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function store(Request $request)
    {
        $rules = [
            'resume_id' => 'required|integer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'marital_status' => 'required|string',
            'date_of_birth' => 'required',
            'nationalities' => 'required'
        ];

        $DOBRules = [
            'year' => 'required|string',
            'month' => 'required|string',
            'day' => 'required|string'
        ];

        $POBRules = [
            'country' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required'
        ];

        $CLRules = [
            'country' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'street_address' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required'
        ];

        $this->validate($request, $rules);

        $resume = Resume::findOrFail($request->resume_id);

        if($resume->personalInformation !== null)
        {
            return $this->errorResponse("Trying To Access Filled Field.", 409);
        }

        return DB::transaction(function() use ($request, $POBRules, $CLRules, $resume, $DOBRules) {

            $DOBRequest = new Request($request->date_of_birth);

            $date_of_birth = "";
            if($request->has('date_of_birth'))
            {
                $this->validate($DOBRequest, $DOBRules);
                $year = $DOBRequest->year;
                $month = $DOBRequest->month;
                $day = $DOBRequest->day;

                $date_string = $year . "-" . $month . "-" . $day;
                $date_time = new \DateTime();
                $date_of_birth = $date_time->createFromFormat('Y-m-d', $date_string);
            }

            $personalInformation = new PersonalInformation();
            $personalInformation->resume_id = $request->resume_id;
            $personalInformation->first_name = $request->first_name;
            $personalInformation->middle_name = $request->has('middle_name') ? $request->middle_name : null;
            $personalInformation->last_name = $request->last_name;
            $personalInformation->resume_title = $request->has('resume_title') ? $request->resume_title : null;
            $personalInformation->gender = $request->gender;
            $personalInformation->marital_status = $request->marital_status;
            $personalInformation->date_of_birth = $date_of_birth;
            $personalInformation->save();

            if($request->has('place_of_birth'))
            {
                $POBRequest = new Request($request->place_of_birth);
                $this->validate($POBRequest, $POBRules);
                PlaceOfBirth::create([
                    'personal_information_id' => $personalInformation->id,
                    'country' => $POBRequest->country,
                    'city' => $POBRequest->city,
                    'latitude' => $POBRequest->latitude,
                    'longitude' => $POBRequest->longitude
                ]);
            }

            if($request->has('current_location'))
            {
                $CLRequest = new Request($request->current_location);
                $this->validate($CLRequest, $CLRules);
                CurrentLocation::create([
                    'personal_information_id' => $personalInformation->id,
                    'country' => $CLRequest->country,
                    'city' => $CLRequest->city,
                    'postal_code' => $CLRequest->postal_code,
                    'street_address' => $CLRequest->street_address,
                    'latitude' => $CLRequest->latitude,
                    'longitude' => $CLRequest->longitude
                ]);
            }

            if($request->nationalities !== [])
            {
                foreach($request->nationalities as $value)
                {
                    //$nationality = Nationality::where('name', $value)->first();
                    $personalInformation->nationalities()->attach([$value]);
                }
            }

            $personalInformation->nationalities;
            $personalInformation->currentLocation;
            $personalInformation->placeOfBirth;

            return $this->showOne($personalInformation);
        });

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PersonalInformation\PersonalInformation  $personalInformation
     * @return \Illuminate\Http\Response
     */
    public function show(Resume $personalInformation)
    {
        $personalInformationId = $personalInformation->personalInformation->id;
        $personalInformation = PersonalInformation::where('id', $personalInformationId)->with(['placeOfBirth', 'currentLocation', 'nationalities'])->get();
        return $this->showAll($personalInformation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PersonalInformation\PersonalInformation  $personalInformation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PersonalInformation $personalInformation)
    {
        $rules = [
            'resume_id' => 'required|integer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'marital_status' => 'required|string',
            'date_of_birth' => 'required',
            'nationalities' => 'required'
        ];

        $DOBRules = [
            'year' => 'required|string',
            'month' => 'required|string',
            'day' => 'required|string'
        ];

        $POBRules = [
            'country' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required'
        ];

        $CLRules = [
            'country' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'street_address' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required'
        ];

        $this->validate($request, $rules);

        return DB::transaction(function() use ($request, $personalInformation, $POBRules, $CLRules, $DOBRules) {

            $date_of_birth = "";
            if($request->has('date_of_birth'))
            {
                $DOBRequest = new Request($request->date_of_birth);
                $this->validate($DOBRequest, $DOBRules);
                $year = $DOBRequest->year;
                $month = $DOBRequest->month;
                $day = $DOBRequest->day;

                $date_string = $year . "-" . $month . "-" . $day;
                $date_time = new \DateTime();
                $date_of_birth = $date_time->createFromFormat('Y-m-d', $date_string);
            }
            $personalInformation->first_name = $request->first_name;
            $personalInformation->middle_name = $request->has('middle_name') ? $request->middle_name : null;
            $personalInformation->last_name = $request->last_name;
            $personalInformation->resume_title = $request->has('resume_title') ? $request->resume_title : null;
            $personalInformation->gender = $request->gender;
            $personalInformation->marital_status = $request->marital_status;
            $personalInformation->date_of_birth = $date_of_birth;
            $personalInformation->save();

            if($request->has('place_of_birth'))
            {
                $personalInformation->placeOfBirth()->delete();
                $POBRequest = new Request($request->place_of_birth);
                $this->validate($POBRequest, $POBRules);
                PlaceOfBirth::create([
                    'personal_information_id' => $personalInformation->id,
                    'country' => $POBRequest->country,
                    'city' => $POBRequest->city,
                    'latitude' => $POBRequest->latitude,
                    'longitude' => $POBRequest->longitude
                ]);
            }

            if($request->has('current_location'))
            {
                $personalInformation->currentLocation()->delete();
                $CLRequest = new Request($request->current_location);
                $this->validate($CLRequest, $CLRules);
                CurrentLocation::create([
                    'personal_information_id' => $personalInformation->id,
                    'country' => $CLRequest->country,
                    'city' => $CLRequest->city,
                    'postal_code' => $CLRequest->postal_code,
                    'street_address' => $CLRequest->street_address,
                    'latitude' => $CLRequest->latitude,
                    'longitude' => $CLRequest->longitude
                ]);
            }

            if($request->has('nationalities'))
            {
                $personalInformation->nationalities()->detach();

                foreach($request->nationalities as $value)
                {
                    //$nationality = Nationality::where('name', $value)->first();
                    $personalInformation->nationalities()->attach([$value]);
                }
            }

            $personalInformation->nationalities;
            $personalInformation->currentLocation;
            $personalInformation->placeOfBirth;

            return $this->showOne($personalInformation);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PersonalInformation\PersonalInformation  $personalInformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resume $personalInformation)
    {
//        $personalInformationId = $personalInformation->personalInformation->id;
//        $personalInformation = PersonalInformation::where('id', $personalInformationId)->with(['placeOfBirth', 'currentLocation', 'nationalities'])->get();
//        return $this->showAll($personalInformation);
    }
}
