<?php

namespace App\Http\Controllers;

use App\Equipment;
use App\AdminCategory;
use Illuminate\Http\Request;

use Auth;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        $equipment = Equipment::with('admin_category')->where('region_id', $admin->region_id)->get();
        return view("admin.equipment", compact('equipment', 'admin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $categories = AdminCategory::where('region_id', $admin->region_id)->get();

        return view('admin.equipment-add', compact('admin', 'categories'));
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
            'name' => 'required|string',
            'equipment_code' => 'required|string',
            'status' => 'required'
        ]);

        if(Equipment::where([['region_id', $request->region_id], ['equipment_code', $request->equipment_code]])->get()->count() > 0) {
            return response()->json([
                'error' => true,
                'message' => 'Equipment name already exists'
            ]);
        }

        $equipment = new Equipment();

        $equipment->id = md5($request->name.microtime());
        $equipment->parent_id = $request->parent_id;
        $equipment->name = $request->name;
        $equipment->type = $request->type;
        $equipment->region_id = $request->region_id;
        $equipment->equipment_code = $request->equipment_code;
        $equipment->admin_category_id = $request->admin_category_id;
        $equipment->purchase_price = $request->purchase_price;
        $equipment->purchase_date = $request->purchase_date != null ? date('Y-m-d', strtotime($request->purchase_date)) : null;
        $equipment->admin_id = $request->admin_id;
        $equipment->status = $request->status;
        $equipment->description = $request->description;
        $equipment->area = $request->area;
        $equipment->serial_number = $request->serial_number;
        $equipment->model_number = $request->model_number;
        $equipment->manufacturer_name = $request->manufacturer_name;
        $equipment->reason = $request->reason;
        $equipment->warranty_expiration = $request->warranty_expiration != null ? date('Y-m-d', strtotime($request->warranty_expiration)) : null;
        $equipment->procurement_type = $request->procurement_type;
        $equipment->donor = $request->donor;

        if($request->image != null) {
            $request->validate([
                'image' => 'mimes:png,jpg,jpeg'
            ]);

            $file = $request->file('image');
            $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path().'/img/assets/equipment', $name);

            $equipment->image = $name;
        }

        if($equipment->save()) {
            return response()->json([
                'error' => false,
                'data' => $equipment,
                'message' => 'Equipment saved successfully'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Could not save equipment. Try again!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function show(Equipment $equipment)
    {
        $admin = Auth::guard('admin')->user();

        $equipment = $equipment->with("donation", "admin_category")->first();
        $categories = AdminCategory::where("region_id", $admin->region_id)->get();
        return view('admin.equipment-details', compact('admin', 'equipment', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipment $equipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Equipment $equipment)
    {
        if($request->image != null) {
            $file = $request->file('image');

            $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path().'/img/assets/equipment', $name);

            $equipment->image = $name;
        }

        $equipment->name = $request->name;
        $equipment->parent_id = $request->parent_id;
        $equipment->type = $request->type;
        $equipment->serial_number = $request->serial_number;
        $equipment->model_number = $request->model_number;
        $equipment->manufacturer_name = $request->manufacturer_name;
        $equipment->description = $request->description;
        $equipment->admin_category_id = $request->admin_category_id;
        $equipment->area = $request->area;
        $equipment->purchase_price = $request->purchase_price;
        $equipment->donor = $request->donor;
        $equipment->purchase_date = date('Y-m-d', strtotime($request->purchase_date));
        $equipment->warranty_expiration = date('Y-m-d', strtotime($request->warranty_expiration));

        $status = $equipment->update();

        return response()->json([
            'data' => $equipment,
            'message' => $status ? 'Equipment updated' : 'Could not update equipment. Try again!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipment $equipment)
    {
        $delete = $equipment->delete();

        if($delete) {
            return response()->json([
                'error' => false,
                'message' => 'Equipment deleted'
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Could not delete equipment'
            ]);
        }
    }

    /**
     * ----------------------------------------
     * Update equipment status
     * ----------------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Equipment $equipment, Request $request){
        $equipment->status = $request->status;
        $equipment->reason = $request->reason;

        if($equipment->update()){
            return response()->json([
                "error" => false,
                "message" => "Status updated"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Could not update status"
        ]);
    }
}
