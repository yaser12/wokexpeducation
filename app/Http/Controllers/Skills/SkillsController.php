<?php

namespace App\Http\Controllers\Skills;

use App\Http\Controllers\ApiController;
use App\Models\Resume;
use App\Models\Skills\Skill;
use App\Models\Skills\Skills;
use App\Models\Skills\SkillsTypes;
use App\Models\Skills\SkillType;
use App\Models\Skills\SkillTypeParent;
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

        $skills = Skill::where('resume_id', $resumeId)
            ->with(['skill_types','skill_types.skill_type_parents'])
            ->get();

        $skill_types = SkillType::all();
        $skill_types_parent = SkillTypeParent::all();
        return response()->json(['skill' => $skills,
            'skill_types' => $skill_types,
            'skills_types_parent' => $skill_types_parent ]
            ,200);
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
        $this->validate($request, ['resume_id' => 'required', 'skill_types_id' => 'required']);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill = new Skill();
        $skill->resume_id  = $request['resume_id'];
        //store_skill_type
        $skill->skill_types_id = $request['skill_types_id'];
        //store_skill_level
        $skill->skill_level = $request['skill_level'];

        $skills = Skill::where('resume_id', $request['resume_id'])->get();
        foreach ($skills as $sk) {
            $sk->order = $sk->order + 1;
            $sk->save();
        }
        $skill->order = 1;
        $skill->save();

        $newSkill = Skill::where('id',$skill->id)->first();
        $newSkill->skill_types;
        return $this->showOne($newSkill);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $skill = Skill::where('id',$id)
            ->orderBy('order')
            ->with(['skill_types','skill_types.skill_type_parents'])
            ->get();

        return $this->showAll($skill);
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
        $this->validate($request, ['resume_id' => 'required', 'skill_types_id'=>'required']);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill = Skill::findOrFail($id);
        //skill_type
        $skill->skill_types_id = $request['skill_types_id'];
        //skill_level
        $skill->skill_level = $request['skill_level'];

        $skill->save();

        $New_skill = Skill::where('id',$id)
            ->with(['skill_types','skill_types.skill_type_parents'])
            ->get();
        return $this->showAll($New_skill);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);

        $user = auth()->user();
        if ($user->id !=  $skill->resume->user_id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill->delete();

        $skills = Skill::where([['resume_id', $skill->resume_id], ['order', '>',$skill->order]])->get();
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
