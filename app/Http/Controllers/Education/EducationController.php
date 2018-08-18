<?php

namespace App\Http\Controllers\Education;

use App\Models\Education\Education;
use App\Models\Education\EducationProject;
use App\Models\Education\Major;
use App\Models\Education\Minor;
use App\Models\Education\University;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class EducationController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'degree_level' => 'required',
            'university' => 'required',
            'major' => 'required',
            'from' => 'required',
            'isPresent' => 'required',
            'resume_id' => 'required',
        ]);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request) {
            $reqUniversity = $request['university'];
            $reqMajor = $request['major'];
            $reqFrom = $request['from'];
            $reqTo = $request['to'];


            $education = new Education();
            $education->degree_level = $request['degree_level'];
            $education->resume_id = $request['resume_id'];

            $university = University::where('name', $reqUniversity['name'])->first();
            if ($university) {
                $education->university_id = $university->id;
            } else {
                $university = new University();
                $university->name = $reqUniversity['name'];
                $university->url = $reqUniversity['url'];
                $university->description = $reqUniversity['description'];
                $university->country = $reqUniversity['country'];
                $university->city = $reqUniversity['city'];
                $university->street_address = $reqUniversity['street_address'];
                $university->latitude = $reqUniversity['latitude'];
                $university->longitude = $reqUniversity['longitude'];
                $university->save();
                $education->university_id = $university->id;
            }


            $major = Major::where('name', $reqMajor['name'])->first();
            if ($major) {
                $education->major_id = $major->id;
            } else {
                $major = new Major();
                $major->name = $reqMajor['name'];
                $major->save();
                $education->major_id = $major->id;
            }

            $fromMonth = $reqFrom['month'];
            $fromYear = $reqFrom['year'];
            $fromDay = 1;
            $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
            $from_date_time = new \DateTime();
            $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
            $education->from = $from;

            if ($request['isPresent'] == false && $request['to'] != null) {
                $toMonth = $reqTo['month'];
                $toYear = $reqTo['year'];
                $toDay = 1;
                $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
                $to_date_time = new \DateTime();
                $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
                $education->to = $to;
            } else {
                $education->to = null;
            }

            $education->grade=null;
            $education->full_grade=null;
            $education->save();
            $education->university;
            $education->major;
            $education->minor;
            $education->projects;
            return $this->showOne($education);

        });
    }

    public function show($resumeId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $education = Education::where('resume_id', $resumeId)
            ->with(['university', 'major', 'minor', 'projects'])
            ->get();

        return $this->showAll($education);
    }

    public function update(Request $request, Education $education)
    {
        $this->validate($request, [
            'degree_level' => 'required',
            'university' => 'required',
            'major' => 'required',
            'from' => 'required',
            'isPresent' => 'required',
            'resume_id' => 'required',
        ]);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request,$education) {
            $reqUniversity = $request['university'];
            $reqMajor = $request['major'];
            $reqFrom = $request['from'];
            $reqTo = $request['to'];


            $education->degree_level = $request['degree_level'];
            $education->resume_id = $request['resume_id'];

            $university = University::where('name', $reqUniversity['name'])->first();
            if ($university) {
                $education->university_id = $university->id;
            } else {
                $university = new University();
                $university->name = $reqUniversity['name'];
                $university->url = $reqUniversity['url'];
                $university->description = $reqUniversity['description'];
                $university->country = $reqUniversity['country'];
                $university->city = $reqUniversity['city'];
                $university->street_address = $reqUniversity['street_address'];
                $university->latitude = $reqUniversity['latitude'];
                $university->longitude = $reqUniversity['longitude'];
                $university->save();
                $education->university_id = $university->id;
            }


            $major = Major::where('name', $reqMajor['name'])->first();
            if ($major) {
                $education->major_id = $major->id;
            } else {
                $major = new Major();
                $major->name = $reqMajor['name'];
                $major->save();
                $education->major_id = $major->id;
            }

            if($request->has('minor')){
                $reqMinor = $request['minor'];
                $minor = Minor::where('name', $reqMinor['name'])->first();
                if ($minor) {
                    $education->minor_id = $minor->id;
                } else {
                    $minor = new Minor();
                    $minor->name = $reqMinor['name'];
                    $minor->major_id=$reqMinor['major_id'];
                    $minor->save();
                    $education->minor_id = $minor->id;
                }

            }else {$education->minor_id=null;}

            if($request->has('projects')){
                $reqProjects = $request['projects'];
                if($reqProjects != null){
                    $education->projects()->delete();
                    foreach($reqProjects as $project){
                        $pro=new EducationProject();
                        $pro->title=$project['title'];
                        $pro->description=$project['description'];
                        $pro->education_id=$education->id;
                        $pro->save();
                    }
                }else {$education->projects()->delete();}


            }


            $fromMonth = $reqFrom['month'];
            $fromYear = $reqFrom['year'];
            $fromDay = 1;
            $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
            $from_date_time = new \DateTime();
            $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
            $education->from = $from;

            if ($request['isPresent'] == false && $request['to'] != null) {
                $toMonth = $reqTo['month'];
                $toYear = $reqTo['year'];
                $toDay = 1;
                $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
                $to_date_time = new \DateTime();
                $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
                $education->to = $to;
            } else {
                $education->to = null;
            }


            if($request->has('grade')){
                $education->grade=$request->grade;
            } else $education->grade=null;

            if($request->has('full_grade')){
                $education->full_grade=$request->full_grade;

            }else $education->full_grade=null;

            $education->save();
            $education->university;
            $education->major;
            $education->minor;
            $education->projects;
            return $this->showOne($education);

        });


    }


    public function destroy(Education $education)
    {
        $user = auth()->user();
        if ($user->id != $education->resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        $ed = clone $education;
        $education->delete();
        return $this->showOne($ed);
    }

}
