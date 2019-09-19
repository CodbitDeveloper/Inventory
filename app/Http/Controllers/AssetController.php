<?php

namespace App\Http\Controllers;

use App\Asset;
use App\Hospital;
use App\AssetCategory;

use Auth;
use Illuminate\Http\Request;

use DB;
use Carbon;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user->role == 'Admin' || $user->role == 'Regular Technician' || $user->role == 'Hospital Head'){
            $assets = Asset::with('asset_category')->where('hospital_id', $user->hospital_id)->get();
            return view("assets", compact("assets", "user"));
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
        //
        $user = Auth::user();
        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $hospital = Hospital::with("assets", "asset_categories", "departments",
            "departments.units", "services", "parts")->with(["users" => function($q){
                $q->where("role", "Admin")->orWhere("role", "Regular Technician")->orWhere("role", "Limited Technician");
            }])->where("id", $user->hospital_id)->first();

            return view('add-item', compact("hospital", "user"));
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
            'name'           => 'required',
            'asset_code'     => 'required',
            'status'         => 'required',
            'availability'   => 'required'    
         ]);

         if(Asset::where([['hospital_id', $request->hospital_id], ['asset_code', $request->asset_code]])->get()->count() > 0) {
             return response()->json([
                 'error' => true,
                 'message' => 'Equipment code already exists'
             ]);
         }

         $asset = new Asset(); 

         $asset->id                  = md5($request->name.microtime());
         $asset->parent_id           = $request->parent_id;
         $asset->name                = $request->name;
         $asset->asset_code          = $request->asset_code;
         $asset->asset_category_id   = $request->asset_category_id;
         $asset->purchase_price      = $request->purchase_price;
         $asset->purchase_date       = $request->purchase_date != null ? date('Y-m-d', strtotime($request->purchase_date)) : null;
         $asset->user_id             = $request->user_id;
         $asset->installation_date   = $request->installation_date != null ? date('Y-m-d', strtotime($request->installation_date)) : null;
         $asset->status              = $request->status;
         $asset->availability        = $request->availability;
         $asset->description         = $request->description;
         $asset->area                = $request->area;
         $asset->department_id       = $request->department_id;
         $asset->unit_id             = $request->unit_id;
         $asset->pos_rep_date        = $request->pos_rep_date != null ? date('Y-m-d', strtotime($request->pos_rep_date)) : null;
         $asset->serial_number       = $request->serial_number;
         $asset->model_number        = $request->model_number;
         $asset->manufacturer_name   = $request->manufacturer_name;
         $asset->service_vendor_id   = $request->service_vendor_id;
         $asset->hospital_id         = $request->hospital_id;
         $asset->procurement_type    = $request->procurement_type;
         $asset->reason              = $request->reason;
         $asset->warranty_expiration = $request->warranty_expiration != null ? date('Y-m-d', strtotime($request->warranty_expiration)) : null;
         $asset->donor               = $request->donor;

         if($request->image != null) {
            $request->validate([
                'image'   => 'mimes:png,jpg,jpeg'
            ]);

            $file = $request->file('image');

            $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path().'/img/assets/equipment/', $name);
            
            $asset->image = $name;
         }

        if($asset->save()) {

            if($request->parts != null){
                $asset->parts()->attach($request->parts);
            }

            return response()->json([
                'error'   => false,
                'data'    => $asset,
                'message' => 'Asset saved successfully!'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Could not save asset. Try Again!' 
            ]); 
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function show($asset)
    {
        $user = Auth::user();

        if($user->role == 'Admin' || $user->role == 'Regular Technician' || $user->role == 'Hospital Head') {
            $asset = Asset::with("unit", "department", "asset_category", "service_vendor", "work_orders")->where("id", $asset)->first();
        
            $hospital = Hospital::where("id", $user->hospital_id)->with(["assets" => function($q) use ($asset){
                $q->where("id", "<>", $asset->id);
            }])->with("asset_categories", 
            "departments", "departments.units", "services", "users", "parts")->first();
            
            return view("asset-details", compact("asset", "hospital", "user"));
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function edit(Asset $asset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Asset $asset)
    {
        if($request->image != null){
            $file = $request->file('image');

            $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path().'/img/assets/equipment/', $name);
            
            $asset->image = $name;
        }

        $asset->name = $request->name;
        $asset->parent_id = $request->parent_id;
        $asset->serial_number = $request->serial_number;
        $asset->model_number = $request->model_number;
        $asset->manufacturer_name = $request->manufacturer_name;
        $asset->description = $request->description;
        $asset->asset_category_id = $request->asset_category_id;
        $asset->area = $request->area;
        $asset->service_vendor_id = $request->service_vendor_id;
        $asset->installation_date = date('Y-m-d', strtotime($request->installation_date));
        $asset->pos_rep_date = date('Y-m-d' , strtotime($request->pos_rep_date));
        $asset->purchase_date = date('Y-m-d', strtotime($request->purchase_date));
        $asset->warranty_expiration = date('Y-m-d', strtotime($request->warranty_expiration));
        
        $status = $asset->update();

        return response()->json([
            'data' => $asset,
            'message' => $status ? 'Equipment Updated' : 'Error updating equipment'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Asset $asset)
    {
        //
        if($asset->delete()){
            return response()->json([
                "error" => false,
                "message" => "Equipment deleted" 
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Could not delete equipment"
        ]);
    }

    /**
     * -------------------------------------------
     * Get all spare parts associated with assets
     * --------------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function getParts(Asset $asset){
        return response()->json($asset->parts()->get());
    }

    /**
     * -------------------------------------
     * Get all files associated with assets
     * -------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function getFiles(Asset $asset){
        return response()->json($asset->files()->get());
    }

    /**
     * --------------------------------------------------
     * Get children assets associated with parent assets
     * --------------------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function getChildren(Asset $asset){
        return response()->json($asset->children()->get());
    }

    /**
     * -------------------------------------
     * Assign child asset to a parent asset
     * -------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignChild(Request $request, Asset $asset){
        $request->validate([
            "children" => "required"
        ]);

        Asset::whereIn("id", $request->children)->update(["parent_id" => $asset->id]);
        
        return response()->json([
            "error" => false,
            "message" => "Child equipment assigned"
        ]);
    }

    /**
     * ------------------------------------------
     * Assign spare part associated with an asset
     * ------------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignPart(Request $request, Asset $asset){
        $request->validate([
            "parts" => "required"
        ]);

        $asset->parts()->attach($request->parts);
        
        return response()->json([
            "error" => false,
            "message" => "Parts assigned"
        ]);
    }

    /**
     * ------------------------
     * Depreciation for asset
     * ------------------------
     * 
     * @param  \App\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function depreciation(Asset $asset){
        $downtime = DB::select(DB::raw("SELECT SUM(TIMESTAMPDIFF(hour, time_up, time_down)) as downtime from down_time where asset_id = '$asset->id'"))[0];
        $running_time = Carbon\Carbon::parse($asset->created_at)->diffInHours(Carbon\Carbon::now());
        return response()->json([
            "downtime" => $downtime,
            "running_time" => $running_time
        ]);
    }

    /**
     * --------------------------------
     * Update downtime and uptime
     * --------------------------------
     * 
     * @param  \App\Asset  $asset
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggle(Request $request, Asset $asset){
        $request->validate([
            "availability" => "required",
            "status" => "required"
        ]);

        $asset->availability = $request->availability;
        $asset->status = $request->status;

        if($asset->save()){
            $query = DB::select(DB::raw("SELECT COUNT(id) as kount FROM down_time where asset_id = '$asset->id' AND time_up IS NULL"))[0];
            if(strtolower($asset->availability) == "operational"){
               if($query->kount > 0){
                    $query = DB::statement("UPDATE down_time SET time_up = NOW() where asset_id = '$asset->id' and time_up IS NULL");
               }
            }else{
               if($query->kount == 0){
                    $query = DB::statement("INSERT INTO down_time(time_down, asset_id) VALUES (NOW(), '$asset->id')");
               }
            }

            return response()->json([
                "error" => false,
                "message" => "Asset updated"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Update failed"
        ]);
    }

    private function formatDate($date){
        return date("Y-m-d H:i:s", strtotime(stripslashes($date)));
    }

    /**
     * ----------------------------
     * Get assets for a hospital
     * ----------------------------
     * 
     * @param $hospital
     */
    public function get($hospital){
        $assets = Asset::where("hospital_id", $hospital)->get();
        return response()->json($assets);
    }
    
    /**
     * -------------------------------------------
     * Remove spare part associated with an asset
     * -------------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removePart(Asset $asset, Request $request){
        $asset->parts()->detach($request->part_id);
        return response()->json([
            "error" => false,
            "message" => "Part unlinked successfully"
        ]);
    }

    /**
     * ------------------------------------------------
     * Remove child asset associated with parent asset
     * ------------------------------------------------
     * 
     * @param  \App\Asset  $asset
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeChild(Asset $asset, Request $request){
        Asset::where("id", $request->child_id)->update(["parent_id" => null]);
        return response()->json([
            "error" => false,
            "message" => "Child unlinked successfully"
        ]);
    }

    /**
     * -------------------------------
     * Search for asset on mobile app
     * -------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request){
        $request->validate([
            "q" => "required",
            "hospital_id" => "required"
        ]);

        $equipment = Asset::with("asset_category", "parent", "user", "department", "unit", "service_vendor")->where("hospital_id", $request->hospital_id)->where(function($q) use ($request){
            $q->where("asset_code", 'LIKE', '%'.$request->q.'%')->orWhere("serial_number", 'LIKE', '%'.$request->q.'%');
        })->get();

        
        return response()->json($equipment);
    }
}
