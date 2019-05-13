<?php

namespace App\Http\Controllers\Skills;

use App\Http\Controllers\ApiController;
use App\Models\Resume;
use App\Models\Skills\Skills;
use App\Models\Skills\SkillsTypes;
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

        $skills = Skills::where('resume_id', $resumeId)
            ->with(['skills_types','skills_types.parents_types'])
            ->get();

        $skills_types = SkillsTypes::whereNull('parent_id')->with(['childs_types'])->get();

        return response()->json(['skill' => $skills, 'skills_types' => $skills_types ] ,200);
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
        $this->validate($request, ['resume_id' => 'required', 'skills_types_id'=>'required']);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill = new Skills();
        $skill->resume_id  = $request['resume_id'];
        //store_skill_type
        $skill->skills_types_id = $request['skills_types_id'];
        //store_skill_level
        $skill->skill_level = $request['skill_level'];

        $skills = Skills::where('resume_id', $request['resume_id'])->get();
        foreach ($skills as $sk) {
            $sk->order = $sk->order + 1;
            $sk->save();
        }
        $skill->order = 1;
        $skill->save();

        $newSkill = Skills::where('id',$skill->id)->first();
        $newSkill->skills_types;
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
        $skill = Skills::where('id',$id)
            ->orderBy('order')
            ->with([
                'skills_types','skills_types.parents_types'])
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
        $this->validate($request, ['resume_id' => 'required', 'skills_types_id'=>'required']);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill = Skills::findOrFail($id);
        //skill_type
        $skill->skills_types_id = $request['skills_types_id'];
        //skill_level
        $skill->skill_level = $request['skill_level'];

        $skill->save();

        $New_skill = Skills::where('id',$id)
            ->with(['skills_types','skills_types.parents_types'])
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
        $skill = Skills::findOrFail($id);

        $user = auth()->user();
        if ($user->id !=  $skill->resume->user_id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $skill->delete();

        $skills = Skills::where([['resume_id', $skill->resume_id], ['order', '>',$skill->order]])->get();
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
            $skill = Skills::findOrFail($sk['skillId']);
            $skill->order = $sk['orderId'];
            $skill->save();
        }
        return response()->json(['success' => 'true']);
    }
}
