<?php

namespace App\Http\Controllers\Skills;

use App\Http\Controllers\ApiController;
use App\Models\Resume;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillLevelTrans;
use App\Models\Skills\Skills;
use App\Models\Skills\SkillsTypes;
use App\Models\Skills\SkillType;
use App\Models\Skills\SkillTypeParent;
use App\Models\Skills\SkillTypeParentTrans;
use App\Models\Skills\SkillTypeTrans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SkillsController extends ApiController
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
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        //resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        $skills = Skill::where('resume_id', $resumeId)
            ->orderBy('order')
            ->with(['skill_types', 'skill_types.skill_type_parents'])
            ->with(array('skill_types.skillTypeTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skill_types.skill_type_parents.skillTypeParentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skillLevel.skillLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))->get();
        return response()->json(['skill' => $skills,], 200);
    }

    public function skillsData($resume_id)
    {
        $resume = Resume::findOrFail($resume_id);
//         resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $skill_types_trans = SkillTypeTrans::where('translated_languages_id', $resume_translated_language)
            ->get(['skill_type_id', 'name']);

        $skill_types_parent_trans = SkillTypeParentTrans::where('translated_languages_id', $resume_translated_language)->
        get(['skill_type_parent_id', 'name']);

        $skill_level_trans = SkillLevelTrans::where('translated_languages_id', $resume_translated_language)->
        get(['skill_level_id', 'name']);
//        $skill_types_basic_parent = SkillType::
//        with(array('skill_type_parents.skillTypeBasicParent.skillTypeBasicParentTrans' => function ($query) use ($resume_translated_language) {
//            $query->where('translated_languages_id', $resume_translated_language);
//        }))->get();


        // $skill_types = SkillType::all();
        // $skill_types_parent = SkillTypeParent::all();
        return response()->json([
            'skill_types_trans' => $skill_types_trans,
            'skill_types_parent_trans' => $skill_types_parent_trans,
            'skill_level' => $skill_level_trans,
//            'skill_types_basic_parent' => $skill_types_basic_parent
        ], 200);
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
        $this->validate($request, ['resume_id' => 'required', 'skill_types_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill = new Skill();
        $skill->resume_id = $request['resume_id'];
        //store_skill_type
        $skill->skill_types_id = $request['skill_types_id'];
        //store_skill_level_id
        $skill->skill_level_id = $request['skill_level_id'];

        $skills = Skill::where('resume_id', $request['resume_id'])->get();
        foreach ($skills as $sk) {
            $sk->order = $sk->order + 1;
            $sk->save();
        }
        $skill->order = 1;
        $skill->save();

        //       resume translated language
        $resume_translated_language = $resume->translated_languages_id;
        $newSkill = Skill::where('id', $skill->id)
            ->with(array('skillLevel.skillLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skill_types.skillTypeTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->first();
        return $this->showOne($newSkill);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $skill = Skill::where('id', $id)
            ->orderBy('order')
            ->with(['skill_types', 'skill_types.skill_type_parents', 'skillLevel'])
            ->first();

        return $this->showOne($skill);
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
        $this->validate($request, ['resume_id' => 'required', 'skill_types_id' => 'required']);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //resume translated language
        $resume_translated_language = $resume->translated_languages_id;

        $skill = Skill::findOrFail($id);
        //skill_type
        $skill->skill_types_id = $request['skill_types_id'];
        //skill_level
        $skill->skill_level_id = $request['skill_level_id'];

        $skill->save();

        /* $New_skill = Skill::where('id',$id)
             ->with(['skill_types','skill_types.skill_type_parents'])
             ->get();*/

        $New_skill = Skill::where('id', $skill->id)
            ->with(['skill_types', 'skill_types.skill_type_parents'])
            ->with(array('skill_types.skillTypeTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skill_types.skill_type_parents.skillTypeParentTrans' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->with(array('skillLevel.skillLevelTranslation' => function ($query) use ($resume_translated_language) {
                $query->where('translated_languages_id', $resume_translated_language);
            }))
            ->first();
        return $this->showOne($New_skill);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);

        $user = auth()->user();
        if ($user->id != $skill->resume->user_id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill->delete();

        $skills = Skill::where([['resume_id', $skill->resume_id], ['order', '>', $skill->order]])->get();
        foreach ($skills as $sk) {
            $sk->order = $sk->order - 1;
            $sk->save();
        }
        return $this->showOne($skill);
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach ($request['orderData'] as $sk) {
            $skill = Skill::findOrFail($sk['skillId']);
            $skill->order = $sk['orderId'];
            $skill->save();
        }
        return response()->json(['success' => 'true']);
    }
}
