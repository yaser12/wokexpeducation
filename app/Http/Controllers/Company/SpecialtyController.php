<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecialtyController extends Controller
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
        $this->validate($request, [
              'specialties_translation_name' => 'required|string'
            , 'is_verfied' => 'required|integer'
            , 'company_industry_id' => 'required|integer'
            , 'translated_languages_id' => 'required|integer'
        ]);
        $specialtiesTranslation = SpecialtiesTranslation::where('specialties_translation_name',$request['specialties_translation_name'])->where('translated_languages_id',$request['translated_languages_id'])->first();
        if (! is_null($specialtiesTranslation) )
        {
            return  $this->errorResponse("  '".$request['specialties_translation_name']."' already inserted ",404);
        }
        //  return  $this->showOne($specialtiesTranslation);
        $specialty=new Specialty();
        $specialty->save();
        $specialtiesTranslation=new SpecialtiesTranslation();
        $specialtiesTranslation->company_type_id=$specialty->id;
        $specialtiesTranslation->is_verfied=$request['is_verfied'];
        $specialtiesTranslation->specialties_translation_name=$request['specialties_translation_name'];
        $specialtiesTranslation->translated_languages_id=$request['translated_languages_id'];
        $specialtiesTranslation->save();
        $companyType1 = Specialty::where('id',$specialty->id) ->
        with(array('SpecialtiesTranslation' => function ($query) use ($request) {
            $query->where('translated_languages_id', $request['translated_languages_id']);
        })) ->first();
        return $this->showOne($companyType1);
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
