<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\ApiController;
use App\Models\Company\Company;
use App\Models\Company\CompanyProfile;
use App\Models\Company\CompanyIndustriesForCompany;
use App\Models\Company\CompanySpecialtiesForCompany;
use App\Models\Company\CompanySocialMedia;
use App\Models\WorkExperience\CompanyIndustry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends ApiController
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
            'company_websit' => 'string'
            , 'company_size_id' => 'integer'
            , 'company_type_id' => 'integer'
            , 'founded_year' => 'string'
            , 'founded_month' => 'string'
            ,'company_profile'=> 'required'
            , 'path_company_imagelogo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
           ,  "company_industries"    => "required|array|min:1"
        ];

          $company_profile_Rules = [
            'name' => 'required|string',
            'translated_languages_id' => 'required|integer',
            'company_description' => 'string',

        ];
        $company_industries_for_company_rule=[
            'company_industry_id' => 'required|integer'
        ];
        $Date_Of_Founded_Rules = [
            'year' => 'required|string',
            'month' => 'required|string'
        ];
        $company_social_media_Rules = [
            'company_social_media_info' => 'required|string',
            'social_media_id' => 'required|integer'
        ];
        $company_specialties_for_company_rule=[
            'specialty_id' => 'required|integer'

        ];

        $this->validate($request, $rules);

        $company_specialties_for_companyRules= [
            'year' => 'required|string',
            'month' => 'required|string'
        ];

        $this->validate($request,$rules);

        $company_profile_Request = new Request($request->company_profile);
        $this->validate($company_profile_Request,$company_profile_Rules);
        $company_industries_request=new Request($request->company_industries);



            foreach ($request->company_industries as $company_industry_request)
            {


                $company_industryRequest = new Request($company_industry_request);

                $this->validate($company_industryRequest,$company_industries_for_company_rule );
            }

     /*   if ($request->hasFile('path_company_imagelogo')){
            $imagelogo = $request->file('path_company_imagelogo');
            $extension = $imagelogo->getClientOriginalExtension(); // you can also use file name
            $imagelogoName = time().'.'.$extension;
            $path = public_path().'/img';
            $uplaodimagelogoName = $imagelogo->move($path,$imagelogoName);

        }*/

        $company=new Company();
        $company->company_websit=$request['company_websit'];
        $company->company_size_id=$request['company_size_id'];
        $company->company_type_id=$request['company_type_id'];

        if ( $request['founded_year'] != null  &&  $request['founded_month'] == null )
        {

            $year = $request->founded_year;
            $month =1;
            $day = 1;
            $date_string = $year . "-" . $month . "-" . $day;
            $date_time = new \DateTime();
            $founded = $date_time->createFromFormat('Y-m-d', $date_string);
            $company->founded=$founded;
        }
        else if( $request['founded_year'] != null  &&  $request['founded_month'] != null )
        {

            $year = $request->founded_year;
            $month = $request->founded_month;
            $day = 1;
            $date_string = $year . "-" . $month . "-" . $day;
            $date_time = new \DateTime();
            $founded = $date_time->createFromFormat('Y-m-d', $date_string);

            $company->founded=$founded;

        }

        $company->save();

      //  $company  = Company::findOrFail($company->id);
        //   return $this->showOne($company);

        $company_profile=new CompanyProfile();
        $company_profile->company_id=$company->id;
        $company_profile->company_description=$company_profile_Request['company_description'];
        $company_profile->name=$company_profile_Request['name'];
        $company_profile->translated_languages_id=$company_profile_Request['translated_languages_id'];
        $company_profile->save();
        foreach ($request->company_industries as $company_industry_request)
        {
            $company_industryRequest = new Request($company_industry_request);
            $companyIndustriesForCompany=new CompanyIndustriesForCompany();
            $companyIndustriesForCompany->company_id=$company->id;
            $companyIndustriesForCompany->company_industry_id =$company_industryRequest->company_industry_id;
            $companyIndustriesForCompany->save();
        }

        if ($request->has('company_specialties')) {
            foreach ($request->company_specialties as $company_specialties_request)
            {
                $company_specialtiesRequest = new Request($company_specialties_request);
                $companySpecialtiesForCompany=new CompanySpecialtiesForCompany();
                $companySpecialtiesForCompany->company_id=$company->id;
                $companySpecialtiesForCompany->specialty_id =$company_specialtiesRequest->specialty_id;
                $companySpecialtiesForCompany->save();
            }
        }
        if ($request->has('company_social_media'))
        {


            $company_social_mediaRequest = new Request($request->company_social_media);
            $this->validate($company_social_mediaRequest,$company_social_media_Rules );
            $companySocialMedia =new CompanySocialMedia();
            $companySocialMedia->company_social_media_info=$company_social_mediaRequest->company_social_media_info;
            $companySocialMedia->social_media_id=$company_social_mediaRequest->social_media_id;
            $companySocialMedia->company_id=$company->id;
            $companySocialMedia->save();
        }
         $company  =  Company:: where('id', $company->id)->  with(array('companyProfile' ,'companyIndustriesForCompany','CompanySpecialtiesForCompany','companySocialMedia'))->get()  ;
        return response()->json(['company' => $company], 200);


    }
    public function add_new_profile(Request $request)
    {

    }
    public function upload_logo(Request $request,$id)
    {
        $rules = [
              'path_company_imagelogo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ];
        $this->validate($request,$rules);
        if ($request->hasFile('path_company_imagelogo')){
            $file = $request->file('path_company_imagelogo');
            $extension = $file->getClientOriginalExtension(); // you can also use file name
            $fileName = time().'.'.$extension;
            $path = public_path().'/img';
            $uplaod = $file->move($path,$fileName);
            return $fileName;
        }

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
        $company  =  Company:: where('id', $id)->  with(array('companyProfile' ,'companyIndustriesForCompany','CompanySpecialtiesForCompany','companySocialMedia'))->get()  ;
        return response()->json(['company' => $company], 200);
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
