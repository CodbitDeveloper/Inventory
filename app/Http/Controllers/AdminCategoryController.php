<?php

namespace App\Http\Controllers;

use App\AdminCategory;
use App\Equipment;

use Illuminate\Http\Request;

use Auth;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $admin = Auth::guard("admin")->user();
        $categories = AdminCategory::where("region_id", $admin->region_id)->get();
        return view("admin.categories", compact("categories", "admin"));
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
            'name' => 'required|string',
            'region_id' => 'required|string'
        ]);

        if(AdminCategory::where([['region_id', $request->region_id], ['name', $request->name]])->get()->count() > 0) {
            return response()->json([
                'error' => true,
                'message' => "Equipment category name already exists"
            ]);
        }

        $adminCategory = new AdminCategory();

        $adminCategory->id = md5($request->name.microtime());
        $adminCategory->name = $request->name;
        $adminCategory->region_id = $request->region_id;
        $adminCategory->parent_id = $request->parent_id;

        if($adminCategory->save()) {
            return response()->json([
                'error' => false,
                'data' => $adminCategory,
                'message' => 'Equipment category saved successfully'
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'Could not save equipment category. Try Again!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AdminCategory  $adminCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AdminCategory $adminCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AdminCategory  $adminCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(AdminCategory $adminCategory)
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AdminCategory  $adminCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdminCategory $adminCategory)
    {
        $adminCategory->name = $request->name;
        $adminCategory->parent_id = $request->parent_id;

        $status = $adminCategory->update();

        return response()->json([
            'data' => $adminCategory,
            'message' => $status ? 'Equipment category updated' : 'Error updating equipment category. Try again!' 
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AdminCategory  $adminCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdminCategory $adminCategory)
    {
        $status = Equipment::where('admin_category_id', $adminCategory->id)->get()->count() < 1;

        if($status) {
            $status = $adminCategory->delete();
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
    public function uploadCSV() 
    {
        $admin = Auth::guard('admin')->user();
        $action = 'Equipment category';
        return view('upload-csv', compact('action', 'admin'));
    }

    /**
     * -------------------------------------------
     * Bulk upload for CSV files
     * -------------------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkSave(Request $request) {
        if($request->file('file') != null) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            if(strpos($filename, "tynkerbox_category_template") === false) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid file uploaded'
                ]);
            }

            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            $valid_extension = array('csv');

            $maxFileSize = 5097152;
            
            if(in_array(strtolower($extension), $valid_extension)) {
                if($fileSize <= $maxFileSize) {
                    $location = 'docs';

                    // Upload file
                    $file->move($location, $filename);

                    //get path of csv file
                    $filepath = public_path($location."/".$filename);

                    // Reading file
                    $file = fopen($filepath, "r");

                    $data_array = array();
                    $insert_array = array();
                    $i = 1;

                    while(($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        if($i == 1) {
                            $i++;
                            continue;
                        }

                        for($c = 0; $c < $num; $c++) {
                            $data_array[$i][] = $filedata[$c];
                        }

                        $i++;
                    }

                    fclose($file);

                    foreach($data_array as $data) {
                        array_push($insert_array, array(
                            "id" => md5($data[0].microtime()),
                            "name" => $data[0],
                            "region_id" => $request->region_id,
                            "created_at" => date("Y-m-d"),
                            "updated_at" => date("Y-m-d")
                        ));
                    }

                    AdminCategory::insert($insert_array);

                    return response()->json([
                        'error' => false,
                        'data' => $insert_array,
                        'message' => 'Data retrieved'
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'message' => 'The provided file is too large'
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid file format received'
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'No file received'
            ]);
        }
    }
}
