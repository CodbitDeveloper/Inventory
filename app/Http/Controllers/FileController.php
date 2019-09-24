<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

use Auth;

class FileController extends Controller
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
            'asset_id' => 'required',
            'name'     => 'required',
            'name'   => 'mimes:pdf,docx,doc,xls,xlsx,epub'
        ]);

        if($request->hasFile('name')) {
            $file = $request->file('name');
            $fileName = $file->getClientOriginalName();
            $fileName = time(). '-' . $fileName;
            $file->move('files', $fileName);
        }

        $file = new File();

        $file->asset_id = $request->asset_id;
        $file->name     = $fileName;
        $file->id = md5($file->nam.microtime());

        if($file->save()) {
            return response()->json([
                'error'   => false,
                'data'    => $file,
                'message' => 'File uploaded successfully!'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Could not upload file. Try Again!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $delete = $file->delete();

        if($delete) {
            return response()->json([
                'error'   => false,
                'message' => 'File deleted successfully!'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Error deleting file. Try Again!'
            ]);
        }
    }

    /**
     * ---------------------------------
     * Download category CSV
     * ---------------------------------
     * 
     * @return \Illuminate\Http\Response
     */
    public function downloadCategoryCSV(){
        return response()->download(storage_path('docs/tynkerbox_category_template.csv'));
    }

    /**
     * ------------------------
     * Download files
     * ------------------------
     * 
     * @param $file
     * @return \Illuminate\Http\Response
     */
    public function download(File $file){
        if(Auth::user()->hospital_id == $file->asset()->first()->hospital_id){
            return response()->download(\public_path('/files/'.$file->name));
        }

        return abort(403);
    }
}
