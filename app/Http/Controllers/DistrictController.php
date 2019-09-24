<?php

namespace App\Http\Controllers;

use App\District;
use Illuminate\Http\Request;
use Auth;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = Auth::guard("admin")->user();
        $districts = District::where("region_id", $admin->region_id)->whereDoesntHave("parent")->with("children", "children.children")->get();

        return view("admin.districts", compact("districts", "admin"));
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
            'name'      => 'required|string',
            'region_id' => 'required',
            'type' => 'required|string'
        ]);

        $district = new District();

        $district->id        = md5($request->name.microtime());
        $district->name      = $request->name;
        $district->region_id = $request->region_id;
        $district->type      = $request->type;

        if(District::where('name', $request->name)->get()->count() > 0){
            return response()->json([
                'error' => $result,
                'message' => 'District name already exists'
            ]);
        }

        if($district->save()){
            $result = false;
        }

        return response()->json([
            'error'   => $result,
            'data'    => $district,
            'message' => !$result ? 'District created successfully' : 'Error creating district'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function show(District $district)
    {
        return response()->json($district, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function edit(District $district)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, District $district)
    {
        $status = $district->update(
            $request->only(['name', 'type'])
        );

        return response()->json([
            'data' => $district,
            'message' => $status ? 'District Updated' : 'Error updating district'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\District  $district
     * @return \Illuminate\Http\Response
     */
    public function destroy(District $district)
    {
        //
    }

    /**
     * -------------------------------
     * Display hospitals for districts
     * -------------------------------
     * 
     * @return \Illuminate\Http\Response
     */
    public function viewHospitals(District $district){
        return response()->json($district->hospitals()->get());
    }
}
