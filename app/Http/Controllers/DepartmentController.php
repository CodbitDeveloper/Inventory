<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use Auth;
use App\Hospital;
use App\User;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::all();

        return response()->json($departments, 200);
    }

    /**
     * -------------------------------
     * Get department with it's units
     * -------------------------------
     * 
     * @return \Illuminate\Http\Response
     */
    public function getDepartmentUnits()
    {
        $department = Department::with('units')->get();

        return response()->json($department, 200);
    }

    /**
     * ------------------------------------------
     * Get all departments with units and assets
     * ------------------------------------------
     * 
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $department = Department::with(['units', 'assets'])->get();

        return response()->json($department, 200);
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
            'name'        => 'required|string',
            'hospital_id' => 'required'
        ]);

        if(Department::where([['hospital_id', $request->hospital_id], ['name', $request->name]])->get()->count() > 0){
            return response()->json([
                'error'   => $result,
                'message' => 'Department name already exists'
            ]);
        }

        $department = new Department;

        $department->id           = md5($request->name.microtime());
        $department->name         = $request->name;
        $department->hospital_id  = $request->hospital_id;
        $department->user_id      = $request->user_id;
        $department->location     = $request->location;
        $department->phone_number = $request->phone_number;

        if($department->save()){
            $result = false;
        }

        return response()->json([
            'error'   => $result,
            'data'    => $department,
            'message' => !$result ? 'Department created successfully' : 'Error creating department'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return response()->json($department, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $department = Department::where('id', $request->department)->first();
        $status = true;

        $request->validate([
            'name' => 'required',
            'user_id' => 'required',
            'location' => 'required',
            'phone_number' => 'required'
        ]);

        $department->name = $request->name;
        $department->user_id = $request->user_id;
        $department->location = $request->location;
        $department->phone_number = $request->phone_number;

        if($department->update()) {
            $status = false;
        }

        return response()->json([
            'data' => $department,
            'error' => $status,
            'message' => !$status ? 'Department updated successfully' : 'Error updating department'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        //
    }

    /**
     * ---------------------
     * Display all department
     * ---------------------
     * 
     * @return view
     */
    public function viewAll(){
        $user = Auth::user();
        if($user->role == 'Admin' || $user->role == 'Regular Technician') { 
            $departments = Department::with('units')->where('hospital_id', $user->hospital_id)->orderBy('name', 'ASC')->paginate(20);
            return view('departments')->with('departments', $departments)->with('user', $user);
        } else if($user->role == 'View Only'){
             $departments = Department::with('units')->where([['hospital_id', $user->hospital_id], ['user_id', $user->id]])->orderBy('name', 'ASC')->paginate(20);
            return view('departments')->with('departments', $departments)->with('user', $user);
        }else {
            abort(403);
        }
    }
    
    /**
     * ------------------------
     * Display department details
     * ------------------------
     * 
     * @param $department
     * @return view
     */
    public function view($department){
        $user = Auth::user();
        
        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $department = Department::where([['id' , $department], ['hospital_id', $user->hospital_id]])->with("units", "assets", "units.assets", "units.user", "user")->first();
            $hospital = Hospital::with('users')->where('id', $user->hospital_id)->first(); 
    
            if($department == null){
                return abort(404);
            }
    
            return view('department-details', compact('department', 'hospital', 'user'));  
        } else if($user->role == 'View Only') {
            $department = Department::where([['id', $department], ['user_id', $user->id]])->with("units", "assets", "units.assets", "units.user", "user")->first();
            $hospital = Hospital::with('users')->where('id', $user->hospital_id)->first(); 

            return view('department-details', compact('department', 'hospital', 'user'));
        } else {
            abort(403);
        }
        
    }
}
