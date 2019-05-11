<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\ApiController;
use App\Models\Training\Training;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class TrainingController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the Trainings associated with single resume.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($resume_id)
    {
        $resume = Resume::findOrFail($resume_id);
        //Authorization
        $user = Auth::user();
        if ($user->id != $resume->user_id) {
            return $this->errorResponse('you are not authorized to do this operation', 401);
        }

        $trainings = $resume->trainings()
            ->orderBy('order')
            ->get();

        //Return the success response data
        return $this->showAll($trainings);
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
     * Store a newly created Trainings in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation
        $this->validate($request, ['resume_id' => 'required',
            'name' => 'required',
//            'to' => 'required',
            'isFromMonthPresent' => 'required',
            'isToMonthPresent' => 'required',
            'isPresent' => 'required',]);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);

        $reqFrom = $request['from'];
        $reqTo = $request['to'];

        $training = new Training();
        //associate with resume
        $training->resume_id = $request['resume_id'];
        $training->name = $request['name'];
        $training->organization = $request['organization'];
        $training->total_hours = $request['total_hours'];

        if ($reqFrom['year'] != null) {

            if ($request['isFromMonthPresent'] == true) {
                $fromMonth = $reqFrom['month'];
                $training->isFromMonthPresent = true;

            } else {
                $training->isFromMonthPresent = false;
                $fromMonth = 1;
            }
            $fromYear = $reqFrom['year'];
            $fromDay = 1;
            $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
            $from_date_time = new \DateTime();
            $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
            $training->from = $from;
        }

        if ($request['isPresent'] == false && $reqTo['year'] != null) {
            if ($request['isToMonthPresent'] == true) {
                $toMonth = $reqTo['month'];
                $training->isToMonthPresent = true;
            } else {
                $toMonth = 1;
                $training->isToMonthPresent = false;
            }

            $toYear = $reqTo['year'];
            $toDay = 1;
            $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
            $to_date_time = new \DateTime();
            $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
            $training->to = $to;
            $training->isPresent = false;
        } else {
            $training->to = null;
            $training->isPresent = true;
//            $training->isToMonthPresent = $request['isToMonthPresent'];
        }
        $training->description = null;
        $training->website = null;
        $training->country = null;
        $training->city = null;


        $trainings = Training::where('resume_id', $request['resume_id'])->get();
        foreach ($trainings as $tr) {
            $tr->order = $tr->order + 1;
            $tr->save();
        }
        $training->order = 1;
        $training->save();

        $newTraining = Training::where('id', $training->id)->first();
        return $this->showOne($newTraining);

    }

    /**
     * Display Trainings associated with single resume..
     *
     * @param  App\Models\Training\Training $training
     * @return \Illuminate\Http\Response
     */
    public function show(Training $training)
    {
        $resume = $training->resume;

        //Authorization

        $user = auth()->user();

        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //return single $training

        return $this->showOne($training);
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
        //validation
        $this->validate($request, ['resume_id' => 'required',
            'name' => 'required',
            'to' => 'required',
            'isPresent' => 'required',
            'isFromMonthPresent' => 'required',
            'isToMonthPresent' => 'required',]);

        $resume = Resume::findOrFail($request['resume_id']);
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        $reqFrom = $request['from'];
        $reqTo = $request['to'];

        $training = Training::findOrFail($id);
        //associate with resume
        $training->resume_id = $request['resume_id'];
        $training->name = $request['name'];
        $training->organization = $request['organization'];
        $training->total_hours = $request['total_hours'];

        if ($reqFrom['year'] != null) {

            if ($request['isFromMonthPresent'] == true) {
                $fromMonth = $reqFrom['month'];
                $training->isFromMonthPresent = true;

            } else {
                $training->isFromMonthPresent = false;
                $fromMonth = 1;
            }
            $fromYear = $reqFrom['year'];
            $fromDay = 1;
            $date_string = $fromYear . "-" . $fromMonth . "-" . $fromDay;
            $from_date_time = new \DateTime();
            $from = $from_date_time->createFromFormat('Y-m-d', $date_string);
            $training->from = $from;
        }

        if ($request['isPresent'] == false && $reqTo['year'] != null) {
            if ($request['isToMonthPresent'] == true) {
                $toMonth = $reqTo['month'];
                $training->isToMonthPresent = true;
            } else {
                $toMonth = 1;
                $training->isToMonthPresent = false;
            }

            $toYear = $reqTo['year'];
            $toDay = 1;
            $date_string = $toYear . "-" . $toMonth . "-" . $toDay;
            $to_date_time = new \DateTime();
            $to = $to_date_time->createFromFormat('Y-m-d', $date_string);
            $training->to = $to;
            $training->isPresent = false;
        } else {
            $training->to = null;
            $training->isPresent = true;
//            $training->isToMonthPresent = $request['isToMonthPresent'];
        }

        if ($request->has('description')) {
            $training->description = $request->description;
        } else $training->description = null;
        if ($request->has('website')) {
            $training->website = $request->website;
        } else $training->website = null;
        if ($request['city'] != null) {
            $training->city = $request['city'];
        } else {
            $training->city = null;
        }
        if ($request['country'] != null) {
            $training->country = $request['country'];
        } else {
            $training->country = null;
        }
        $training->save();

        $newTraining = Training::find($training->id);
        return $this->showOne($newTraining);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Training\Training $training
     * @return \Illuminate\Http\Response
     */
    public function destroy(Training $training)
    {
        $user = auth()->user();
        if ($user->id != $training->resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $training->delete();
        $trainings = Training::where([['resume_id', $training->resume_id], ['order', '>', $training->order]])->get();
        foreach ($trainings as $tr) {
            $tr->order = $tr->order - 1;
            $tr->save();
        }
        return $this->showOne($training);
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        foreach ($request['orderData'] as $tr) {
            $training = Training::findOrFail($tr['trainingId']);
            $training->order = $tr['orderId'];
            $training->save();
        }
        return response()->json(['success' => 'true']);
    }
}
