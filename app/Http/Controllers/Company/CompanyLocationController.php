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
        //
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
        return $this->showOne($companyLocation);
    }
public function  set_comapnylocatin_as_main(Request $request,$id)
{

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
