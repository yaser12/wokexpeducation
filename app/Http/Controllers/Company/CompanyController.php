<?php

namespace App\Http\Controllers\Company;

use App\Models\Company\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {

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
            'company_websit' => 'string|unique'
            , 'company_size_id' => 'integer'
            , 'company_type_id' => 'integer'
            , 'is_month' => 'integer'
            , 'path_company_imagelogo' => 'image'
            ,  "company_industries_for_company"    => "required|array|min:1",
        ];

        $Date_Of_Founded_Rules = [
            'year' => 'required|string',
            'month' => 'required|string'
        ];
        $company_profiles_Rules = [
            'name' => 'required|string',
            'translated_languages_id' => 'required|integer',
            'company_description' => 'string',
            'company_description' => 'string',
        ];
        $company_social_media_Rules = [
            'company_social_media_info' => 'required|string',
            'social_media_id' => 'required|integer'
        ];
        $company_specialties_for_company_rule=[
            'specialty_id' => 'required|integer'

        ];
        $company_industries_for_company_rule=[
            'company_industry_id ' => 'required|integer'
        ];
        $this->validate($request, $rules);

        $company_specialties_for_companyRules= [
            'year' => 'required|string',
            'month' => 'required|string'
        ];
        $company_industries_for_company= [
            'year' => 'required|string',
            'month' => 'required|string'
        ];
        $this->validate($request,$rules);
        $company=new Company();
        $company->company_websit=$request['company_websit'];
        $company->company_size_id=$request['company_size_id'];
        $company->company_type_id=$request['company_type_id'];

        $date_of_birth = "";
        $Date_Of_Founded_Request = new Request($request->founded);
        if ($request->has('founded')) {
            $this->validate($Date_Of_Founded_Request, $Date_Of_Founded_Rules);
            $year = $Date_Of_Founded_Request->year;
            $month = $Date_Of_Founded_Request->month;
            $day = 1;

            $date_string = $year . "-" . $month . "-" . $day;
            $date_time = new \DateTime();
            $founded = $date_time->createFromFormat('Y-m-d', $date_string);
        }
    }
    public function add_new_profile(Request $request)
    {

    }
    public function upload_logo(Request $request)
    {

    }
    public function  add_new_company_location(Request $request,$id)
    {
        $location_Rules = [
            'country' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'street_address' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required'];
        $this->validate($request, $location_Rules);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
