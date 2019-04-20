<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\ApiController;
use App\Models\Publications\Publications;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicationsController extends ApiController
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

        $publication = new Publications();

        //store date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $publication->isMonthPresent = true;
            } else {
                $Month = 1;
                $publication->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $publication->date = $date;
            $publication->isPresent = false;
        }  else  if( $request['isPresent'] == true){
            $publication->date = null;
            $publication->isPresent = true;
            $publication->isMonthPresent = false;}
        else{
            // There is no date
            $publication->date= null;
            $publication->isPresent = $request['isPresent'];// isPresent=0
            $publication->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }



        $publication->description = $request['description'];

        $publication->resume_id = $request['resume_id'];

        $publications=Publications::where('resume_id',$request['resume_id'])->get();
        foreach($publications as $pub){
            $pub->order=$pub->order+1;
            $pub->save();
        }
        $publication->order=1;
        $publication->save();

        $newPublication = Publications::where('id' , $publication->id)->first();

        return $this->showOne($newPublication);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publications\Publications  $publications
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $publications = Publications::where('resume_id', $resumeId)
            ->orderBy('order')
            ->get();

        return $this->showAll($publications);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Publications\Publications  $publications
     * @return \Illuminate\Http\Response
     */
    public function edit(Publications $publications)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publications\Publications  $publications
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $this->validate($request,[ 'resume_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);

        $user = auth()->user();

        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required',
            'isPresent' => 'required',
            'isMonthPresent' => 'required',]);
        $publications = Publications::findOrFail( $id);

        //update date
        if ($request['isPresent'] == false && $request['date']['year'] != null) {
            if ($request['isMonthPresent'] == true) {
                $Month = $request['date']['month'];
                $publications->isMonthPresent = true;
            } else {
                $Month = 1;
                $publications->isMonthPresent = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $publications->date = $date;
            $publications->isPresent = false;
        }  else  if( $request['isPresent'] == true){
            $publications->date = null;
            $publications->isPresent = true;
            $publications->isMonthPresent = false;}
        else{
            // There is no date
            $publications->date= null;
            $publications->isPresent = $request['isPresent'];// isPresent=0
            $publications->isMonthPresent = $request['isMonthPresent'];//isMonthPresent=0

        }



        $publications->description = $request['description'];

        $publications->resume_id = $request['resume_id'];

        $publications->save();

        $newPublications = Publications::where('id' ,  $publications->id)->first();

        return $this->showOne($newPublications);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publications\Publications  $publication
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publications $publication)
    {
        $user = auth()->user();
        if ($user->id != $publication->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $publication->delete();
        $publications = Publications::where([['resume_id', $publication->resume_id], ['order', '>', $publication->order]])->get();
        foreach ($publications as $pub) {
            $pub->order = $pub->order - 1;
            $pub->save();
        }

        return $this->showOne($publication);
    }

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $pub){
            $publication=Publications::findOrFail($pub['publicationId']);
            $publication->order=$pub['orderId'];
            $publication->save();
        }
        return response()->json(['success'=>'true']);
    }
}
