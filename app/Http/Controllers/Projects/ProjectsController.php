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

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);
        $project = new Projects();

        //store date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $project->isMonthPresent = true;
            } else {
                $Month = 1;
                $project->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $project->date = $date;
            $project->isPresent = false;
        }  else  if( $request['isPresent'] == true){
            $project->date = null;
            $project->isPresent = true;
            $project->isMonthPresent = false;}
        else{
            // There is no date
            $project->date= null;
            $project->isPresent = $request['isPresent'];// isPresent=0
            $project->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }

        $project->description = $request['description'];

        $project->resume_id = $request['resume_id'];

        $projects=Projects::where('resume_id',$request['resume_id'])->get();
        foreach($projects as $pro){
            $pro->order=$pro->order+1;
            $pro->save();
        }
        $project->order=1;
        $project->save();

        $newProjects = Projects::where('id' , $project->id)->first();

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
        $this->validate($request,[ 'resume_id' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $this->validate($request, ['description' => 'required']);

        $projects = Projects::findOrFail( $id);

       //update date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $projects->isMonthPresent = true;
            } else {
                $Month = 1;
                $projects->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $projects->date = $date;
            $projects->isPresent = false;
        } else  if( $request['isPresent'] == true){
            $projects->date = null;
            $projects->isPresent = true;
            $projects->isMonthPresent = false;}
        else{
            // There is no date
            $projects->date= null;
            $projects->isPresent = $request['isPresent'];// isPresent=0
            $projects->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }

        $projects->description = $request['description'];
        $projects->resume_id = $request['resume_id'];
        $projects->save();
        $newProjects = Projects::where('id', $projects->id)->first();

        return $this->showOne($newProjects);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Projects\Projects  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Projects $project)
    {
        $user = auth()->user();
        if ($user->id != $project->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $project->delete();
        $projects = Projects::where([['resume_id', $project->resume_id], ['order', '>', $project->order]])->get();
        foreach ($projects as $pro) {
            $pro->order = $pro->order - 1;
            $pro->save();
        }
            return $this->showOne($project);

    }
    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $pro){
            $project=Projects::findOrFail($pro['projectId']);
            $project->order=$pro['orderId'];
            $project->save();
        }
        return response()->json(['success'=>'true']);
    }
}
