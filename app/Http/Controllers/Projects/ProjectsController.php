<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\ApiController;
use App\Models\Projects\Projects;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectsController extends ApiController
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
        $this->validate($request,[ 'resume_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required']);
        $project = new Projects();

        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $request['date']);
        $project->date = $date;

        $project->description = $request['description'];

        $project->resume_id = $request['resume_id'];

        $projects=Projects::where('resume_id',$request['resume_id'])->get();
        foreach($projects as $lang){
            $lang->order=$lang->order+1;
            $lang->save();
        }
        $project->order=1;
        $projects->save();

        $newProjects = Projects::where('id' , $projects->id)->first();

        return $this->showOne($newProjects);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Projects\Projects  $projects
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $projects = Projects::where('resume_id', $resumeId)
            ->orderBy('order')
            ->get();

        return $this->showAll($projects);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Projects\Projects  $projects
     * @return \Illuminate\Http\Response
     */
    public function edit(Projects $projects)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Projects\Projects  $projects
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[ 'resume_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $this->validate($request, ['description' => 'required']);

        $projects = Projects::findOrFail( $id);

        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $request['date']);
        $projects->date = $date;
        $projects->description = $request['description'];
        $projects->resume_id = $request['resume_id'];
        $projects->save();
        $newProjects = Projects::where('id', $projects->id)->first();

        return $this->showOne($newProjects);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Projects\Projects  $projects
     * @return \Illuminate\Http\Response
     */
    public function destroy(Projects $projects)
    {
        $user = auth()->user();
        if ($user->id != $projects->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $projects->delete();
        return $this->showOne($projects);
    }

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $lang){
            $projects=Projects::findOrFail($lang['languageId']);
            $projects->order=$lang['orderId'];
            $projects->save();
        }
        return response()->json(['success'=>'true']);
    }
}
