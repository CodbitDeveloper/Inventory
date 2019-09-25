<?php

namespace App\Http\Controllers;

use App\RequestEngineer;
use App\Admin;
use App\WorkOrder;

use Illuminate\Http\Request;

use Auth;

class RequestEngineerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::guard("admin")->user();
        $requests = RequestEngineer::whereHas("work_order", function($q) use ($user){
            $q->whereHas("hospital", function($query) use ($user){
                $query->whereHas("district", function($query) use ($user){
                    $query->where("region_id", $user->region_id);
                });
            });
        })->with("work_order", "work_order.hospital", "work_order.priority", "engineer")->get();

        $engineers = Admin::where("region_id", $user->region_id)->get();
        return view("admin.requests", \compact("requests", "engineers"));
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
        $request->validate([
            'work_order_id' => 'required|string',
            'description' => 'required'
        ]);

        $requestEngineer = new RequestEngineer();

        $requestEngineer->id = md5('Request'.microtime().rand(1, 1000));
        $requestEngineer->description = $request->description;
        $requestEngineer->work_order_id = $request->work_order_id;
        $requestEngineer->assigned_to = $request->assigned_to;
        $requestEngineer->assigned_date = $request->assigned_date;

        if($requestEngineer->save()) {
            return response()->json([
                'error' => false,
                'data' => $requestEngineer,
                'message' => 'Request sent successfully'
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'Could not send request. Try again'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RequestEngineer  $requestEngineer
     * @return \Illuminate\Http\Response
     */
    public function show(RequestEngineer $requestEngineer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RequestEngineer  $requestEngineer
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestEngineer $requestEngineer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequestEngineer  $requestEngineer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequestEngineer $requestEngineer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RequestEngineer  $requestEngineer
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestEngineer $requestEngineer)
    {
        //
    }

    public function assign(Request $request){
        $request->validate([
            "admin_id" => "required",
            "request_id" => "required"
        ]);

        $requestEngineer = RequestEngineer::where("id", $request->request_id)->first();

        $requestEngineer->update(["status" => 1, "assigned_to" => $request->admin_id]);
        $requestEngineer->work_order()->update(["admin_id" => $request->admin_id]);

        return response()->json([
            "error" => false,
            "message" => "Request approved"
        ]);
    }

    public function decline(RequestEngineer $requestEngineer){
        $requestEngineer->update(["status" => 0, "assigned_to" => null]);
        $requestEngineer->work_order()->update(["admin_id" => null]);

        return response()->json([
            "error" => false,
            "message" => "Request declined"
        ]);
    }

    public function revertDecline(RequestEngineer $requestEngineer){
        $requestEngineer->update(["status" => 2, "assigned_to" => null]);

        return response()->json([
            "error" => false,
            "message" => "Request reverted"
        ]);
    }
}
