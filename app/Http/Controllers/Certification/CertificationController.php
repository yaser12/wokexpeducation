<?php

namespace App\Http\Controllers\Certification;

use App\Http\Controllers\ApiController;
use App\Models\Certifications\Certifications;
use App\Models\Certifications\ValidYear;
use App\Models\Country\Country;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CertificationController extends ApiController
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
    public function index($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        //       resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        $certification = Certifications::where('resume_id', $resumeId)
            ->with(array('validYear.validYearTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->orderBy('order')
            ->get();
        //Return the success response data
        return $this->showAll($certification);
    }

    public function certificationData($resume_id)
    {
        $resume = Resume::findOrFail($resume_id);
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        $valid_for = ValidYear::with(array('validYearTranslation' => function ($query) use ($resume_translated_language) {
            $query->where('translated_languages_id', $resume_translated_language);
        }))->get(['id']);

        return response()->json([
            'valid_for' => $valid_for
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
     * Store a newly created certification in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $resume = Resume::findOrFail($request['resume_id']);
        //         resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        //validation
        $this->validate($request, ['resume_id' => 'required',
            'name' => 'required',
            'organization' => 'required',
            'isMonth' => 'required',
            'date' => 'required',
        ]);
        $certification = new Certifications();

        //associate with resume
        $certification->resume_id = $request['resume_id'];

        $certification->name = $request['name'];
        $certification->organization = $request['organization'];
        $certification->valid_year_id = $request['valid_year_id'];

        //store date
        if ($request['date']['year'] != null) {

            if ($request['isMonth'] == true) {
                $Month = $request['date']['month'];
                $certification->isMonth = true;
            } else {
                $Month = 1;
                $certification->isMonth = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $certification->date = $date;
        } else {
            $certification->date = null;
            $certification->isMonth = false;
        }
        $certification->description = null;

        $certifications = Certifications::where('resume_id', $request['resume_id'])->get();
        foreach ($certifications as $Cer) {
            $Cer->order = $Cer->order + 1;
            $Cer->save();

        }
        $certification->order = 1;
        $certification->save();
        //fetch the newly created certification from the database
        $newCertification = Certifications::where('id', $certification->id)
            ->with(array('validYear.validYearTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->first();
        return $this->showOne($newCertification);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Certifications $certification)
    {
        $resume = $certification->resume;

        //Authorization

        $user = auth()->user();

        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //return single $certification

        return $this->showOne($certification);
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
        //validation
        $this->validate($request, ['resume_id' => 'required',
            'name' => 'required',
            'organization' => 'required',
            'isMonth' => 'required',
            'date' => 'required',
        ]);;
        $resume = Resume::findOrFail($request['resume_id']);
        //         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $certification = Certifications::findOrFail($id);

        //associate with resume
        $certification->resume_id = $request['resume_id'];
        $certification->name = $request['name'];
        $certification->organization = $request['organization'];
        $certification->valid_year_id = $request['valid_year_id'];

        //update date
//
        if ($request['date']['year'] != null) {

            if ($request['isMonth'] == true) {
                $Month = $request['date']['month'];
                $certification->isMonth = true;
            } else {
                $Month = 1;
                $certification->isMonth = false;
            }
            $Year = $request['date']['year'];
            $Day = 1;
            $date_string = $Year . "-" . $Month . "-" . $Day;
            $date_time = new \DateTime();
            $date = $date_time->createFromFormat('Y-m-d', $date_string);
            $certification->date = $date;
        }
        if ($request->has('description')) {
            $certification->description = $request->description;
        } else $certification->description = null;

        $certification->save();

        $newCertification = Certifications::where('id', $certification->id)
            ->with(array('validYear.validYearTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->first();
        return $this->showOne($newCertification);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Certifications\Certifications $certification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certifications $certification)
    {
        $user = auth()->user();
        if ($user->id != $certification->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $certification->delete();
        $certifications = Certifications::where([['resume_id', $certification->resume_id], ['order', '>', $certification->order]])->get();
        foreach ($certifications as $cer) {
            $cer->order = $cer->order - 1;
            $cer->save();
        }
        return $this->showOne($certification);
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        foreach ($request['orderData'] as $cer) {
            $certification = Certifications::findOrFail($cer['certificationId']);
            $certification->order = $cer['orderId'];
            $certification->save();
        }
        return response()->json(['success' => 'true']);
    }
}
