<?php

namespace App\Http\Controllers\Portfolio;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\Portfolio\Portfolio;
use Auth;

class PortfolioController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the portfolio.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Resume $resume)
    {
        //Authorization
        $user = Auth::user();
        if ($user->id != $resume->user_id) {
            return $this->errorResponse('you are not authorized to do this operation', 401);

        }
        $portfolio = $resume->Portfolio()
            ->orderBy('order')
            ->get();

        //Return the success response data
        return $this->showAll($portfolio);
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
            'resume_id' => 'required',
            'title' => 'required',
            'link' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $Portfolio = new Portfolio();

        //associate with resume
        $Portfolio->resume_id = $request['resume_id'];

        //store title
        $Portfolio->title = $request['title'];

        //store link
        $Portfolio->link = $request['link'];

        $Portfolios = Portfolio::where('resume_id', $request['resume_id'])->get();
        foreach ($Portfolios as $port) {
            $port->order = $port->order + 1;
            $port->save();

        }
        $Portfolio->order = 1;
        $Portfolio->save();
        //fetch the newly created Portfolio from the database
        $newPortfolio = Portfolio::where('id', $Portfolio->id)->first();
        return $this->showOne($newPortfolio);

    }

    /**
     * Display the specified portfolio.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {

        $resume = $portfolio->resume;

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        //return single portfolio
        return $this->showOne($portfolio);
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
     * Update the specified portfolio in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'resume_id' => 'required',
            'title' => 'required',
            'link' => 'required',
        ]);
        $resume = Resume::findOrFail($request['resume_id']);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $Portfolio = Portfolio::findOrFail($id);

        //associate with resume
        $Portfolio->resume_id = $request['resume_id'];

        //update title
        $Portfolio->title = $request['title'];

        //update link
        $Portfolio->link = $request['link'];

        $Portfolio->save();

        //fetch the newly created Portfolio from the database
        $newPortfolio = Portfolio::where('id', $Portfolio->id)->first();
        return $this->showOne($newPortfolio);

    }

    /**
     * Remove the specified portfolio from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        $user = auth()->user();
        if ($user->id != $portfolio->resume->user_id)
            return $this->errorResponse('you are not authorized to do this operation', 401);

        $portfolio->delete();
        $portfolios = Portfolio::where([['resume_id', $portfolio->resume_id], ['order', '>', $portfolio->order]])->get();
        foreach ($portfolios as $port) {
            $port->order = $port->order - 1;
            $port->save();
        }
        return $this->showOne($portfolio);
    }

    public function orderData(Request $request, $resumeId)
    {

        $resume = Resume::findOrFail($resumeId);

        //Authorization
        $user = auth()->user();
        if ($user->id != $resume->user->id) return $this->errorResponse('you are not authorized to do this operation', 401);


        foreach ($request['orderData'] as $port) {

            $portfolio = Portfolio::findOrFail($port['PortfolioId']);
            $portfolio->order = $port['orderId'];
            $portfolio->save();
        }
        return response()->json(['success' => 'true']);
    }
}