<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\ApiController;
use App\Models\Resume;
use App\Models\SummarySec\Summary;
use Illuminate\Http\Request;

class SummaryController extends ApiController
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['description' => 'required', 'resume_id'=>'required']);
        $resume=Resume::findOrFail($request->resume_id);
        if($resume->summary !=null){
            return $this->errorResponse('Trying To Access Filled Filed', 409);
        }
        $data['description'] = $request->description;
        $data['resume_id'] = $request->resume_id;
        $summary = Summary::create($data);
        return $this->showOne($summary);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SummarySec\Summary $summary
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId){
        $resume=Resume::findOrFail($resumeId);
        if($resume->summary==null){
            return response()->json(['data' => 404]);
        }
        $summary = $resume->summary;
        return $this->showOne($summary);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SummarySec\Summary $summary
     * @return \Illuminate\Http\Response
     */
    public function edit(Summary $summary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\SummarySec\Summary $summary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Summary $summary)
    {
        $this->validate($request,['description'=>'required' , 'resume_id'=>'required']);
        $summary->description = $request->description;
        $summary->save();
        return $this->showOne($summary);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SummarySec\Summary $summary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Summary $summary)
    {
        $summaryData= $summary;
        $summary->delete();
        return $summaryData;
    }
}

