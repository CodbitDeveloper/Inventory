<?php

namespace App\Http\Controllers;

use App\AssetCategory;
use App\Asset;

use Auth;

use Illuminate\Http\Request;

class AssetCategoryController extends Controller
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
            'name'        => 'required|string',
            'hospital_id' => 'required'
        ]);

        if(AssetCategory::where([['hospital_id', $request->hospital_id], ['name', $request->name]])->get()->count() > 0) {
            return response()->json([
                'error' => true,
                'message' => 'Equipment category name already exists'
            ]);
        }

        $assetCategory = new AssetCategory();
 
        $assetCategory->id          = md5($request->name.microtime());
        $assetCategory->name        = $request->name;
        $assetCategory->hospital_id = $request->hospital_id;
        $assetCategory->parent_id   = $request->parent_id;

        if($assetCategory->save()){
            return response()->json([
                'error'   => false,
                'data'    => $assetCategory,
                'message' => 'Equipment category saved successfully!'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not save equipment category'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AssetCategory $assetCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(AssetCategory $assetCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssetCategory $assetCategory)
    {
        $assetCategory->name = $request->name;
        $assetCategory->parent_id = $request->parent_id;
        $status = $assetCategory->update();

        return response()->json([
            'data'    => $assetCategory,
            'message' => $status ? 'Equipment category updated' : 'Error updating equipment category. Try again!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AssetCategory  $assetCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetCategory $assetCategory)
    {
        $status = Asset::where('asset_category_id', $assetCategory->id)->get()->count() < 1;

        if($status){
            $status = $assetCategory->delete();
        }

         return response()->json([
            'error'   => !$status,
            'message' => $status ? 'Equipment category deleted' : 'The selected equipment category already has items under it.'
         ]);
    }

    /**
     * ------------------------------------------
     * Upload CSV for regional equipment category
     * ------------------------------------------
     * 
     * @return view 
     */
    public function uploadCSV(){
        $user = Auth::user();
        $action = "equipment category";
        return view("upload-csv", compact("action", "user"));
    }

    /**
     * -------------------------------------------
     * Bulk upload for CSV files
     * -------------------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkSave(Request $request){
        if($request->file('file') != null){
            //handle save data from csv
            $file = $request->file('file');

            // File Details 
            $filename = $file->getClientOriginalName();
            
            if(strpos($filename, "tynkerbox_category_template") === false ){
                return response()->json([
                    "error" => true,
                    "message" => 'Invalid file uploaded'
                ]);
            }
            
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 4.96MB in Bytes
            $maxFileSize = 5097152;
            
            if(in_array(strtolower($extension), $valid_extension)){
                if($fileSize <= $maxFileSize){
                    $location = "docs";
                    
                    // Upload file
                    $file->move($location,$filename);

                    // get path of csv file
                    $filepath = public_path($location."/".$filename);

                    // Reading file
                    $file = fopen($filepath,"r");

                    $data_array = array();
                    $insert_data = array();
                    $i = 1;

                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        
                        if($i == 1){
                            $i++;
                            continue;
                        }

                        for ($c=0; $c < $num; $c++) {
                           $data_array[$i][] = $filedata [$c];
                        }
                        
                        $i++;
                    }
                    fclose($file);

                    foreach($data_array as $data){
                        array_push($insert_data, array(
                            "id" => md5(microtime().$data[0]),
                            "name" => $data[0],
                            "hospital_id" => $request->hospital_id,
                            "created_at" => date("Y-m-d"),
                            "updated_at" => date("Y-m-d")
                        ));
                    }

                    AssetCategory::insert($insert_data);

                    return response()->json([
                        'error' => false,
                        "message" => "Data retrieved",
                        "data" => $insert_data
                    ]);
                }else{
                    return responose()->json([
                        "error" => true, 
                        "message" => "The provided file is too large"
                    ]);
                }
            }else{
                return response()->json([
                    "error" => true,
                    "message" => 'Invalid file format received'
                ]);
            }
        }else{
            return response()->json([
                "error" => true,
                "message" => 'No file received'
            ]);
        }
    }
}
