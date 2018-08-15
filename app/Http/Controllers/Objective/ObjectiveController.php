<?php

namespace App\Http\Controllers\Objective;

use App\Http\Controllers\ApiController;
use App\Models\ObjectiveSec\Objective;
use App\Models\Resume;
use Illuminate\Http\Request;

class ObjectiveController extends ApiController
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
        $this->validate($request,['description'=>'required', 'resume_id'=>'required']);
        $resume=Resume::findOrFail($request->resume_id);
        if($resume->objective!=null){
            return $this->errorResponse('Trying To Access Filled Filed', 409);
        }
        $data['description'] = $request->description;
        $data['resume_id']= $request->resume_id;
        $objective = Objective::create($data);
        return $this->showOne($objective);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ObjectiveSec\Objective  $objective
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId )
    {

        $resume=Resume::findOrFail($resumeId);
        if($resume->objective==null){
            return response()->json(['data' => 404]);
        }
        $objective = $resume->objective;
        return $this->showOne($objective);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ObjectiveSec\Objective  $objective
     * @return \Illuminate\Http\Response
     */
    public function edit(Objective $objective)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ObjectiveSec\Objective  $objective
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Objective $objective)
    {
        //$this->validate($request,['description'=>'required']);
        if ($request->has('description'))
            $objective->description = $request->description;

        $objective->save();
        return $this->showOne($objective);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ObjectiveSec\Objective  $objective
     * @return \Illuminate\Http\Response
     */
    public function destroy(Objective $objective)
    {
        $objectiveData= $objective;
        $objective->delete();
        return $objectiveData;
    }
}
