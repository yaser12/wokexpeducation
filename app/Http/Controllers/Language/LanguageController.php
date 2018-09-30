<?php

namespace App\Http\Controllers\Language;

use App\Models\Language\Language;
use App\Models\Resume;
use App\Models\Diploma\Diploma;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class LanguageController extends ApiController
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'resume_id' => 'required',
            'language_id' => 'required',
            'type' => 'required'
        ];

        $otherRules = [
            'listening' => 'required',
            'reading' => 'required',
            'speaking' => 'required',
            'writing' => 'required'
        ];

        $this->validate($request, $rules);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request, $otherRules) {

            $language = new Language();
            $language->resume_id = $request['resume_id'];
            $language->language_id = $request['language_id'];
            $language->type = $request['type'];

            if($request['type'] === 'other')
            {
                $this->validate($request, $otherRules);

                $language->listening = $request['listening'];
                $language->reading = $request['reading'];
                $language->speaking = $request['speaking'];
                $language->writing = $request['writing'];
            }
            $languages=Language::where('resume_id',$request['resume_id'])->get();
            foreach($languages as $lang){
                $lang->order=$lang->order+1;
                $lang->save();
            }
            $language->order=1;
            $language->save();
            $newLanguage = Language::where('id', $language->id);
            $newLanguage->diplomas;
            return $this->showOne($newLanguage);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Language\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $languages = Language::where('resume_id', $resumeId)
            ->orderBy('order')
            ->with(['diplomas'])
            ->get();

        return $this->showAll($languages);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Language\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        $rules = [
            'resume_id' => 'required',
            'language_id' => 'required',
            'type' => 'required'
        ];

        $otherRules = [
            'listening' => 'required',
            'reading' => 'required',
            'speaking' => 'required',
            'writing' => 'required'
        ];

        $diplomaRules = [
            'name' => 'required',
            'grade' => 'required',
            'full_grade' => 'required'
        ];

        $this->validate($request, $rules);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request, $language, $otherRules, $diplomaRules) {

            $language->resume_id = $request['resume_id'];
            $language->language_id = $request['language_id'];
            $language->type = $request['type'];

            if($request['type'] === 'other')
            {
                $this->validate($request, $otherRules);

                $language->listening = $request['listening'];
                $language->reading = $request['reading'];
                $language->speaking = $request['speaking'];
                $language->writing = $request['writing'];
            }

            $language->save();

            if($request->has('diplomas'))
            {
                $language->diplomas()->delete();
                foreach($request['diplomas'] as $value)
                {
                    $diplomaRequest = new Request($value);
                    $this->validate($diplomaRequest, $diplomaRules);

                    $diploma = new Diploma();
                    $diploma->name = $diplomaRequest['name'];
                    $diploma->grade = $diplomaRequest['grade'];
                    $diploma->full_grade = $diplomaRequest['full_grade'];
                    $diploma->language_id = $language->id;
                    $diploma->save();
                }
            }
            $language->diplomas;
            return $this->showOne($language);
        });
    }
    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $lang){
            $Language=Language::findOrFail($lang['languageId']);
            $Language->order=$lang['orderId'];
            $Language->save();
        }
        return response()->json(['success'=>'true']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Language\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Language $language)
    {
        $user = auth()->user();
        if ($user->id != $language->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $language->diplomas()->delete();
        $language->delete();

        return $this->showOne($language);
    }
}
