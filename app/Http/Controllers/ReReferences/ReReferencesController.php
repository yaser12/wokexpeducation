<?php

namespace App\Http\Controllers\ReReferences;

use App\Http\Controllers\ApiController;
use App\Models\ReReference\ReReference;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReReferencesController extends ApiController
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
        $reference = $resume->reReferences()
            ->orderby('order')
            ->get() ;
        //Return the success response data
        return $this->showAll($reference);
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
        $this->validate($request, ['name' => 'required', 'resume_id'=>'required',
           'ref_email_address'=>'required' ]);
        $resume=Resume::findOrFail($request->resume_id);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $reference = ReReference::create([
            'resume_id'=>$request->resume_id,
            'name'=>$request->name,
            'position'=>$request->position,
            'organization'=>$request->organization,
            'prefered_time_to_call'=>$request->prefered_time_to_call,
            'ref_email_address'=>$request->ref_email_address,
            'is_available' => $request->is_available
        ]);

        if($request->has('contact_number')){
            $reqcontact_number = $request['contact_number'];
            $reference->country_code = $reqcontact_number['country_code']['code'];
            $reference->mobile = $reqcontact_number['mobile'];
        }

        $references = ReReference::where('resume_id',$request['resume_id'])->get();
        foreach($references as $ref){
            $ref->order=$ref->order+1;
            $ref->save();
        }
        $reference->order=1;
        $reference->save();
        $newrefernce=ReReference::where('id',$reference->id)->first();
        return $this->showOne($newrefernce);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $references =ReReference::where('Resume_id',$resumeId)
            ->orderBy('order')
            ->get();
        return $this->showAll($references);
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
        $this->validate($request,[
            'name'=>'required',
            'resume_id'=>'required',
            'ref_email_address'=>'required'
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $reference = ReReference::findOrFail($id);

        $reference->resume_id = $request->resume_id;
        $reference->name = $request->name;
        $reference->position = $request->position;
        $reference->organization = $request->organization;
        $reference->prefered_time_to_call = $request->prefered_time_to_call;
        $reference->ref_email_address = $request->ref_email_address;
        $reference->is_available = $request->is_available;

        if($request->has('contact_number')){
            $reqcontact_number = $request['contact_number'];
            $reference->country_code = $reqcontact_number['country_code']['code'];
            $reference->mobile = $reqcontact_number['mobile'];
        }


       /* if($request->has('is_available')){
            $reference->is_available = true;
        }else{
            $reference->is_available = false;
        }*/

        $reference->save();
        $newReference = ReReference::where('id',$reference->id)->first();
        return $this->showOne($newReference);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ref_id)
    {
        $reference = ReReference::findOrFail($ref_id);
        $user = auth()->user();


        if ($user->id != $reference->resume->user_id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $reference->delete();

        $references = ReReference::where([['resume_id',$reference->resume_id],['order','>',$reference->order]]);
        foreach ($references as $ref) {
            $ref->order = $ref->order - 1;
            $ref->save();
        }

        return $this->showOne($reference);
    }

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        foreach($request['orderData'] as  $ref){

            $reference=ReReference::findOrFail($ref['referenceId']);
            $reference->order=$ref['orderId'];
            $reference->save();
        }
        return response()->json(['success'=>'true']);
    }

}

