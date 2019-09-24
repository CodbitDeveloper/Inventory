<?php

namespace App\Http\Controllers;

use App\PmSchedule;
use App\Hospital;
use App\PmAction;

use Auth;
use Illuminate\Http\Request;

class PmScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role == 'Admin' || $user->role == 'Regular Technician'){
            $pmSchedules = PmSchedule::with("priority")->where('hospital_id', $user->hospital_id)->get();
            return view("pm-types", compact("pmSchedules", "user"));
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        if($user->role == 'Admin' || $user->role == 'Regular Technician'){
            $hospital = Hospital::where("id", $user->hospital_id)->with("priorities", "asset_categories", "assets", "departments", "departments.units")->first();
            return view('pm-types-add', \compact("hospital", "user"));
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
            'title'             => 'required',
            'recurringSchedule' => 'required',
            'due_date'          => 'required'
        ]);

        $pmSchedule = new PmSchedule();
        
        $pmSchedule->id                = md5($request->title.microtime());
        $pmSchedule->title             = $request->title;
        $pmSchedule->recurringSchedule = $request->recurringSchedule;
        $pmSchedule->due_date          = date('Y-m-d H:i:s', strtotime($request->due_date));
        $pmSchedule->endDueDate        = $request->endDueDate != null ? date('Y-m-d', strtotime($request->endDueDate)) : null;
        $pmSchedule->department_id     = $request->department_id;
        $pmSchedule->unit_id           = $request->unit_id;
        $pmSchedule->priority_id       = $request->priority_id;
        $pmSchedule->hospital_id       = $request->hospital_id;
        $pmSchedule->description       = $request->description;
        $pmSchedule->asset_category_id = $request->asset_category_id;
        $pmSchedule->recorded_by       = $request->recorded_by;
        
        if($request->rescheduledBasedOnCompletion == "on"){
            $pmSchedule->rescheduledBasedOnCompletion = 1;
        }else if($request->rescheduledBasedOnCompletion == "off"){
            $pmSchedule->rescheduledBasedOnCompletion = 0;
        }

        if($pmSchedule->save()){
            if($request->assets != null){
                $pmSchedule->assets()->attach($request->assets);
            }

            if($request->actions != null){
                $actions = explode("," , $request->actions);
                $pmActions = array();
                
                foreach($actions as $action){
                    array_push($pmActions, array("pm_schedule_id" => $pmSchedule->id, "name" => $action, "created_at" => date('Y-m-d H:i:s')));
                }

                PmAction::insert($pmActions);
            }

            
            return response()->json([
                'error'       => false,
                'Pm Schedule' => $pmSchedule,
                'message'     => 'Preventive Maintenance Schedule created successfully'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not create prevent maintenance schedule. Try Again!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PmSchedule  $pmSchedule
     * @return \Illuminate\Http\Response
     */
    public function show($pmSchedule)
    {
        $user = Auth::user();

        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $pmSchedule = PmSchedule::with("preventive_maintenances", "preventive_maintenances.user", "priority", "department", "unit", "asset_category", "assets", "actions")->where("id", $pmSchedule)->first();
            $hospital = Hospital::where("id", $user->hospital_id)->with("priorities", "asset_categories", "assets", "departments", "departments.units")->first();
            return view("pm-type-details", compact("pmSchedule", "hospital", "user"));
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PmSchedule  $pmSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(PmSchedule $pmSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PmSchedule  $pmSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PmSchedule $pmSchedule)
    {
        $pmSchedule->title = $request->title;
        $pmSchedule->recurringSchedule = $request->recurringSchedule;
        $pmSchedule->due_date = date('Y-m-d H:i:s', strtotime($request->due_date));
        $pmSchedule->endDueDate = $request->endDueDate != null ? date('Y-m-d', strtotime($request->endDueDate)) : null;
        $pmSchedule->department_id = $request->department_id;
        $pmSchedule->unit_id = $request->unit_id;
        $pmSchedule->priority_id = $request->priority_id;
        $pmSchedule->description = $request->description;
        $pmSchedule->asset_category_id = $request->asset_category_id;
        $pmSchedule->recorded_by = $request->recorded_by;

        if($request->rescheduledBasedOnCompletion == 'on') {
            $pmSchedule->rescheduledBasedOnCompletion = 1;
        } else if ($request->rescheduledBasedOnCompletion = 'off') {
            $pmSchedule->rescheduledBasedOnCompletion = 0;
        }

        if($pmSchedule->update()) {
            return response()->json([
                'error' => false,
                'data' => $pmSchedule,
                'message' => 'Preventive maintenance schedule updated'
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'Could not update preventive maintenance schedule. Try again!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PmSchedule  $pmSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(PmSchedule $pmSchedule)
    {
        $status = $pmSchedule->delete();

        if($status) {
            $pmSchedule->assets()->detach();

            return response()->json([
                'error'   => false,
                'message' => 'Preventive Maintenance Schedule deleted successfully'
            ]); 
        }

        return response()->json([
            'error'  => true,
            'message' => 'Could not delete preventive maintenance schedule. Try Again!'
        ]);
    }

    /**
     * -------------------------------
     * Display PM-Schedule with assets
     * -------------------------------
     * 
     * @param $id
     */
    public function getPmSchedule($id)
    {
        $pmSchedule = PmSchedule::with('assets')->where([['id', $id], ['hospital_id', Auth::user()->hospital_id]])->first();

        return view();
    }

    /**
     * -----------------------
     * Add task to PM-Schedule
     * ------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PmSchedule  $pmSchedule
     * @return \Illuminate\Http\Response
     */
    public function addTask(PmSchedule $pmSchedule, Request $request){
        $pmAction = new PmAction();
        $pmAction->pm_schedule_id = $pmSchedule->id;
        $pmAction->name = $request->name;

        $pmAction->save();

        return response()->json([
            "error" => false,
            "message" => "Task added",
            "data" => $pmAction
        ]);
    }

    /**
     * ---------------------
     * Delete task 
     * ---------------------
     * 
     * @param  \App\PmAction  $pmAction
     * @return \Illuminate\Http\Response
     */
    public function deleteTask(PmAction $pmAction){
        $pmAction->delete();
        return response()->json([
            "error" => false,
            "message" => "Task deleted"
        ]);
    }

    /**
     * -----------------------------
     * Get PmSchedules for mobile app
     * ------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function appGetPmSchedules(Request $request){
        $request->validate([
            "hospital_id" => "required"
        ]);
        
        $pmSchedules = PmSchedule::where("hospital_id", $request->hospital_id)->with("actions", "department", "unit", "priority", "asset_category")->get();
        return response()->json($pmSchedules);
    }

    /**
     * -------------------------------------
     * Get PmSchedule history for mobile app
     * -------------------------------------
     * 
     * @param  \App\PmSchedule  $pmSchedule
     * @return \Illuminate\Http\Response
     */
    public function appGetPmScheduleHistory(PmSchedule $pmSchedule){
        return response()->json($pmSchedule->preventive_maintenances()->get());
    }
}
