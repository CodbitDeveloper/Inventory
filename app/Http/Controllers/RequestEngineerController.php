<?php

namespace App\Http\Controllers;

use App\RequestEngineer;
use Illuminate\Http\Request;

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
            'message' => 'Could not send request. Try again!'
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
}
