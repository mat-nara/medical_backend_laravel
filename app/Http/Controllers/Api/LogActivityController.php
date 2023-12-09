<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LogActivity as LogActivityModel;
use LogActivity;
use Auth;



class LogActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $range = $request->validate([
            'start' => 'required',
            'end'   => 'required',
        ]);

        $start  = new Carbon($range['start']); 
        $end    = new Carbon($range['end']);

        $start  = $start->addDays(1);
        $end    = $end->addDays(1);
        
        $start  = $start->format('Y-m-d')." 00:00:00";
        $end    = $end->format('Y-m-d')." 23:59:59";

        $loggedInUser = Auth::user();

        //Grant access to superadmin user
        if($loggedInUser->roles[0]->slug == 'super_admin'){
           return LogActivityModel::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->get();
        }

        //For other users
        $user_id    = $loggedInUser->id;
        $logs       = LogActivityModel::whereIn('user_id', 
                                            function($query) use ($user_id){ 
                                                $query->select('id')->from('users')
                                                                    ->where('parent_id', $user_id)
                                                                    ->orWhere('id', $user_id);
                                            }
                                        )
                                        ->whereBetween('created_at', [$start, $end])
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        
        return $logs;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToLog()
    {
        LogActivity::addToLog('My Testing Add To Log.');
        dd('log insert successfully.');
    }
}
