<?php

namespace App\Http\Controllers\ReReferences;

use App\Http\Controllers\ApiController;
use App\Models\Country\Country;
use App\Models\ReReference\ReferenceInformation;
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
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $reference_available = ReReference::where('resume_id', $resume->id)
//            ->where('is_available', false)
            ->with(array('reference_info.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
//                $query->select(['country_id','name']);

            }))


//                  ->with(array('reference_info' => function ($query)  {
////                $query->where('translated_languages_id', $resume_translated_language);
//                      $query->select(['re_reference_id','name','country_id']);
//                  }))
            ->get();
        /*      $ref = ReReference::where('resume_id', $resume->id)->first();
              $ref_info = ReferenceInformation::where('re_reference_id', $ref->id)
                  ->with(array('country.countryTranslation' => function ($query) use ($resume_translated_language) {
                      $query->where('translated_languages_id', $resume_translated_language);
                  }))->get();
        */
        //Return the success response data
        return response()->json([
            'references' => $reference_available,
//            'references_information' => $ref_info,
        ]);
    }

    public function referencesData($resume_id)
    {
        $resume = Resume::findOrFail($resume_id);
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $country_code = Country::with(array('countryTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))->get();
        //Return the success response data
        return response()->json([
            'country_codes' => $country_code
        ]);
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
        $resume = Resume::findOrFail($request->resume_id);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        //         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $re_ref1 = ReReference::where('resume_id', $resume->id)->first();
        if ($re_ref1 != null) {
            $re_ref = ReReference::where('resume_id', $resume->id)->first();
            $re_ref->save();
        } else {
            $re_ref = new ReReference();
            $re_ref->resume_id = $resume->id;
            $re_ref->is_available = false;//  default show data in preview
            $re_ref->save();
        }
        $reference_info = new ReferenceInformation();
        $reference_info->name = $request['name'];
        $reference_info->position = $request['position'];
        $reference_info->organization = $request['organization'];
        $reference_info->preferred_time_to_call = $request['preferred_time_to_call'];
        $reference_info->email = $request['email'];
        $reference_info->re_reference_id = $re_ref->id;
        $reference_info->save();
        if ($request['contact_number'] != null) {
            $req_contact_number = $request['contact_number'];
            $reference_info->country_id = $req_contact_number['country_id'];
            $reference_info->mobile = $req_contact_number['mobile'];
        }
        $reference_info->save();
        $Reference_info = ReferenceInformation::where('re_reference_id', $re_ref->id)->get();
        foreach ($Reference_info as $ref1) {
            $ref1->order = $ref1->order + 1;
            $ref1->save();
        }
        $reference_info->order = 1;
        $reference_info->save();

        $newReference = ReferenceInformation::where('id', $reference_info->id)
            ->with(array('country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->first();
        return $this->showOne($newReference);
    }

    public function is_available($resume_id, Request $request)
    {

        $resume = Resume::findOrFail($resume_id);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $re_ref1 = ReReference::where('resume_id', $resume_id)->first();
        if ($re_ref1 != null) {
            $re_ref = ReReference::where('resume_id', $resume_id)->first();
            $re_ref->is_available = $request->is_available;
            $re_ref->save();
        } else {
            $re_ref = new ReReference();
            $re_ref->resume_id = $resume_id;
            $re_ref->is_available = $request->is_available;
            $re_ref->save();
        }
        return $this->showOne($re_ref);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $reference = ReReference::where('resume_id', $resume->id)
//            ->where('is_available', false)
            ->with('reference_info')->
            with(array('reference_info.country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->first();
        return $this->showOne($reference);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resume = Resume::findOrFail($request->resume_id);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        //         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $reference_info = ReferenceInformation::where('id', $id)->first();
        $reference_info->name = $request['name'];
        $reference_info->position = $request['position'];
        $reference_info->organization = $request['organization'];
        $reference_info->preferred_time_to_call = $request['preferred_time_to_call'];
        $reference_info->email = $request['email'];
        $reference_info->save();
        if ($request['contact_number'] != null) {
            $req_contact_number = $request['contact_number'];
            $reference_info->country_id = $req_contact_number['country_id'];
            $reference_info->mobile = $req_contact_number['mobile'];
        }
        $reference_info->save();
        $newReference = ReferenceInformation::where('id', $id)
            ->with(array('country.countryTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->first();
        return $this->showOne($newReference);
//        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($ref_info_id)
    {
        $reference = ReferenceInformation::findOrFail($ref_info_id);
        $user = auth()->user();
//        if ($user->id != $reference->resume->user_id)
//            return $this->errorResponse('you are not authorized to do this operation', 401);

        $reference->delete();

        $references = ReferenceInformation::where([['re_reference_id', $reference->re_reference_id], ['order', '>', $reference->order]])->get();
        foreach ($references as $ref) {
            $ref->order = $ref->order - 1;
            $ref->save();
        }

        return $this->showOne($reference);
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        foreach ($request['orderData'] as $ref) {
            $reference = ReferenceInformation::findOrFail($ref['referenceId']);
            $reference->order = $ref['orderId'];
            $reference->save();
        }
        return response()->json(['success' => 'true']);
    }

}

