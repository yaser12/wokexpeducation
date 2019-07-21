<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\ApiController;
use App\Models\Company\CompanyLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyLocationController extends ApiController
{
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
        $location_Rules = [
            'company_id'=>'required|integer',
            'country' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'street_address' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required'];
        $this->validate($request, $location_Rules);
        $companyLocation                =new CompanyLocation();
        $companyLocation->company_id    =$request->company_id;
        $companyLocation->name          =$request->name;
        $companyLocation->is_main_office=$request->is_main_office;
        $companyLocation->country       =$request->country;
        $companyLocation->city          =$request->city;
        $companyLocation->postal_code   =$request->postal_code;
        $companyLocation->street_address=$request->street_address;
        $companyLocation->latitude      =$request->latitude;
        $companyLocation->longitude     =$request->longitude;
        $companyLocation->save();
        $companyLocation  =  CompanyLocation:: where('id', $companyLocation->id)
             //-> with(array('companyType' ))
            ->first()  ;
        return $companyLocation;
    }
public function  set_comapnylocatin_as_main(Request $request)
{
    $set_main_location_Rules = [
        'company_id'=>'required|integer',
        'company_location_id' => 'required|integer'];
    $this->validate($request, $set_main_location_Rules);
    $companyLocation= CompanyLocation
        ::where('is_main_office','=',1)
        ->where('company_id','=',$request->company_id)
        ->update(['is_main_office' => 0]);


    $companyLocation= CompanyLocation
        ::where('company_id','=',$request->company_id)
        ->where('id','=',$request->company_location_id)
        ->update(['is_main_office' => 1]);


    $companyLocation= CompanyLocation::where('company_id','=',$request->company_id)->get();;
    return $companyLocation;
}
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_company_locations($company_id)
    {
        $companyLocation= CompanyLocation::where('company_id','=',$company_id)->get();;
        return $companyLocation;
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
