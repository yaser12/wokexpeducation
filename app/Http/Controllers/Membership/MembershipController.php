<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\ApiController;

use App\Models\Membership\Membership;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MembershipController extends ApiController
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

        $this->validate($request, ['resume_id'=>'required' ,
            'description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);
        $membership = new Membership();

        //store date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $membership->isMonthPresent = true;
            } else {
                $Month = 1;
                $membership->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $membership->date = $date;
            $membership->isPresent = false;
        }
        else  if( $request['isPresent'] == true){
            $membership->date = null;
            $membership->isPresent = true;
            $membership->isMonthPresent = false;}
        else{
            // There is no date
            $membership->date= null;
            $membership->isPresent = $request['isPresent'];// isPresent=0
            $membership->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }


        $membership->description = $request['description'];

        $membership->resume_id = $request['resume_id'];

        $memberships=Membership::where('resume_id',$request['resume_id'])->get();
        foreach($memberships as $member){
            $member->order=$member->order+1;
            $member->save();
        }
        $membership->order=1;
        $membership->save();

        $newMembership = Membership::where('id' , $membership->id)->first();

        return $this->showOne($newMembership);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Membership\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $membership = Membership::where('resume_id', $resumeId)
            ->orderBy('order')
            ->get();

        return $this->showAll($membership);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Membership\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function edit(Membership $membership)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Membership\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[ 'resume_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $this->validate($request, ['description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required']);

        $membership = Membership::findOrFail( $id);

        //update date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $membership->isMonthPresent = true;
            } else {
                $Month = 1;
                $membership->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $membership->date = $date;
            $membership->isPresent = false;
        }    else  if( $request['isPresent'] == true){
            $membership->date = null;
            $membership->isPresent = true;
            $membership->isMonthPresent = false;}
        else{
            // There is no date
            $membership->date= null;
            $membership->isPresent = $request['isPresent'];// isPresent=0
            $membership->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }

        $membership->description = $request['description'];
        $membership->resume_id = $request['resume_id'];
        $membership->save();
        $newMembership = Membership::where('id', $membership->id)->first();

        return $this->showOne($newMembership);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Membership\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function destroy(Membership $membership)
    {
        $user = auth()->user();
        if ($user->id != $membership->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $membership->delete();
        $memberships = Membership::where([['resume_id', $membership->resume_id],['order','>',$membership->order]])->get();
        foreach ($memberships as $member) {
            $member->order = $member->order-1;
            $member->save();
        }
        return $this->showOne($membership);
    }
    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $member){
            $membership=Membership::findOrFail($member['membershipId']);
            $membership->order=$member['orderId'];
            $membership->save();
        }
        return response()->json(['success'=>'true']);
    }
}
