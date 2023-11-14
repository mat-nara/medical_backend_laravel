<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use LogActivity;
use Auth;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedInUser = $request->user();
        
        if($loggedInUser->roles[0]->slug == 'super_admin'){
            return Service::with('head', 'hopital')->get();
        }

        return Service::with('head', 'hopital')
                        ->where('hopital_id', $loggedInUser->service->hopital->id)
                        ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $service = new Service();

        $service->hopital_id    = $request->hopital_id;
        $service->head_id       = $request->head_id;
        $service->name          = $request->name;              

        $service->save();

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Service data '.' with name: '. $service->name);
        return response(['error' => 0, 'message' => 'Service has been created successfully', 'data' => $service], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hopital  $hopital
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        $loggedInUser = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' viewed Service '.$service->name.'\'s information' );
        return $service;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hopital  $hopital
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $service->hopital_id    = $request->hopital_id  ??  $service->hopital_id;
        $service->head_id       = $request->head_id     ??  $service->head_id;
        $service->name          = $request->name        ??  $service->name;

        $service->update();

        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Service data '.' with name: '. $service->name);
        return response(['error' => 0, 'message' => 'Service updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hopital $hopital
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();

        $loggedInUser   = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' deleted the Service data '.' with name: '. $service->name);
        return response(['error' => 0, 'message' => 'Service deleted']);
    }
}
