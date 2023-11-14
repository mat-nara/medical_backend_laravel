<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LogActivity;
use App\Models\Patient;
use App\Models\Access;
use App\Models\User;


class AccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient)
    {
        $user = Auth::user();
        if( ! ($user->roles[0]->slug == 'super_admin')){
            $authorized = count(Patient::find($patient)->owners->where('id', $user->id)) > 0 ? true : false;
            if(!$authorized){
                return response(['error' => 1, 'message' => 'Patient doesn\'t exist or you are not authorized'], 403); 
            }
        }

        $accesses = Patient::with('owners', 'owners.roles')->where('id', $patient)->first();
        LogActivity::addToLog($user->name . ' viewed the access list for the patient '. $accesses->nom . '  ' . $accesses->prenoms);
        return $accesses;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $patient)
    {
        $user       = Auth::user();
        $patient    = Patient::find($patient);
        $owners     = $patient->owners->where('id', $user->id);
        $authorized = count($owners) > 0 ? true : false;

        if(!$authorized){
            return response(['error' => 1, 'message' => 'Patient doesn\'t exist or you are not authorized'], 403); 
        }

        $acces_user = User::where('email', $request->email)->first();
        if(!$acces_user){
            return response(['error' => 1, 'message' => 'User with this email is not found'], 400); 
        }

        $access = new Access();

        $access->patient_id     = $request->patient_id;              
        $access->user_id        = $acces_user->id;             
        $access->permission     = $request->permission;                    
        
        $access->save();


        LogActivity::addToLog($user->name . ' shared access to patient '. $patient->nom . '  ' . $patient->prenoms .' with ' . $acces_user->name);
        return response(['error' => 0, 'message' => 'Access has been shared successfully'], 201);

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
    public function destroy($patient, $access)
    {
        $access = Access::find($access);
        $access->delete();

        $user           = Auth::user();
        $patient        = Patient::find($access->patient_id);   
        $user_access    = User::find($access->user_id);   
        LogActivity::addToLog($user->name . ' removed access to patient '. $patient->nom .' '. $patient->prenoms .' for ' . $user_access->name);
        return response(['error' => 0, 'message' => 'Access deleted']);
    }
}
