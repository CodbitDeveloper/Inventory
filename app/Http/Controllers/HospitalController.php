<?php

namespace App\Http\Controllers;

use App\Hospital;
use Illuminate\Http\Request;
use DB;
use Auth;
use App\District;
use App\User;
use App\Service_Vendor;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hospitals = Hospital::with('district')->whereHas("district", function($q){
            $q->where("region_id", Auth::guard("admin")->user()->region_id);
        })->get();

        return view('admin.hospitals')->with('hospitals', $hospitals);
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
            'name'           => 'required|string',
            'address'        => 'required|string',
            'district_id'    => 'required',
            'contact_number' => 'required'
        ]);

        $hospital  = new Hospital();

        $hospital->id             = md5($request->name.microtime());
        $hospital->name           = $request->name;
        $hospital->address        = $request->address;
        $hospital->district_id    = $request->district_id;
        $hospital->contact_number = $request->contact_number;
        $hospital->type           = $request->type;

        if($d = $hospital->save()){
            $service = new Service_Vendor();

            $service->name           = 'None';
            $service->contact_number = 'No contact';
            $service->hospital_id    = $hospital->id;

            $ghs = new Service_Vendor();

            $ghs->name = 'Ghana Health Service';
            $ghs->contact_number = 'No contact';
            $ghs->hospital_id = $hospital->id;

            if($service->save() && $ghs->save()){
                $user = new User();

                $user->id           = md5($request->username.microtime());
                $user->firstname    = $request->firstname;
                $user->lastname     = $request->lastname;
                $user->username     = $request->username;
                $user->phone_number = $request->phone_number;
                $user->password     = bcrypt('Password');
                $user->hospital_id  = $hospital->id;
                $user->role         = 'Admin';
    
                if($user->save()){
                    $result = false;
                }   
            }
        }
        

        return response()->json([
            'error'   => $result,
            'data'    => $hospital,
            'message' => !$result ? 'Hospital created successfully' : 'Error creating hospital'
          ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function show(Hospital $hospital)
    {
        return response()->json($hospital, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function edit(Hospital $hospital)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hospital $hospital)
    {
        $status = $hospital->update(
            $request->only(['name', 'address', 'district_id', 'contact_number'])
        );

        return response()->json([
            'data'    => $hospital,
            'message' => $status ? 'Hospital Updated' : 'Error updating hospital'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospital $hospital)
    {
        //
    }

    /**
     * ------------------------------------
     * Display single hospital for regional admin
     * -------------------------------------
     * 
     *@return view
     */
    public function viewHospital(Hospital $hospital){
        return view('admin.single_hospital')->with('hospital', $hospital);
    }

    /**
     * ----------------------------------------------------
     * Get equipment for hospitals with equipment category
     * ----------------------------------------------------
     * 
     * @param \App\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function getEquipment(Hospital $hospital){
        return response()->json($hospital->assets()->with("asset_category")->get());
    }

    /**
     * ---------------------------------------------
     * Get department of the hospital with the units
     * ----------------------------------------------
     * 
     * @param \App\Hospital  $hospital
     * @return \Illuminate\Http\Response
     */
    public function getDepartments(Hospital $hospital){
        return response()->json($hospital->departments()->with("units")->get());
    }
}
