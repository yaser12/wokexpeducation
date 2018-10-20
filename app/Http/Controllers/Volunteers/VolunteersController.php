<?php

namespace App\Http\Controllers\Volunteers;

use App\Http\Controllers\ApiController;
use App\Models\Resume;
use App\Models\Volunteers\Volunteers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VolunteersController extends ApiController
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
        $volunteer = new Volunteers();

        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $request['date']);
        $volunteer->date = $date;

        $volunteer->description = $request['description'];

        $volunteer->resume_id = $request['resume_id'];

        $volunteers=Volunteers::where('resume_id',$request['resume_id'])->get();
        foreach($volunteers as $lang){
            $lang->order=$lang->order+1;
            $lang->save();
        }
        $volunteer->order=1;


        $volunteer->save();

        $newVolunteer = Volunteers::where('id' , $volunteer->id)->first();

        return $this->showOne($newVolunteer);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Volunteers\Volunteers  $volunteers
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $volunteers = Volunteers::where('resume_id', $resumeId)
            ->orderBy('order')
            ->get();

        return $this->showAll($volunteers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Volunteers\Volunteers  $volunteers
     * @return \Illuminate\Http\Response
     */
    public function edit(Volunteers $volunteers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Volunteers\Volunteers  $volunteers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id )
    {
        $this->validate($request,[ 'resume_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required']);
        $volunteers = new Publications();

        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $request['date']);
        $volunteers->date = $date;

        $volunteers->description = $request['description'];

        $volunteers->resume_id = $request['resume_id'];

        $volunteers->save();

        $newVolunteers = Publications::where('id' ,  $volunteers->id)->first();

        return $this->showOne($newVolunteers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Volunteers\Volunteers  $volunteers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Volunteers $volunteers)
    {
        $user = auth()->user();
        if ($user->id != $volunteers->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $volunteers->delete();
        return $this->showOne($volunteers);
    }
    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $lang){
            $volunteers=Volunteers::findOrFail($lang['languageId']);
            $volunteers->order=$lang['orderId'];
            $volunteers->save();
        }
        return response()->json(['success'=>'true']);
    }
}
