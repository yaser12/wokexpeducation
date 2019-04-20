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

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);
        $volunteer = new Volunteers();

        //store date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $volunteer->isMonthPresent = true;
            } else {
                $Month = 1;
                $volunteer->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $volunteer->date = $date;
            $volunteer->isPresent = false;
        }  else  if( $request['isPresent'] == true){
            $volunteer->date = null;
            $volunteer->isPresent = true;
            $volunteer->isMonthPresent = false;}
        else{
            // There is no date
            $volunteer->date= null;
            $volunteer->isPresent = $request['isPresent'];// isPresent=0
            $volunteer->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }

        $volunteer->description = $request['description'];

        $volunteer->resume_id = $request['resume_id'];

        $volunteers=Volunteers::where('resume_id',$request['resume_id'])->get();
        foreach($volunteers as $vol){
            $vol->order=$vol->order+1;
            $vol->save();
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

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);
        $volunteers = Volunteers::findOrFail($id);

        //update date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $volunteers->isMonthPresent = true;
            } else {
                $Month = 1;
                $volunteers->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $volunteers->date = $date;
            $volunteers->isPresent = false;
        } else  if( $request['isPresent'] == true){
            $volunteers->date = null;
            $volunteers->isPresent = true;
            $volunteers->isMonthPresent = false;}
        else{
            // There is no date
            $volunteers->date= null;
            $volunteers->isPresent = $request['isPresent'];// isPresent=0
            $volunteers->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }

        $volunteers->description = $request['description'];

        $volunteers->resume_id = $request['resume_id'];

        $volunteers->save();

        $newVolunteers = Volunteers::where('id' ,  $volunteers->id)->first();

        return $this->showOne($newVolunteers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Volunteers\Volunteers  $volunteer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Volunteers $volunteer)
    {
        $user = auth()->user();
        if ($user->id != $volunteer->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $volunteer->delete();
        $volunteers = Volunteers::where([['resume_id', $volunteer->resume_id],['order','>',$volunteer->order]])->get();
        foreach ($volunteers as $vol) {
            $vol->order = $vol->order-1;
            $vol->save();
        }
        return $this->showOne($volunteer);
    }
    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $vol){
            $volunteer=Volunteers::findOrFail($vol['volunteerId']);
            $volunteer->order=$vol['orderId'];
            $volunteer->save();
        }
        return response()->json(['success'=>'true']);
    }
}
