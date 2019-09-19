<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\User;
use App\Hospital;
use Auth;
use DB;
use Mail;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $hospital = Hospital::with("setting")->where("id", $user->hospital_id)->first();
        return view('setting')->with('hospital', $hospital)->with('user', $user);
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
            'hospital_id' => 'required',
            'user_id' => 'required'
        ]);

        $settings  = new Setting();

        $settings->hospital_id = $request->hospital_id;
        $settings->user_id = $request->user_id;

        return response()->json([
            'error' => $result,
            'data' => $settings,
            'message' => !$result ? 'Settings created successfully' : 'Error creating settings'
        ]);
    }   

    /**
     * Display the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the spec ified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        $status = $setting->update($request->all());

        return response()->json([
            'data' => $setting,
            'status' => $status ? 'Settings Updated Successfully' : 'Error Updating Settings'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }

    /**
     * ---------------------------------------------------
     * Generate request URL (link) for work order request
     * ---------------------------------------------------
     * 
     * @param  $hospital_id
     * @return \Illuminate\Http\Response
     */
    public function generateRequestLink($hospital_id){
        $hospital = Hospital::where("id", $hospital_id)->first();

        if($hospital == null){
            return response()->json([
                "error" => true,
                "message" => "Invalid request"
            ]);
        }

        $setting = $hospital->setting()->first();
        
        if($setting == null){
            $setting = new Setting();
            $setting->hospital_id = $hospital->id;
            $setting->createLink();
        }else{
            $setting->createLink();
        }

        if($setting->save()){
            return response()->json([
                "error" => false,
                "data" => $setting,
                "message" => "Link generated"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Could not save setting"
        ]);
    }

    /**
     * ----------------------------------
     * Send request URL (link)
     * ----------------------------------
     * 
     * @param $hospital_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendLink($hospital_id, Request $request)
    {
        $setting = Setting::with('hospital')->where('hospital_id', $hospital_id)->first();

        if($setting != null) {
            if($setting->request_link != null){
                $data = array('request_link' => $setting->request_link, 'hospital' => $setting->hospital);

                $to_name = '';
                $to_email = $request->email;

                Mail::send('email_templates.send_link', $data, function($message) use($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('Request Generation');
                    $message->from('noreply@tynkerbox.com', 'TynkerBox');
                });

                if(count(Mail::failures()) > 0) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Error sending email'
                    ]);
                } else {
                    return response()->json([
                        'error'   => false,
                        'message' => 'Email sent successfully'
                    ]);
                }
            }
        } 

        return response()->json([
            'error' => true,
            'message' => 'Invalid request'
        ]);


    }
}
