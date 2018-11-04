<?php

namespace App\Http\Controllers\Achievements;

use App\Http\Controllers\ApiController;
use App\Models\Achievements\Achievements;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use function response;

class AchievementsController extends ApiController
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

        $achievement = new Achievements();
        
        if ( $request['date']['year']!= null ){
            $year =$request['date']['year'];
        }
        if ( $request['date']['month']!= null ){
            $month =$request['date']['month'];
        }
        if ( $request['date']['day']!= null ){
            $day =  $request['date']['day'];
        }

        $date_string = $year . "-" . $month . "-" . $day;
        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $date_string);
        $achievement->date = $date;

        $achievement->description = $request['description'];

        $achievement->resume_id = $request['resume_id'];


        $achievements=Achievements::where('resume_id',$request['resume_id'])->get();
        foreach($achievements as $lang){
            $lang->order=$lang->order+1;
            $lang->save();
        }
        $achievement->order=1;

        $achievement->save();

        $newAchievement = Achievements::where('id' , $achievement->id)->first();

        return $this->showOne($newAchievement);

        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Achievements\Achievements  $achievements
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $achievements = Achievements::where('resume_id', $resumeId)
            ->orderBy('order')
            ->get();

        return $this->showAll($achievements);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Achievemens\Achievements  $achievements
     * @return \Illuminate\Http\Response
     */
    public function edit(Achievements $achievements)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Achievemens\Achievements  $achievements
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $this->validate($request,[ 'resume_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

            $this->validate($request, ['description' => 'required']);

            $achievement = Achievements::findOrFail( $id);

            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $request['date']);
            $achievement->date = $date;
            $achievement->description = $request['description'];
            $achievement->resume_id = $request['resume_id'];
            $achievement->save();
            $newAchievements = Achievements::where('id', $achievement->id)->first();

            return $this->showOne($newAchievements);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Achievemens\Achievements  $achievements
     * @return \Illuminate\Http\Response
     */
    public function destroy(Achievements $achievement)
    {
        
        $user = auth()->user();
        if ($user->id != $achievement->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $achievement>delete();
        return $this->showOne($achievement);
    }

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        

        foreach($request['orderData'] as $ach){
            $achievement=Achievements::findOrFail($ach['achievementId']);
            $achievement->order=$ach['orderId'];
            $achievement->save();
        }
        return response()->json(['success'=>'true']);
    }
}
