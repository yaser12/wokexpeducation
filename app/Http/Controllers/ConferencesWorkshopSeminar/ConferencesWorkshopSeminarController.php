<?php

namespace App\Http\Controllers\ConferencesWorkshopSeminar;

use App\Models\ConferencesWorkshopSeminar\ConferencesWorkshopSeminar;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ConferencesWorkshopSeminarController extends ApiController
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
    public function index(Resume $resume)
    {

        $Con_work_sem = $resume->ConferencesWorkshopSeminar()
            ->orderBy('order')
            ->get();
        //Return the success response data
        return $this->showAll($Con_work_sem);

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
        $this->validate($request, [
            'resume_id' => 'required',
            'type' => 'required',
            'isMonth' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $conferences_workshop_seminar = new ConferencesWorkshopSeminar();

        //associate with resume
        $conferences_workshop_seminar->resume_id = $request['resume_id'];

        //store description
        $conferences_workshop_seminar->description = $request['description'];

        //store type
        $conferences_workshop_seminar->type = $request['type'];

        if($request['type'] === 'conference')
        {
            $this->validate($request,['attended_as' => 'required']);
            $conferences_workshop_seminar->attended_as =$request['attended_as'];

        }

        //store date
//
        if ( $request['date']['year']!= null ){

            if ($request['isMonth'] == true) {
                $Month = $request['date']['month'];
                $conferences_workshop_seminar->isMonth = true;
            } else {
                $Month = 1;
                $conferences_workshop_seminar->isMonth = false;
            }
            $Year =$request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $conferences_workshop_seminar->date = $date;
        }else{
            $conferences_workshop_seminar->date =null;
            $conferences_workshop_seminar->isMonth =null;
        }



        $conferences_workshop_seminars=ConferencesWorkshopSeminar::where('resume_id',$request['resume_id'])->get();
        foreach($conferences_workshop_seminars as $Con_work_sem){
            $Con_work_sem->order=$Con_work_sem->order+1;
            $Con_work_sem->save();

        }
        $conferences_workshop_seminar->order=1;
        $conferences_workshop_seminar->save();

        //fetch the newly created conferences_workshop_seminar from the database
        $newConferences_workshop_seminar = ConferencesWorkshopSeminar::where('id' , $conferences_workshop_seminar->id)->first();
        return $this->showOne($newConferences_workshop_seminar);
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Models\ConferencesWorkshopSeminar\ConferencesWorkshopSeminar $conferences_workshop_seminar
     * @return \Illuminate\Http\Response
     */
    public function show( $con_work_sem)
    {
        $conferencesWorkshopSeminar= ConferencesWorkshopSeminar::findOrFail($con_work_sem);

        $resume = $conferencesWorkshopSeminar->resume ;

        //Authorization

        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //return single conferencesWorkshopSeminar

        return $this->showOne($conferencesWorkshopSeminar);
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'resume_id' => 'required',
            'type' => 'required',
            'isMonth' => 'required',

        ]);
        $resume = Resume::findOrFail($request['resume_id']);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $conferences_workshop_seminar = ConferencesWorkshopSeminar::findOrFail($id);

        //associate with resume
        $conferences_workshop_seminar->resume_id = $request['resume_id'];

        // description
        $conferences_workshop_seminar->description = $request['description'];

        // type
        $conferences_workshop_seminar->type = $request['type'];

        if($request['type'] === 'conference')
        {
            $this->validate($request,['attended_as' => 'required']);
            $conferences_workshop_seminar->attended_as =$request['attended_as'];

        }

        //update date
//
        if ( $request['date']['year']!= null ){

            if ($request['isMonth'] == true) {
                $Month = $request['date']['month'];
                $conferences_workshop_seminar->isMonth = true;
            } else {
                $Month = 1;
                $conferences_workshop_seminar->isMonth = false;
            }
            $Year =$request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $conferences_workshop_seminar->date = $date;
        }else{
            $conferences_workshop_seminar->date =null;
            $conferences_workshop_seminar->isMonth =null;
        }

        $conferences_workshop_seminar->save();

        //fetch the newly created conferences_workshop_seminar from the database
        $newConferences_workshop_seminar = ConferencesWorkshopSeminar::where('id' , $conferences_workshop_seminar->id)->first();
        return $this->showOne($newConferences_workshop_seminar);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\ConferencesWorkshopSeminar\ConferencesWorkshopSeminar $con_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($con_id)
    {
        $conferencesWorkshopSeminar= ConferencesWorkshopSeminar::findOrFail($con_id);
        $user = auth()->user();


        if ($user->id != $conferencesWorkshopSeminar->resume->user_id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $conferencesWorkshopSeminar->delete();

        $conferences_workshop_seminars = ConferencesWorkshopSeminar::where([['resume_id', $conferencesWorkshopSeminar->resume_id],['order','>',$conferencesWorkshopSeminar->order]])->get();
        foreach ($conferences_workshop_seminars as $con) {
            $con->order = $con->order-1;
            $con->save();
        }
        return $this->showOne($conferencesWorkshopSeminar);
    }


    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        foreach($request['orderData'] as  $con){

            $conferences_workshop_seminar=ConferencesWorkshopSeminar::findOrFail($con['conferences_workshop_seminarId']);
            $conferences_workshop_seminar->order=$con['orderId'];
            $conferences_workshop_seminar->save();
        }
        return response()->json(['success'=>'true']);
    }
}