<?php

namespace App\Http\Controllers\WorkExperience;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Resume;
use App\Models\WorkExperience\Company;
use App\Models\WorkExperience\CompanyIndustry;
use App\Models\WorkExperience\EmploymentTypeParent;
use App\Models\WorkExperience\EmploymentType;
use App\Models\WorkExperience\WorkExperience;

class WorkExperienceController extends ApiController
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

        $work_experiences = WorkExperience::where('resume_id', $resumeId)
            ->orderBy('order')
            ->with(['company', 'company_industry', 'employment_types',
                'employment_types.employment_type_parent', 'employment_types.employment_type_parent.parent_category'])
            ->get();

        $companies = Company::all();
        $company_industries = CompanyIndustry::where('verified', true)->get();
        $employment_types = EmploymentType::all();
        $employment_type_parents = EmploymentTypeParent::whereNull('parent_id')->with(['child_types'])->get();

        return response()->json(['work_experiences' => $work_experiences,
            'companies' => $companies,
            'company_industries' => $company_industries,
            'employment_types' => $employment_types,
            'employment_type_parents' => $employment_type_parents,

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
        $this->validate($request, [
            'company' => 'required',
            'job_title' => 'required',
            'from' => 'required',
            'isPresent' => 'required',
            'isFromMonthPresent' => 'required',
            'isToMonthPresent' => 'required',
            'resume_id' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request) {

            $reqCompany = $request['company'];
            $reqFrom = $request['from'];
            $reqTo = $request['to'];

            $work_exp = new WorkExperience();
            $work_exp->job_title = $request['job_title'];
            $work_exp->resume_id = $request['resume_id'];
            $work_exp->description = $request['description'];

            //store company
            $comapny = Company::where('name', $reqCompany['name'])
                ->where('city', $reqCompany['city'])
                ->where('country', $reqCompany['country'])->first();
            if ($comapny != null) {
                $work_exp->company_id = $comapny->id;
            } else {
                $new_comapny = new Company();
                $new_comapny->name = $reqCompany['name'];
                $new_comapny->city = $reqCompany['city'];
                $new_comapny->country = $reqCompany['country'];
                $new_comapny->verified = false;

                $new_comapny->save();
                $work_exp->company_id = $new_comapny->id;
            }
            //store date
            if ($reqFrom['year'] != null) {
                if ($request['isFromMonthPresent'] == true) {
                    $fromMonth = $reqFrom['month'];
                    $work_exp->isFromMonthPresent = true;

                } else {
                    $work_exp->isFromMonthPresent = false;
                    $fromMonth = 1;
                }
                $fromYear = $reqFrom['year'];
                $fromDay = 1;
                $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
                $from_date_time = new \DateTime();
                $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
                $work_exp->from = $from;
            }
            if ($request['isPresent'] == false && $reqTo['year'] != null) {
                if ($request['isToMonthPresent'] == true) {
                    $toMonth = $reqTo['month'];
                    $work_exp->isToMonthPresent = true;
                } else {
                    $toMonth = 1;
                    $work_exp->isToMonthPresent = false;
                }

                $toYear = $reqTo['year'];
                $toDay = 1;
                $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
                $to_date_time = new \DateTime();
                $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
                $work_exp->to = $to;
                $work_exp->isPresent = false;
            } else {
                $work_exp->to = null;
                $work_exp->isPresent = true;
            }

            $work_exps = WorkExperience::where('resume_id', $request['resume_id'])->get();
            foreach ($work_exps as $wo) {
                $wo->order = $wo->order + 1;
                $wo->save();
            }
            $work_exp->order = 1;
            $work_exp->save();

            $newWorkExp = WorkExperience::where('id', $work_exp->id)->first();
            $newWorkExp->company;
            $newWorkExp->company_industry;
            $newWorkExp->employment_types;
            return $this->showOne($newWorkExp);

        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $work_exp = WorkExperience::where('id', $id)
            ->orderBy('order')
            ->with(['company', 'company_industry', 'employment_types',
                'employment_types.employment_type_parent'
                , 'employment_types.employment_type_parent.parent_category'])
            ->get();
        return $this->showAll($work_exp);
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

        $this->validate($request, [
            'company' => 'required',
            'job_title' => 'required',
            'from' => 'required',
            'isPresent' => 'required',
            'isFromMonthPresent' => 'required',
            'isToMonthPresent' => 'required',
            'resume_id' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $work_exp = WorkExperience::findOrFail($id);
        return DB::transaction(function () use ($request, $work_exp, $id) {

            $reqCompany = $request['company'];
//            $reqEmploymentType = $request['employment_types'];
            $reqFrom = $request['from'];
            $reqTo = $request['to'];


            $work_exp->job_title = $request['job_title'];
            $work_exp->resume_id = $request['resume_id'];
            $work_exp->description = $request['description'];

            // company
            $comapny = Company::where('name', $reqCompany['name'])
                ->where('city', $reqCompany['city'])
                ->where('country', $reqCompany['country'])->first();

            if ($comapny != null) {
                $work_exp->company_id = $comapny->id;
                $comapny->company_size = $reqCompany['company_size'];
                $comapny->company_website = $reqCompany['company_website'];
                $comapny->company_description = $reqCompany['company_description'];
                $comapny->save();
            } else {
                $new_comapny = new Company();
                $new_comapny->name = $reqCompany['name'];
                $new_comapny->city = $reqCompany['city'];
                $new_comapny->country = $reqCompany['country'];
                $new_comapny->company_size = $reqCompany['company_size'];
                $new_comapny->company_website = $reqCompany['company_website'];
                $new_comapny->company_description = $reqCompany['company_description'];
                $new_comapny->verified = false;

                $new_comapny->save();
                $work_exp->company_id = $new_comapny->id;
            }
            //company industry
            if ($request->has('company_industry')) {
                $req_company_industry = $request['company_industry'];
                if ($req_company_industry['id'] > 0) {
                    $company_industry = CompanyIndustry::where('name', $req_company_industry['name'])->first();
                    $work_exp->company_industry_id = $company_industry->id;
                } else {
                    $company_industry = new CompanyIndustry();
                    $company_industry->name = $req_company_industry['name'];
                    $company_industry->verified = false;
                    $company_industry->save();
                    $work_exp->company_industry_id = $company_industry->id;
                }
            }

            //employment type
            if ($request->has('employment_types')) {

                $work_exp->employment_types()->delete();
                foreach ($request['employment_types'] as $value) {
                    $employment_typeRequest = new Request($value);
//
                    $employment_type = new EmploymentType();
//
                    $employment_type->work_experience_id = $work_exp->id;

                    $employment_type->employment_type_parent_id = $employment_typeRequest['employment_type_parent_id'];
                    $employment_type->save();
                }
            }
            //date
            if ($reqFrom['year'] != null) {

                if ($request['isFromMonthPresent'] == true) {
                    $fromMonth = $reqFrom['month'];
                    $work_exp->isFromMonthPresent = true;

                } else {
                    $work_exp->isFromMonthPresent = false;
                    $fromMonth = 1;
                }
                $fromYear = $reqFrom['year'];
                $fromDay = 1;
                $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
                $from_date_time = new \DateTime();
                $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
                $work_exp->from = $from;
            }
            if ($request['isPresent'] == false && $reqTo['year'] != null) {
                if ($request['isToMonthPresent'] == true) {
                    $toMonth = $reqTo['month'];
                    $work_exp->isToMonthPresent = true;
                } else {
                    $toMonth = 1;
                    $work_exp->isToMonthPresent = false;
                }

                $toYear = $reqTo['year'];
                $toDay = 1;
                $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
                $to_date_time = new \DateTime();
                $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
                $work_exp->to = $to;
                $work_exp->isPresent = false;
            } else {
                $work_exp->to = null;
                $work_exp->isPresent = true;
            }

            $work_exp->save();

            $New_work_exp = WorkExperience::where('id', $id)
                ->with(['company', 'company_industry', 'employment_types',
                    'employment_types.employment_type_parent',
                    'employment_types.employment_type_parent.parent_category'])
                ->get();

            return $this->showAll($New_work_exp);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $work_exp = WorkExperience::findOrFail($id);
        $user = auth()->user();
        $oldWorkExp = clone $work_exp;
        if ($user->id != $work_exp->resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        return DB::transaction(function () use ($oldWorkExp, $work_exp) {
            $work_exp->employment_types()->delete();

            $work_exp->delete();
            $WorkExps = WorkExperience::where([['resume_id', $work_exp->resume_id], ['order', '>', $work_exp->order]])->get();
            foreach ($WorkExps as $wo) {
                $wo->order = $wo->order - 1;
                $wo->save();
            }
            return $this->showOne($oldWorkExp);
        });
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);
        foreach ($request['orderData'] as $wo) {
            $work_exp = WorkExperience::findOrFail($wo['workExperienceId']);
            $work_exp->order = $wo['orderId'];
            $work_exp->save();
        }
        return response()->json(['success' => 'true']);
    }
}