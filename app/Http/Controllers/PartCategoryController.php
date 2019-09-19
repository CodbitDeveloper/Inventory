<?php

namespace App\Http\Controllers;

use App\PartCategory;
use App\Part;

use Auth;

use Illuminate\Http\Request;

class PartCategoryController extends Controller
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

        if(PartCategory::where([['hospital_id', $request->hospital_id], ['name', $request->name]])->get()->count() > 0) {
            return response()->json([
                'error' => true,
                'message' => 'Spare part category name already exists'
            ]);
        }

        $partCategory = new PartCategory();

        $partCategory->id          = md5($request->name.microtime());
        $partCategory->name        = $request->name;
        $partCategory->hospital_id = $request->hospital_id;

        if($partCategory->save()) {
            return response()->json([
                'error'   => false,
                'data'    => $partCategory,
                'message' => 'Part category saved successfully!'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Could not save part category'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PartCategory  $partCategory
     * @return \Illuminate\Http\Response
     */
    public function show(PartCategory $partCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PartCategory  $partCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(PartCategory $partCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PartCategory  $partCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PartCategory $partCategory)
    {
        $status = $partCategory->update(
            $request->only(['name'])
        );

        return response()->json([
            'data' => $partCategory,
            'message' => $status ? 'Part Category updated' : 'Error updating part category',
            'error' => !$status
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PartCategory  $partCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartCategory $partCategory)
    {
        $status = Part::where('part_category_id', $partCategory->id)->get()->count() < 1;

        if($status){
            $status = $partCategory->delete();
        }

         return response()->json([
            'error'  => !$status,
            'message' => $status ? 'Part Category deleted' : 'The selected part category already has items under it.'
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
        $action = "part category";
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

                    PartCategory::insert($insert_data);

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
