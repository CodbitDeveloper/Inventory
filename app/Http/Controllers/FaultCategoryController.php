<?php

namespace App\Http\Controllers;

use App\FaultCategory;
use Illuminate\Http\Request;

use Auth;

class FaultCategoryController extends Controller
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

        if(FaultCategory::where([['hospital_id', $request->hospital_id], ['name', $request->name]])->get()->count() > 0) {
            return response()->json([
                'error' => true,
                'message' => 'Fault category name already exists'
            ]);
        }

        $faultCategory = new FaultCategory();

        $faultCategory->id          = md5($request->name.microtime());
        $faultCategory->name        = $request->name;
        $faultCategory->hospital_id = $request->hospital_id;

        if($faultCategory->save()){
            return response()->json([
                'error'   => false,
                'data'    => $faultCategory,
                'message' => 'Fault category saved successfully!'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not save fault category'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FaultCategory  $faultCategory
     * @return \Illuminate\Http\Response
     */
    public function show(FaultCategory $faultCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FaultCategory  $faultCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(FaultCategory $faultCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FaultCategory  $faultCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FaultCategory $faultCategory)
    {
        $status = $faultCategory->update(
            $request->only(['name'])
        );

        return response()->json([
            'data'    => $faultCategory,
            'message' => $status ? 'Fault category updated' : 'Error updating fault category'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FaultCategory  $faultCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(FaultCategory $faultCategory)
    {
        $delete = $faultCategory->delete();

        if($delete) {
            return response()->json([
                'error'   => false,
                'data'    => $delete,
                'message' => 'Fault category deleted successfully!'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Error deleting fault category. Try Again!'
            ]);
        }
    }

    public function uploadCSV(){
        $user = Auth::user();
        $action = "fualt category";
        return view("upload-csv", compact("action", "user"));
    }

    public function bulkSave(Request $request){
        if($request->file('file') != null){
            //handle save data from csv
            $file = $request->file('file');

            // File Details 
            $filename = $file->getClientOriginalName();
            if(strpos($filename, "tynkerbox_category_template.csv") === false ){
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

                    FaultCategory::insert($insert_data);

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
