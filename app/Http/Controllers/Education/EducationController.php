<?php

namespace App\Http\Controllers\Education;

use App\Models\Education\Education;
use App\Models\Education\EducationProject;
use App\Models\Education\Major;
use App\Models\Education\Minor;
use App\Models\Education\University;
use App\Models\Resume;
use Carbon\Carbon;
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
//            'from' => 'required',
            'isPresent' => 'required',
            'isFromMonthPresent' =>'required',
            'isToMonthPresent' => 'required',
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
            $education->description = $request['description'];

            $university = University::where('name', $reqUniversity['name'])->first();
            if ($university) {
                $education->university_id = $university->id;
            } else {
                $university = new University();
                $university->name = $reqUniversity['name'];
                $university->url = $reqUniversity['url'];


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
                $major->verified=false;
                $major->save();
                $education->major_id = $major->id;
            }

            if ( $reqFrom['year']!= null ){

                if($request['isFromMonthPresent'] == true){
                    $fromMonth = $reqFrom['month'];
                    $education->isFromMonthPresent  =true;

                }else{

                    $education->isFromMonthPresent = false;
                    $fromMonth = 1;

                }
                $fromYear = $reqFrom['year'];
                $fromDay = 1;
                $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
                $from_date_time = new \DateTime();
                $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
                $education->from = $from;
            }

            if ($request['isPresent'] == false && $reqTo['year'] != null) {

                if($request['isToMonthPresent'] == true) {
                    $toMonth = $reqTo['month'];
                    $education->isToMonthPresent = true;

                }
                else{
                    $toMonth = 1;
                    $education->isToMonthPresent = false;
                }

                $toYear = $reqTo['year'];
                $toDay = 1;
                $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
                $to_date_time = new \DateTime();
                $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
                $education->to = $to;
                $education->isPresent=false;
            } else {
                $education->to = null;
                $education->isPresent=true;

            }


            $education->grade=null;
            $education->full_grade=null;

            $educations=Education::where('resume_id',$request['resume_id'])->get();
            foreach($educations as $ed){
                $ed->order=$ed->order+1;
                $ed->save();
            }
            $education->order=1;
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
            ->orderBy('order')
            ->with(['university', 'major', 'minor', 'projects'])
            ->get();
        $majors=Major::where('verified',true)->get();
        $minors=Minor::where('verified',true)->get();
        return response()->json(['educations'=>$education,'majors'=>$majors,'minors'=>$minors],200);
//        return $this->showAll($education);
    }
    public function  getSingleEducation($resumeId,$educationId)
    {
        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        $education = Education::where('id', $educationId)
            ->with(['university', 'major', 'minor', 'projects'])
            ->first();
        $majors=Major::where('verified',true)->get();
        $minors=Minor::where('verified',true)->get();
        return response()->json(['education'=>$education,'majors'=>$majors,'minors'=>$minors],200);



    }

    public function update(Request $request, Education $education)
    {
        $this->validate($request, [
            'degree_level' => 'required',
            'university' => 'required',
            'major' => 'required',
//            'from' => 'required',
            'isPresent' => 'required',
            'isFromMonthPresent' =>'required',
            'isToMonthPresent' => 'required',
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
            $education->description = $request['description'];

            $university = University::where('name', $reqUniversity['name'])->first();
            if ($university) {
                $education->university_id = $university->id;
            } else {
                $university = new University();
                $university->name = $reqUniversity['name'];
                $university->url = $reqUniversity['url'];

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
                $major->verified=false;
                $major->save();
                $education->major_id = $major->id;
            }


            if( $request['minor']['name'] != null){
                $reqMinor = $request['minor'];
                $minor = Minor::where('name', $reqMinor['name'])->first();
                if ($minor) {
                    $education->minor_id = $minor->id;
                } else {
                    $minor = new Minor();
                    $minor->name = $reqMinor['name'];
                    $minor->major_id=$major->id;
                    $minor->verified=false;
                    $minor->save();
                    $education->minor_id = $minor->id;
                }

            }else {$education->minor_id=null;}

            if($request->has('projects')){
                $reqProjects = $request['projects'];
                $education->projects()->delete();
                if($reqProjects != null){
                    foreach($reqProjects as $project){
                        $pro=new EducationProject();
                        $pro->title=$project['title'];
                        $pro->description=$project['description'];
                        $pro->education_id=$education->id;
                        $pro->save();
                    }
                }else {$education->projects()->delete();}


            }

            if ( $reqFrom['year']!= null ) {
                if($request['isFromMonthPresent'] == true){
                    $fromMonth = $reqFrom['month'];
                    $education->isFromMonthPresent = true;
                }
                else{
                    $fromMonth = 1 ;
                    $education->isFromMonthPresent = false;
                }
                $fromYear = $reqFrom['year'];
                $fromDay = 1;
                $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
                $from_date_time = new \DateTime();
                $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
                $education->from = $from;
            }

            if ($request['isPresent'] == false && $reqTo['year'] != null) {
                if ($request['isToMonthPresent'] == true){
                    $toMonth = $reqTo['month'];
                    $education->isToMonthPresent = true;
                }
                else{
                    $toMonth = 1;
                    $education->isToMonthPresent = false;
                }
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

    public function orderData(Request $request,$resumeId){

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach($request['orderData'] as $ed){
            $education=Education::findOrFail($ed['educationId']);
            $education->order=$ed['orderId'];
            $education->save();
        }
        return response()->json(['success'=>'true']);
    }


    public function destroy(Education $education)
    {
        $user = auth()->user();
        $oldEducation = clone $education;
        if ($user->id != $education->resume->user_id) return $this->errorResponse('you are not authorized to do this operation', 401);
        return DB::transaction(function () use ($oldEducation,$education) {
            $education->delete();
            $educations = Education::where([['resume_id', $education->resume_id],['order','>',$education->order]])->get();
            foreach ($educations as $ed) {
                $ed->order = $ed->order-1;
                $ed->save();
            }

            return $this->showOne($oldEducation);
        });
    }



}
