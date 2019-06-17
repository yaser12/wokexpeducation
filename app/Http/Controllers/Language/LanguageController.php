<?php

namespace App\Http\Controllers\Language;

use App\Models\Language\InternationalLanguageTrans;
use App\Models\Language\Language;
use App\Models\Language\LanguageAssessment;
use App\Models\Language\SelfAssessment;
use App\Models\Language\SelfAssessmentTrans;
use App\Models\Resume;
use App\Models\Diploma\Diploma;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use function response;

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'resume_id' => 'required',
            'international_language_id' => 'required',
            'type' => 'required'
        ];

//        $otherRules = [
//            'listening' => 'required',
//            'reading' => 'required',
//            'speaking' => 'required',
//            'writing' => 'required'
//        ];

        $this->validate($request, $rules);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        return DB::transaction(function () use ($request, $resume_translated_language) {

            $language = new Language();
            $language->resume_id = $request['resume_id'];
//            $language->language_id = $request['language_id'];
            $language->international_language_id = $request['international_language_id'];
            $language->type = $request['type'];
            $language->save();

            if ($request['type'] === 'other') {
//                $this->validate($request, $otherRules);
                foreach ($request->self_assessments as $value) {
                    $assessment = new Request($value);
                    $ass = new LanguageAssessment();
                    $ass->language_id = $language->id;
                    $ass->self_assessment_id = $assessment->self_assessment_id;
                    $ass->assessment_type = $assessment->assessment_type;
                    $ass->save();
                }

                /*   $language->listening = $request['listening'];
                   $language->reading = $request['reading'];
                   $language->speaking = $request['speaking'];
                   $language->writing = $request['writing'];*/
            }
            $languages = Language::where('resume_id', $request['resume_id'])->get();
            foreach ($languages as $lang) {
                $lang->order = $lang->order + 1;
                $lang->save();
            }
            $language->order = 1;
            $language->save();

            $newlanguage = Language::where('id', $language->id)
                ->with(['diplomas'])
                ->with(array('internationalLanguage.internationalLanguageTrans' => function ($query) use ($resume_translated_language) {
                    $query->where('translated_languages_id', $resume_translated_language);
                }))
                ->with(array('languageAssessment.selfAssessment.selfAssessmentTrans' => function ($query) use ($resume_translated_language) {
                    $query->where('translated_languages_id', $resume_translated_language);
                }))
                ->first();
            return $this->showOne($newlanguage);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Language\Language $language
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        //resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        $languages = Language::where('resume_id', $resumeId)
            ->orderBy('order')
            ->with(['diplomas'])
            ->with(array('internationalLanguage.internationalLanguageTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('languageAssessment.selfAssessment.selfAssessmentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->get();
        return response()->json([
            'languages' => $languages,], 200);
    }
    public function languageData($resume_id)
    {
        $resume = Resume::findOrFail($resume_id);
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $languages_trans = InternationalLanguageTrans::where('translated_languages_id', $resume_translated_language)
            ->get(['international_language_id', 'name']);

        $self_assessment_trans = SelfAssessmentTrans::where('translated_languages_id', $resume_translated_language)->get();

        return response()->json([
            'language_translations' => $languages_trans,
            'self_assessment_translations' => $self_assessment_trans,
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Language\Language $language
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        $rules = [
            'resume_id' => 'required',
            'international_language_id' => 'required',
            'type' => 'required'
        ];

//        $otherRules = [
//            'listening' => 'required',
//            'reading' => 'required',
//            'speaking' => 'required',
//            'writing' => 'required'
//        ];

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

        //resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        return DB::transaction(function () use ($request, $language, $diplomaRules, $resume_translated_language) {

            $language->resume_id = $request['resume_id'];
//            $language->language_id = $request['language_id'];
            $language->international_language_id = $request['international_language_id'];
            $language->type = $request['type'];
            $language->save();

            if ($request['type'] === 'other') {
                $language->languageAssessment()->delete();
//                $this->validate($request, $otherRules);
                foreach ($request->self_assessments as $value) {
                    $assessment = new Request($value);
                    $ass = new LanguageAssessment();
                    $ass->language_id = $language->id;
                    $ass->self_assessment_id = $assessment->self_assessment_id;
                    $ass->assessment_type = $assessment->assessment_type;
                    $ass->save();
                }
            }

            $language->save();

            if ($request->has('diplomas')) {
                $language->diplomas()->delete();
                foreach ($request['diplomas'] as $value) {
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
            $language1 = Language::where('id', $language->id)
                ->with(['diplomas'])
                ->with(array('internationalLanguage.internationalLanguageTrans' => function ($query) use ($resume_translated_language) {
                    $query->where('translated_languages_id', $resume_translated_language);
                }))
                ->with(array('languageAssessment.selfAssessment.selfAssessmentTrans' => function ($query) use ($resume_translated_language) {
                    $query->where('translated_languages_id', $resume_translated_language);
                }))
                ->first();
            return $this->showOne($language1);
        });
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach ($request['orderData'] as $lang) {
            $Language = Language::findOrFail($lang['languageId']);
            $Language->order = $lang['orderId'];
            $Language->save();
        }
        return response()->json(['success' => 'true']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Language\Language $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Language $language)
    {
        $user = auth()->user();
        if ($user->id != $language->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $language->diplomas()->delete();
        $language->languageAssessment()->delete();
        $language->delete();

        return $this->showOne($language);
    }
}
