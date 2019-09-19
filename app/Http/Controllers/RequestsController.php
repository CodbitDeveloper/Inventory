<?php

namespace App\Http\Controllers;

use App\Requests;
use App\WorkOrder;
use App\User;
use App\Setting;
use Auth;
use Mail;
use Notification;
use App\Notifications\RequestReceived;
use App\Notifications\RequestStatus;
use App\Notifications\AssignedToEngineer;
use Illuminate\Http\Request;
use App\Hospital;
class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role == 'Admin'){
            $requests = Requests::where('hospital_id', $user->hospital_id)->with("priority", "user")->get();
            return view('requests', compact("requests", "user"));
        } elseif ($user->role == 'Regular Technician' || $user->role == 'Limited Technician' || $user->role == 'View Only' || $user->role == 'Hospital Head') {
            $requests = Requests::where([['hospital_id', $user->hospital_id], ['requested_by', $user->id]])->with("priority", "user")->get();
            return view('requests', compact("requests", "user"));
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
        $user = Auth::user();

        $hospital = Hospital::where('id', $user->hospital_id)->with("priorities", "departments", "departments.units", "assets")->first();
        return view('request-add', compact("hospital", "user"));
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
            'title'       => 'required',
            'hospital_id' => 'required'
        ]);

        $work_request  = new Requests();
        
        $work_request->title         = $request->title;
        $work_request->id            = md5(microtime().$request->title);
        $work_request->description   = $request->description;
        $work_request->priority_id   = $request->priority_id;
        $work_request->department_id = $request->department_id;
        $work_request->unit_id       = $request->unit_id;
        $work_request->asset_id      = $request->asset_id;
        $work_request->hospital_id   = $request->hospital_id;

        if($request->requested_by != null) {
            $work_request->requested_by = $request->requested_by;
        } else {
            $work_request->requester_name   = $request->requester_name;
            $work_request->requester_number = $request->requester_number;
            $work_request->requester_email  = $request->requester_email;
        }

        if($request->image != null) {
            $request->validate([
                'image'   => 'mimes:png,jpg,jpeg'
            ]);

            $file = $request->file('image');

            $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path().'/img/assets/request/', $name);
            
            $work_request->image = $name;
         }

         if($request->hasFile('fileName')) {
             $request->validate([
                'fileName'   => 'required|mimes:doc,pdf,docx,zip'
             ]);
            $file = $request->file('fileName');
            $name = $file->getClientOriginalName();
            $name = time(). '-' . $name;
            $file->move('files', $name);
            $work_request->fileName = $name;
         }

         if($work_request->save()) {
             $user = User::where([['role', 'Admin'], ['hospital_id', $request->hospital_id], ['id', '<>', $request->requested_by]])->first();
             if($request->requested_by != null){
                 $requester = $work_request->user()->first();
                 $name_of_requester = $requester->firstname.' '.$requester->lastname;
             }else{
                $name_of_requester = $work_request->requester_name;
             }

             $work_request->name_of_requester = $name_of_requester;

             if($user != null) {
                $user->notify(new RequestReceived($work_request)); 
             }
            
            return response()->json([
                'error' => false,
                'data' => $work_request,
                'message' => 'Request created successfully'
            ]);
         }

        return response()->json([
            'error' => true,
            'message' => 'Error creating request'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Requests  $requests
     * @return \Illuminate\Http\Response
     */
    public function show($request)
    {
        $user = Auth::user();

        $request = Requests::where('id', $request)->with("priority", "user", "asset", "unit", "department")->first();
        $hospital = Hospital::where("id", $user->hospital_id)->with("priorities", "assets", "departments", "departments.units")->first();
        return view("request-details", compact("request", "hospital", "user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Requests  $requests
     * @return \Illuminate\Http\Response
     */
    public function edit(Requests $requests)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Requests  $requests
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Requests $work_request)
    {
        //
        $work_request->title         = $request->title;
        $work_request->description   = $request->description;
        $work_request->priority_id   = $request->priority_id;
        $work_request->department_id = $request->department_id;
        $work_request->unit_id       = $request->unit_id;
        $work_request->asset_id      = $request->asset_id;

        if($work_request->update()){
            return response()->json([
                "error" => false,
                "message" => "Request successfully updated"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Could not update the request"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Requests  $requests
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requests $requests)
    {
        //
    }

    /**
     * --------------------------------------
     * Approve work order request
     * --------------------------------------
     * 
     * @param  \App\Requests  $work_request
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function approve(Requests $work_request, Request $request)
    {
        $request->validate([
            'approved_by' => 'required'
        ]);
        $work_request->approve();

        $work_request->response = $request->response;
        $work_request->reason = null;
        $work_request->approved_by = $request->approved_by;

        if($work_request->save()) {
            $last_order = WorkOrder::where("hospital_id", $work_request->hospital_id)->latest()->first();
            $work_order = $work_request->toWorkOrder();

            
            if($last_order == null){
             $work_order->wo_number = 1;   
            }else{
                $work_order->wo_number = $last_order->wo_number + 1;
            }
            $work_order->user_admin = $request->user_admin;
            $work_order->due_date = null;

            
            if($work_order->save()) {
                if($work_request->requested_by == null) {
                    $to_name = ucwords($work_request->requester_name);
                    $to_email = $work_request->requester_email;

                    Mail::send('email_templates.request_accepted', array("request" => $work_request), function($message) use($to_name, $to_email) {
                        $message->to($to_email, $to_name)
                                ->subject('Work order request accepted');
                        $message->from('noreply@tynkerbox.com', 'TynkerBox');
                    });
        
                    if(count(Mail::failures()) > 0) {
                        return response()->json([
                            'error' => false,
                            "work_order" => $work_order,
                            'message' => 'Request saved. Could not notify the requester.'
                        ]);
                    }
                } else {
                    $requester = $work_request->user()->first();
                    //function to notify requester
                    if($requester->role != 'Admin') {
                        $requester->notify(new RequestStatus($work_request));
                    }
                }
    
                return response()->json([
                    'error' => false,
                    "work_order" => $work_order,
                    'message' => 'Request accepted. A new work order has been generated.'
                ]);
            }
            
            return response()->json([
                'error'   => false,
                'message' => 'Request approved. Could not create a work order. Create a new work order.'
            ]);
        } 

        return response()->json([
            'error'   => true,
            'message' => 'Could not approve work order request. Try Again!'
        ]);
    }

    /**
     * --------------------------------------
     * Decline work order request
     * --------------------------------------
     * 
     * @param  \App\Requests  $work_request
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function decline(Requests $work_request, Request $request)
    {
        $work_request->decline();

        $work_request->reason = $request->reason;

        if($work_request->save()) {

            if($work_request->requested_by == null){
                $to_name = $work_request->requester_name;
                $to_email = $work_request->requester_email;
            
                Mail::send('email_templates.request_declined', array("request" => $work_request), function($message) use($to_name, $to_email){
                    $message->to($to_email, $to_name)
                            ->subject("Work order request declined");
                    $message->from('noreply@tynkerbox.com', 'TynkerBox');
                });
    
                if(count(Mail::failures()) > 0) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Could not send the mail. Try again!'
                    ]);
                }
            }else{
                $requester = $work_request->user()->first();
                //notify the user;
                if($requester->role != 'Admin') {
                    $requester->notify(new RequestStatus($work_request, 'Work order request had been declined'));
                }
            }

            return response()->json([
                'error'   => false,
                'message' => 'Work order request declined'
            ]);
        } 

        return response()->json([
            'error'   => true,
            'message' => 'Could not decline work order request. Try Again!'
        ]);
    }

    public function guestRequest($request_link){
        $hospital = Hospital::whereHas("setting", function($q) use ($request_link){
            $q->where("request_link", $request_link);
        })->with("priorities", "setting")->first();

        if($hospital == null){
            return abort(404);
        }

        return view("request-guest", compact("hospital"));
    }

    private function formatDate($date){
        $date = str_replace(',', '', $date);
        $date = str_replace('-', '/', $date);
        return date("Y-m-d H:i:s", strtotime(stripslashes($date)));
    }

    /**
     * --------------------------------------
     * Check request link from guest
     * --------------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guestAdd(Request $request){
        $request->validate([
            "request_link" => "required",
            "hospital_id" => "required"
        ]);
        
        $setting = Setting::where("hospital_id", $request->hospital_id)->first();
        
        if($setting->request_link == $request->request_link){
            return $this->store($request);
        }

        return response('Unauthenticated',401);
    }
}
