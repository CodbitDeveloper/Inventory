<?php

namespace App\Http\Controllers;

use App\PreventiveMaintenance;
use App\PmSchedule;

use Auth;
use Carbon;

use Illuminate\Http\Request;

class PreventiveMaintenanceController extends Controller
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
     * Show the form for listing resource types.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $pmSchedules = PmSchedule::where("hospital_id", $user->hospital_id)->with("priority")->get();
            return view("pm-add-step1", \compact("pmSchedules", "user"));
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource
     * 
     * @return \Illuminate\Http\Response
     */
    public function make(PmSchedule $pmSchedule)
    {
        $user = Auth::user();

        if($pmSchedule == null){
            return abort(403);
        }
        if($user->role == 'Admin' || $user->role == 'Regular Technician'){
            return view("pm-add", compact("pmSchedule", "user"));
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'pm_schedule_id' => 'required'
        ]);

        $preventiveMaintenance = new PreventiveMaintenance();

        $preventiveMaintenance->pm_schedule_id = $request->pm_schedule_id;
        $preventiveMaintenance->observation    = $request->observation;
        $preventiveMaintenance->recommendation = $request->recommendation;
        $preventiveMaintenance->action_taken   = $request->action_taken;
        $preventiveMaintenance->date_completed = date('Y-m-d', strtotime($request->date_completed));
        $preventiveMaintenance->is_completed = 2;
        $preventiveMaintenance->reported_by = $request->reported_by;

        if($preventiveMaintenance->save()) {
            return response()->json([
                'error'   => false,
                'data'    => $preventiveMaintenance,
                'message' => 'Preventive Maintenance created successfully.'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not create preventive maintenance. Try Again!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PreventiveMaintenance  $preventiveMaintenance
     * @return \Illuminate\Http\Response
     */
    public function show(PreventiveMaintenance $preventiveMaintenance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PreventiveMaintenance  $preventiveMaintenance
     * @return \Illuminate\Http\Response
     */
    public function edit(PreventiveMaintenance $preventiveMaintenance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PreventiveMaintenance  $preventiveMaintenance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PreventiveMaintenance $preventiveMaintenance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PreventiveMaintenance  $preventiveMaintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreventiveMaintenance $preventiveMaintenance)
    {
        //
    }

    public function approve(PreventiveMaintenance $preventiveMaintenance, Request $request){
        $request->validate([
            'marked_by' => 'required'
        ]);

        $preventiveMaintenance->is_completed = 1;
        $preventiveMaintenance->marked_by = $request->marked_by;

        if($preventiveMaintenance->update()){
            $pm_schedule = $preventiveMaintenance->pm_schedule()->first();
            $last_pm = PreventiveMaintenance::where("pm_schedule_id", $pm_schedule->id)->where("is_completed", 1)->orderBy("date_completed")->first();
            
            //come and update the next due date here
            if($last_pm->id == $preventiveMaintenance->id){
                if($pm_schedule->rescheduleBasedOnCompletion == 1){
                    
                    if($preventiveMaintenance->date_completed != null){
                        $date = Carbon\Carbon::parse($preventiveMaintenance->date_completed);
                        
                        switch($pm_schedule->recurringSchedule){
                            case "daily":
                                $date->addDay();
                                break;
                            case "weekly":
                                $date->addWeek();
                                break;
                            case "bi-weekly":
                                $date->addWeeks(2);
                                break;
                            case "monthly":
                                $date->addMonth();
                                break;
                            case "bi-monthly":
                                $date->addMonths(2);
                                break;
                            case "quarterly":
                                $date->addMonths(3);
                                break;
                            case "triannually":
                                $date->addMonths(4);
                                break;
                            case "biannually":
                                $date->addMonths(6);
                                break;
                            case "yearly":
                                $date->addYear();
                                break;
                            case "biennially":
                                $date->addYears(2);
                                break;
                            default:
                                break;
                        }
                    }

                    $pm_schedule->due_date = $date;
                    $pm_schedule->save();
                }else{
                    $date = Carbon\Carbon::parse($pm_schedule->due_date);
                    
                    switch($pm_schedule->recurringSchedule){
                        case "daily":
                            $date->addDay();
                            break;
                        case "weekly":
                            $date->addWeek();
                            break;
                        case "bi-weekly":
                            $date->addWeeks(2);
                            break;
                        case "monthly":
                            $date->addMonth();
                            break;
                        case "bi-monthly":
                            $date->addMonths(2);
                            break;
                        case "quarterly":
                            $date->addMonths(3);
                            break;
                        case "triannually":
                            $date->addMonths(4);
                            break;
                        case "biannually":
                            $date->addMonths(6);
                            break;
                        case "yearly":
                            $date->addYear();
                            break;
                        case "biennially":
                            $date->addYears(2);
                            break;
                        default:
                            break;
                    }

                    $pm_schedule->due_date = $date;
                    $pm_schedule->save();
                }
            }

            return response()->json([
                "error" => false,
                "message" => "Preventive maintenance approved."
            ]);
        }
    }

    public function decline(PreventiveMaintenance $preventiveMaintenance, Request $request){

        $preventiveMaintenance->is_completed = 0;

        if($preventiveMaintenance->update()){
            $pm_schedule = $preventiveMaintenance->pm_schedule()->first();
            $last_pm = PreventiveMaintenance::where("pm_schedule_id", $pm_schedule->id)->where("is_completed", 1)->orderBy("date_completed")->first();
            
            //come and update the next due date here
            if($last_pm->id == $preventiveMaintenance->id){
                if($pm_schedule->rescheduleBasedOnCompletion == 1){
                    
                    if($preventiveMaintenance->date_completed != null){
                        $date = Carbon\Carbon::parse($preventiveMaintenance->date_completed);
                        
                        switch($pm_schedule->recurringSchedule){
                            case "daily":
                                $date->subDay();
                                break;
                            case "weekly":
                                $date->subWeek();
                                break;
                            case "bi-weekly":
                                $date->subWeeks(2);
                                break;
                            case "monthly":
                                $date->subMonth();
                                break;
                            case "bi-monthly":
                                $date->subMonths(2);
                                break;
                            case "quarterly":
                                $date->subMonths(3);
                                break;
                            case "triannually":
                                $date->subMonths(4);
                                break;
                            case "biannually":
                                $date->subMonths(6);
                                break;
                            case "yearly":
                                $date->subYear();
                                break;
                            case "biennially":
                                $date->subYears(2);
                                break;
                            default:
                                break;
                        }
                    }

                    $pm_schedule->due_date = $date;
                    $pm_schedule->save();
                }else{
                    $date = Carbon\Carbon::parse($pm_schedule->due_date);
                    
                    switch($pm_schedule->recurringSchedule){
                        case "daily":
                            $date->subDay();
                            break;
                        case "weekly":
                            $date->subWeek();
                            break;
                        case "bi-weekly":
                            $date->subWeeks(2);
                            break;
                        case "monthly":
                            $date->subMonth();
                            break;
                        case "bi-monthly":
                            $date->subMonths(2);
                            break;
                        case "quarterly":
                            $date->subMonths(3);
                            break;
                        case "triannually":
                            $date->subMonths(4);
                            break;
                        case "biannually":
                            $date->subMonths(6);
                            break;
                        case "yearly":
                            $date->subYear();
                            break;
                        case "biennially":
                            $date->subYears(2);
                            break;
                        default:
                            break;
                    }

                    $pm_schedule->due_date = $date;
                    $pm_schedule->save();
                }
            }

            return response()->json([
                "error" => false,
                "message" => "Preventive maintenance approved."
            ]);
        }
    }
}
