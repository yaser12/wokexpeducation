<?php

namespace App\Http\Controllers\Achievements;

use App\Http\Controllers\ApiController;
use App\Models\Achievements\Achievements;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth ; 

use function response;

class AchievementsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of achievements associated with single resume.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Resume $resume)
    {
        //Authorization
        $user = Auth::user();  
        if($user->id != $resume->user_id){
           return  $this->errorResponse('you are not authorized to do this operation', 401);
        
        }

        //fetch the achievements associated with the resume 
        $achievements = $resume->achievements()
                        ->orderBy('order')
                        ->get(); 


        
        //Return the success response data 
        return $this->showAll($achievements);

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

      //validation 
      $this->validate($request,[ 'resume_id' => 'required']);

      $resume = Resume::findOrFail($request['resume_id']);


        //Authorization 
        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        //validate
        $this->validate($request, ['resume_id'=>'required' ,
            'description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);

        $achievement = new Achievements();

        //store date
//
            if ($request['isPresent'] == false &&  $request['date']['year'] != null) {
                if ($request['isMonthPresent'] == true) {
                    $Month = $request['date']['month'];
                    $achievement->isMonthPresent = true;
                } else {
                    $Month = 1;
                    $achievement->isMonthPresent = false;
                }
                $Year = $request['date']['year'];
                $Day = 1;
                $date_string = $Year . "-" . $Month . "-" . $Day;
                $date_time = new \DateTime();
                $date = $date_time->createFromFormat('Y-m-d', $date_string);
                $achievement->date = $date;
                $achievement->isPresent = false;
            }
              else  if( $request['isPresent'] == true){
                $achievement->date = null;
                $achievement->isPresent = true;
                  $achievement->isMonthPresent = false;}
                else{
                  // There is no date
                    $achievement->date= null;
                    $achievement->isPresent = $request['isPresent'];// isPresent=0
                    $achievement->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

                }



//
//        if ( $request['date']['year']!= null ){
//            $year =$request['date']['year'];
//        }
//        if ( $request['date']['month']!= null ){
//            $month =$request['date']['month'];
//        }
//        if ( $request['date']['day']!= null ){
//            $day =  $request['date']['day'];
//        }
//        //handle the case the date is null
//        if(isset($year) && isset($month) && isset($day)){
//            $date_string = $year . "-" . $month . "-" . $day;
//            $date_time = new \DateTime();
//            $date = $date_time->createFromFormat('Y-m-d', $date_string);
//            $achievement->date = $date;
//        }
        
        //store description
        $achievement->description = $request['description'];


        //associate with resume 
        $achievement->resume_id = $request['resume_id'];

        //Update Orders of achievements associated with current resume
        $achievements=Achievements::where('resume_id',$request['resume_id'])->get();
        foreach($achievements as $ach){
            $ach->order=$ach->order+1;
            $ach->save();
        }
        $achievement->order=1;


        //persist the achievment in the database 
        
        $achievement->save();

        //fetch the newly created achievement from the database 
        $newAchievement = Achievements::where('id' , $achievement->id)->first();

        return $this->showOne($newAchievement);

        }

    /**
     * Display the specified Achievement.
     *
     * @param  \App\Models\Achievements\Achievements  $achievements
     * @return \Illuminate\Http\Response
     */
    public function show(Achievements $achievement)
    {
        //Fetch the Resume Associated with the Achievement 

        $resume = $achievement->resume ;
        
        //Authorization

        $user = auth()->user();

        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //return single achievement

        return $this->showOne($achievement);
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

        //validate Resume Id 
        $this->validate($request,['resume_id' => 'required',]);


        //Authorization
        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        //validate
        $this->validate($request, ['description' => 'required'
        ,  'isPresent' => 'required',
            'isMonthPresent' => 'required']);

        //fetch the target achievment from the database 
        $achievement = Achievements::findOrFail($id);

        //update date
//
        if ($request['isPresent'] == false &&  $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $achievement->isMonthPresent = true;
            } else {
                $Month = 1;
                $achievement->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $achievement->date = $date;
            $achievement->isPresent = false;
        }
        else  if( $request['isPresent'] == true){
            $achievement->date = null;
            $achievement->isPresent = true;
            $achievement->isMonthPresent = false;}
        else{
            // There is no date
            $achievement->date= null;
            $achievement->isPresent = $request['isPresent'];// isPresent=0
            $achievement->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }

//        if ( $request['date']['year']!= null ){
//            $year =$request['date']['year'];
//        }
//        if ( $request['date']['month']!= null ){
//            $month =$request['date']['month'];
//        }
//        if ( $request['date']['day']!= null ){
//            $day =  $request['date']['day'];
//        }
//        //handle the case the date is null
//        if(isset($year) && isset($month) && isset($day)){
//
//            $date_string = $year . "-" . $month . "-" . $day;
//            $date_time = new \DateTime();
//            $date = $date_time->createFromFormat('Y-m-d', $date_string);
//            $achievement->date = $date;
//        }

        /*
        $date_time = new \DateTime();

        $date = $date_time->createFromFormat('Y-m-d', $request['date']);

        $achievement->date = $date;
        */

        //store description
        $achievement->description = $request['description'];
        
        //store resume_id 
        $achievement->resume_id = $request['resume_id'];
        
        //persist achievment to database 
        $achievement->save();
        
        //fetch the persisted achievement from database 
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


        $achievement->delete();
        $achievements = Achievements::where([['resume_id', $achievement->resume_id],['order','>',$achievement->order]])->get();
        foreach ($achievements as $ach) {
            $ach->order = $ach->order-1;
            $ach->save();
        }

       

        return $this->showOne($achievement);
    }

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);

        //Authorization
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
