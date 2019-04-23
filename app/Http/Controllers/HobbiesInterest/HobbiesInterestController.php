<?php

namespace App\Http\Controllers\HobbiesInterest;

use App\Http\Controllers\ApiController;
use App\Models\HobbiesInterest\HobbiesInterest;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HobbiesInterestController extends ApiController
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
        //
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
        $this->validate($request,['description => required','resume_id => required']);
        $resume = Resume::findOrFail($request->resume_id);
         if($resume->hobbiesinterest!=null){
            return $this->errorResponse('Trying To Access Filled Failed', 409);
        }
        $data['description'] = $request->description;
        $data['resume_id'] = $request->resume_id;
        $hobbiesinterest = HobbiesInterest::create($data);
        return $this->showOne($hobbiesinterest);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        if($resume->hobbiesinterest == null){
            return response()->json(['data' => 404]);
        }
        $hobbiesinterests = $resume->hobbiesinterest;
        return $this->showOne($hobbiesinterests);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HobbiesInterest $hobbiesInterest)
    {
        $this->validate($request,['description => required','resume_id => required']);
        $hobbiesInterest->description = $request->description;
        $hobbiesInterest->save();
        return $this->showOne($hobbiesInterest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(HobbiesInterest $hobbiesInterest)
    {
        $hobbiesInterestData = $hobbiesInterest;
        $hobbiesInterest->delete();
        return $hobbiesInterestData;
    }
}
