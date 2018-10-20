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

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required']);
        $publication = new Publications();

        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $request['date']);
        $publication->date = $date;

        $publication->description = $request['description'];

        $publication->resume_id = $request['resume_id'];

        $publications=Publications::where('resume_id',$request['resume_id'])->get();
        foreach($publications as $lang){
            $lang->order=$lang->order+1;
            $lang->save();
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

        $this->validate($request, ['resume_id'=>'required' , 'description' => 'required']);
        $publications = new Publications();

        $date_time = new \DateTime();
        $date = $date_time->createFromFormat('Y-m-d', $request['date']);
        $publications->date = $date;

        $publications->description = $request['description'];

        $publications->resume_id = $request['resume_id'];

        $publications->save();

        $newPublications = Publications::where('id' ,  $publications->id)->first();

        return $this->showOne($newPublications);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publications\Publications  $publications
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publications $publications)
    {
        $user = auth()->user();
        if ($user->id != $publications->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $publications->delete();
        return $this->showOne($publications);
    }

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $lang){
            $publications=Publications::findOrFail($lang['languageId']);
            $publications->order=$lang['orderId'];
            $publications->save();
        }
        return response()->json(['success'=>'true']);
    }
}
