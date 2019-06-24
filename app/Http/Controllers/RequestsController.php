<?php

namespace App\Http\Controllers;

use App\Requests;
use App\Admin;
use App\User;
use App\Unit;
use App\Department;
use App\WorkOrder;
use Auth;
use DB;
use App\Notifications\RequestReceived;
use App\Notifications\RequestAssigned;
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
        $requests = Requests::where('hospital_id', Auth::user()->hospital_id)->with("priority", "user")->paginate(10);
        return view('requests', compact("requests"));
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
            'title'       => 'required',
            'hospital_id' => 'required'
        ]);

        $work_request  = new Requests();
        
        $work_request->title         = $request->title;
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
                'fileName'   => 'required|mime:doc,pdf,docx,zip'
             ]);
            $file = $request->file('fileName');
            $name = $file->getClientOriginalName();
            $name = time(). '-' . $name;
            $file->move('files/request_files', $name);
            $work_request->fileName = $name;
         }

         if($work_request->save()) {
             if(User::where([['role', 'Admin'], ['id', $request->requested_by]])->first() == null) {
                $user = User::where([['role', 'Admin'], ['hospital_id', $request->hospital_id]])->first();
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
    public function show(Requests $requests)
    {
        //
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
    public function update(Request $request, Requests $requests)
    {
        //
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

    public function approve(Requests $work_request, Request $request)
    {
        $work_request->approve();

        $work_request->response = $request->response;

        if($work_request->save()) {
            $work_order = new WorkOrder();

            $work_order->id                 = md5($request->title.microtime());
            $work_order->title              = $request->title;
            $work_order->description        = $request->description;
            $work_order->due_date           = date('Y-m-d', strtotime($request->due_date));
            $work_order->frequency          = $request->frequency;
            $work_order->estimated_duration = $request->estimated_duration;
            $work_order->priority_id        = $request->priority_id;
            $work_order->hospital_id        = $request->hospital_id;
            $work_order->fault_category_id  = $request->fault_category_id;
            $work_order->assigned_to        = $request->assigned_to;
            $work_order->admin_id           = $request->admin_id;
            $work_order->department_id      = $request->department_id;
            $work_order->unit_id            = $request->unit_id;
            $work_order->service_vendor_id  = $request->service_vendor_id;
            $work_order->request_id         = $request->request_id;

            $last_wo_number = WorkOrder::where('hospital_id', Auth::user()->hospital_id)->latest()->get();

            if($last_wo_number == null) {
                $work_order->wo_number = 1;
            } else {
                $work_order->wo_number = $last_wo_number->wo_number + 1;
            }

            if($request->image != null) {
                $request->validate([
                    'image' => 'required|mime:png,jpg,jpeg'
                ]);

                $file = $request->file('image');
                $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/img/assets/work_orders', $name);

                $work_order->image = $name;
            }

            if($request->hasFile('fileName')) {
                $request->validate([
                    'fileName' => 'required|mime:doc,pdf,docx,zip'
                ]);

                $file = $request->file('fileName');

                $name = time().'-'.md5($file->getClientOriginalName());
                $file->move('files/work_orders', $name);

                $work_order->fileName = $name;
            }

            if($work_order->save()) {
                return response()->json([
                    'error'      => false,
                    'work_order' => $work_order,
                    'message'    => 'New work order created successfully!'
                ]);
            } else {
                return response()->json([
                    'error'   => true,
                    'message' => 'Could not create a new work order. Try Again!' 
                ]);
            }
            return response()->json([
                'error'   => false,
                'message' => 'Work order request approved'
            ]);
        } 

        return response()->json([
            'error'   => true,
            'message' => 'Could not approve work order request. Try Again!'
        ]);
    }

    public function decline(Requests $work_request, Request $request)
    {
        $work_request->decline();

        $work_request->reason = $request->reason;

        if($work_request->save()) {
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

    /*public function adminIndex(){
        $requests = Requests::with('equipments', 'equipments.hospital')->whereHas('equipments', function($q){
            $q->whereHas('hospital', function($qr){
                $qr->whereHas('district', function($qry){
                    $qry->where('region_id', '=', Auth::guard('admin')->user()->region_id);
                });
            });
        })->orderBy('is_checked', 'desc')->get();
        $engineers = Admin::where('role', '=', 'Biomedical Engineer')->get();
        return view('admin.requests')->with('requests', $requests)->with('engineers', $engineers);
    }*/

    public function assign(Request $request){

        $request->validate([
            'id' => 'required',
            'assigned_to' => 'required',
            'scheduled_for' => 'required',
            'hospital_id' => 'required'
        ]);

        /*$requests = Requests::find($request->id)->first();
        $requests->assigned_to = $request->assigned_to;
        $requests->scheduled_for = $this->formatDate($request->scheduled_for);

        $status = $requests->save();*/
        $request->scheduled_for = $this->formatDate($request->scheduled_for);
        $status = DB::table('requests')->where('id', $request->id)->update(['assigned_to' => $request->assigned_to, 'scheduled_for' => $request->scheduled_for, 'is_checked' => 1]);

        if($status){
            $temp = new Requests;
            $temp->id = $request->id;
            $temp->assigned_to = $request->assigned_to;
            $temp->scheduled_for = $request->scheduled_for;


            $admin = Admin::where('id', $request->assigned_to)->first();

            $admin->notify(new AssignedToEngineer($temp));   

            $user = User::where([['hospital_id', '=', $request->hospital_id], ['role', '=', 'Admin']])->first();

            $user->notify(new RequestAssigned($temp));
        }

        return response()->json([
            'error' => !$status,
            'message' => $status ? 'Request updated' : 'Could not update request'
        ]);
    }

    public function presentEngineerJobs(){
        $requests = Requests::where('assigned_to', '=', Auth::guard('admin')->user()->id)->with('equipments', 'equipments.hospital')->get();
        return view('admin.engineer-requests')->with('requests', $requests);
    }

    public function handleMaintenance($equipment, $job){
        $requests = Requests::where('id', '=', $job)->first();
        $hospital = Equipment::with('unit', 'unit.department')->where('code', '=', $equipment)->first();
        $hospital = $hospital->unit->department->hospital_id;
        return view('admin.maintenance-form')->with('equipment', $equipment)->with('requests', $requests)->with('hospital', $hospital);
    }

    private function formatDate($date){
        $date = str_replace(',', '', $date);
        $date = str_replace('-', '/', $date);
        return date("Y-m-d H:i:s", strtotime(stripslashes($date)));
    }
}
