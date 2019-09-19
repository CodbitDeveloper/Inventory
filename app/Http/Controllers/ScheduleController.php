<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\WorkOrder;
use App\PmSchedule;

use Illuminate\Http\Request;
use Auth;

use App\Asset;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if(strtolower(Auth::user()->role) == 'admin' || strtolower(Auth::user()->role) == 'regular technician'){
            $equipment = Asset::where('hospital_id', '=', Auth::user()->hospital_id)->get();
            return view('schedule', compact("equipment", "user"));
        }else{
            return abort(403);
        }
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
        $result = true;
        $request->validate([
            'equipment_code' => 'required|string',
            'maintenance_type' => 'required',
            'maintenance_date' => 'required'
        ]);

        $schedule  = new Schedule();

        $schedule->equipment_code = $request->equipment_code;
        $schedule->maintenance_type = $request->maintenance_type;
        $schedule->maintenance_date = $request->maintenance_date;

        if($schedule->save()){
            $result = false;
        }

        return response()->json([
            'error' => $result,
            'data' => $schedule
          ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }

    /**
     * -----------------------------------------
     * Get all PM-Schedule to populate calendar
     * ------------------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchAll(Request $request){
        $start = date("Y-m-d", \strtotime($request->start));
        $end = date("Y-m-d", \strtotime($request->end));
        
        $hospital_id = Auth::user()->hospital_id;
        
        $work_order_events = WorkOrder::where("hospital_id", $hospital_id)
        ->with("priority", "fault_category")->whereDate("due_date", ">=" , $start)->
        whereDate("due_date", "<=", $end)->get();

        $pm_events = PmSchedule::where("hospital_id", $hospital_id)
        ->whereDate("due_date", ">=" , $start)->
        whereDate("due_date", "<=", $end)->get();
        
        $response = array();
        
        foreach($work_order_events as $event){
            $temp = array();
            $temp['id'] = $event->id;
            $temp['title'] = $event->title;
            $temp["description"] = "Work order to be worked on by ".date('jS F Y', strtotime($event->due_date));
            
            if($event->priority != null){
                $temp["description"] .= " Priority: ".$event->priority->name;
            }

            if($event->fault_category != null){
                $temp["description"] .= " Fault category: ".$event->fault_category->name;
            }
            
            $temp['start'] = $event->due_date;
            $temp['end'] = $event->due_date;

            array_push($response, $temp);
        }

        foreach($pm_events as $event){
            $temp = array();
            $temp['id'] = $event->id;
            $temp['title'] = $event->title;
            $temp["description"] = "Preventive maintenance to be worked on by ".date('jS F Y', strtotime($event->due_date));
            $temp['start'] = $event->due_date;
            $temp['end'] = $event->due_date;

            array_push($response, $temp);
        }

        return response()->json($response);
    }

}
