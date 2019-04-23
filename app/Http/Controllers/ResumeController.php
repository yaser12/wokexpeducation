<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resume;
use App\Http\Controllers\ApiController;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,['user_id'=>'required']);
        $resume = Resume::create(['user_id' => $request->user_id]);
        return $this->showOne($resume);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function show(Resume $resume)
    {

//        $data = Resume::where('id',8)->
//        with(['summary','objective',
//            'contactInformation.emails',
//            'contactInformation.contactNumbers',
//            'contactInformation.internetCommunications',
//            'contactInformation.personalLinks',
//            'personalInformation.nationalities',
//            'personalInformation.currentLocation',
//            'personalInformation.placeOfBirth',
//
//        ])->get();
//
//        return $this->showAll($data);

        $resume->summary;
        $resume->objective;

        $resume->contactInformation->emails;
        $resume->contactInformation->contactNumbers;
        $resume->contactInformation->internetCommunications;
        $resume->contactInformation->personalLinks;

        $resume->personalInformation->nationalities;
        $resume->personalInformation->currentLocation;
        $resume->personalInformation->placeOfBirth;
        $resume->ConferencesWorkshopSeminar;

        return $this->showOne($resume);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function edit(Resume $resume)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resume $resume)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resume $resume)
    {
        $resumeDate = $resume;
        $resume->delete();
        return $resumeDate;
    }
}
