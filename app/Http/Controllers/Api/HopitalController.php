<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hopital;
use LogActivity;
use Auth;

class HopitalController extends Controller
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
            return Hopital::all();
        }

        return Hopital::where('id', $loggedInUser->service->hopital->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $hopital = new Hopital();

        $hopital->nom      = $request->nom;              
        $hopital->adresse  = $request->adresse;   
        $hopital->ville    = $request->ville;  

        $hopital->save();

        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new Hopital data '.' with name: '. $hopital->nom);
        return response(['error' => 0, 'message' => 'Hopital has been created successfully', 'data' => $hopital], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hopital  $hopital
     * @return \Illuminate\Http\Response
     */
    public function show(Hopital $hopital)
    {
        $loggedInUser = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' viewed Hopitals '.$hopital->nom.'\'s information' );
        return $hopital;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hopital  $hopital
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hopital $hopital)
    {
        $hopital->nom     = $request->nom      ?? $hopital->nom;
        $hopital->adresse = $request->adresse  ?? $hopital->adresse;
        $hopital->ville   = $request->ville    ?? $hopital->ville;

        $hopital->update();

        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the Hopital data '.' with name: '. $hopital->nom);
        return response(['error' => 0, 'message' => 'Hopital updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hopital $hopital
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hopital $hopital)
    {
        $hopital->delete();

        $loggedInUser   = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' deleted the Hopital data '.' with name: '. $hopital->nom);
        return response(['error' => 0, 'message' => 'Hopital deleted']);
    }
}
