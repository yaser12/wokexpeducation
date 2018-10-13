<?php

namespace App\Http\Controllers\DrivingLicense;


use App\Models\DrivingCategory\DrivingCategory;

use App\Http\Controllers\ApiController;
use App\Models\DrivingLicense\Driving;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\DB;

class DrivingLicenseController extends ApiController
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
        $this->validate($request,  [
            'resume_id' => 'required',
            'license_type' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        return DB::transaction(function () use ($request) {
            $driving = new Driving();
            $driving->resume_id = $request['resume_id'];
            $driving->license_type = $request['license_type'];
            $driving->country = $request['country'];
            $driving->international = $request['international'];
            $driving->save();
            $categories=$request['category'];
            foreach ($categories as $cat){
                $c=new DrivingCategory();
                $c->driving_id=$driving->id;
                $c->name=$cat['name'];
                $c->save();
            }
            $driving->categories = $categories;
            return $this->showOne($driving);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($resumeId)
    {

        $resume = Resume::findOrFail($resumeId);

        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $driving = Driving::where('resume_id', $resumeId)
            ->with(['categories'])
            ->get();
        if( $driving ==null){
            return response()->json(['data' => 404]);
        }
        return $this->showAll($driving);
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
    public function update(Request $request, Driving $driving)
    {
        $this->validate($request,  [
            'resume_id' => 'required',
            'license_type' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        return DB::transaction(function () use ($request , $driving) {

            $driving->resume_id = $request['resume_id'];
            $driving->license_type = $request['license_type'];
            $driving->country = $request['country'];
            $driving->international = $request['international'];
            $driving->save();
            if ($request->has('category')) {

                $categories = $request['category'];
                if ($categories != null) {
                    $driving->categories()->delete();
                }
                foreach ($categories as $cat) {
                    $category = new DrivingCategory();
                    $category->driving_id = $driving->id;
                    $category->name = $cat['name'];
                    $category->save();
                }
                $driving->categories = $categories;
            }
            return $this->showOne($driving);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driving $driving)
    {
        $user = auth()->user();
        if ($user->id != $driving->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);
        $driving->categories()->delete();
        $driving->delete();

        return $this->showOne($driving);
    }
}
