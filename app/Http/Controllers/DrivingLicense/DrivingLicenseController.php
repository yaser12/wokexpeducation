<?php

namespace App\Http\Controllers\DrivingLicense;


use App\Models\Country\Country;
use App\Models\Country\CountryTranslation;
use App\Models\DrivingCategory\DrivingCategory;

use App\Http\Controllers\ApiController;
use App\Models\DrivingLicense\Driving;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\DB;

class DrivingLicenseController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
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
            'resume_id' => 'required',
            'license_type' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        return DB::transaction(function () use ($request, $resume) {
            $driving = new Driving();
            $driving->resume_id = $request['resume_id'];
            $driving->license_type = $request['license_type'];
//            $driving->country = $request['country'];
            $driving->country_id = $request['country_id'];
            $driving->international = $request['international'];
            $driving->save();
            $categories = $request['category'];
            foreach ($categories as $cat) {
                $c = new DrivingCategory();
                $c->driving_id = $driving->id;
                $c->name = $cat['name'];
                $c->save();
            }
            $driving->categories = $categories;
            //       resume translated language
            $resume_translated_language = $resume->translated_languages_id;
            $driving1 = Driving::where('resume_id', $resume->id)->
            with('categories', 'country')->
            with(array('country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->first();
            return $this->showOne($driving1);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  $resumeId
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {

        $resume = Resume::findOrFail($resumeId);

        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
//               resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $driving = Driving::where('resume_id', $resume->id)->
        with('categories', 'country')->
        with(array('country.countryTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))->get();
        $country_name_trans = CountryTranslation::where('translated_languages_id', $resume_translated_language)->
        get(['country_id', 'name']);


        if ($driving == null) {
            return response()->json(['data' => 404]);
        }
        return response()->json([
            'driving' => $driving,

        ]);
    }

    public function drivingData($resume_id)
    {
        $resume = Resume::findOrFail($resume_id);
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $country = Country::with(array('countryTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))->get();;

        return response()->json([
            'country_codes' => $country,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Driving $driving)
    {
        $this->validate($request, [
            'resume_id' => 'required',
            'license_type' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request, $driving, $resume) {

            $driving->resume_id = $request['resume_id'];
            $driving->license_type = $request['license_type'];
//            $driving->country = $request['country'];
            $driving->country_id = $request['country_id'];
            $driving->international = $request['international'];
            $driving->save();
            if ($request->has('category')) {

                $categories = $request['category'];
                if ($categories != null) {
                    $driving->categories()->delete();
                }
                foreach ($categories as $cat) {
                    $category = new DrivingCategory();
                    $category->driving_id = $driving->id;
                    $category->name = $cat['name'];
                    $category->save();
                }
                $driving->categories = $categories;
            }
            //       resume translated language
            $resume_translated_language = $resume->translated_languages_id;
            $driving1 = Driving::where('resume_id', $resume->id)->with('categories', 'country')->
            with(array('country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->first();
            return $this->showOne($driving1);

        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driving $driving)
    {
        $user = auth()->user();
        if ($user->id != $driving->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $driving->categories()->delete();
        $driving->delete();

        return $this->showOne($driving);
    }
}
