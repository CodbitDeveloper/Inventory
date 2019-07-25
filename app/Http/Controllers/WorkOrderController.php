<?php

namespace App\Http\Controllers;

use App\User;
use App\WorkOrder;
use App\Hospital;
use App\Comment;

use Illuminate\Http\Request;
use Notification;
use App\Notifications\WorkOrderStatus;
use App\Notifications\AssignedToEngineer;
use Auth;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role == 'Admin') {  
            $work_orders = WorkOrder::where("hospital_id", $user->hospital_id)->with("priority", "user", "asset")->get();
            return view("work-orders", compact("work_orders", "user"));
        } elseif ($user->role == 'Regular Technician' || $user->role == 'Limited Technician') {
           $work_orders = WorkOrder::whereHas('users', function($q) use($user) {
               $q->where('additional_workers', $user->id);
           })->orWhere('assigned_to', $user->id)->with('priority', 'user', 'asset')->get();

           return view('work-orders', compact('work_orders', 'user'));
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

        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $hospital = Hospital::with("departments", "departments.units", "assets", 
            "fault_categories", "priorities", "services")->with(["users" => function($q){
                $q->where("role", "Admin")->orWhere("role", "Limited Technician")->orWhere("role", "Regular Technician");
            }])->where("id", $user->hospital_id)->first();
            return view('work-order-add', compact("hospital", "user"));
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
    public function store(Request $request, WorkOrder $work_order)
    {
        $request->validate([
            'title'       => 'required',
            'hospital_id' => 'required'
        ]);

        $workOrder = new WorkOrder();

        $workOrder->id                  = md5($request->title.microtime());
        $workOrder->title               = $request->title;
        $workOrder->description         = $request->description;
        $workOrder->due_date            = $request->due_date != null ?$date('Y-m-d', strtotime($request->due_date)) : null; 
        $workOrder->estimated_duration = $request->estimated_duration; 
        $workOrder->priority_id         = $request->priority_id;
        $workOrder->hospital_id         = $request->hospital_id;
        $workOrder->fault_category_id   = $request->fault_category_id;
        $workOrder->assigned_to         = $request->assigned_to;
        $workOrder->admin_id            = $request->admin_id;
        $workOrder->department_id       = $request->department_id;
        $workOrder->unit_id             = $request->unit_id;
        $workOrder->service_vendor_id   = $request->service_vendor_id;
        $workOrder->request_id          = $request->request_id;
        $work_order->cost               = $request->cost;
        $workOrder->asset_id            = $request->asset_id;

        if($workOrder->assigned_to != null){
            $workOrder->status = 4;
        }

        $last_wo_number = WorkOrder::where('hospital_id', Auth::user()->hospital_id)->latest()->first();
        if($last_wo_number == null) {
            $workOrder->wo_number = 1;
        } else {
            $workOrder->wo_number = $last_wo_number->wo_number + 1;
        }

        if($request->hasFile("image") != null) {
            $request->validate([
                'image' => 'mime:png,jpg,jpeg'
            ]);

            $file = $request->file('image');
            $name = md5($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path().'/img/assets/work_orders', $name);

            $workOrder->image = $name;
        }

        if($request->hasFile('fileName')) {
            $request->validate([
                'fileName' => 'mime:doc,pdf,docx,zip'
            ]);

            $file = $request->file('fileName');
            $name = time().'-'.md5($file->getClientOriginalName());
            $file->move('files/work_orders', $name);

            $workOrder->fileName = $name;
        }

        if($workOrder->assigned_to == null) {
            $work_order->pending();
        } else {
            $work_order->open();
        }

        if($workOrder->save()) {
            if($request->additionalWorkers != null) {
                $workOrder->teams()->attach($request->additionalWorkers);
            }

            if($request->authenticated_user != $request->assigned_to) {
                $user = User::where('id', $request->assigned_to)->first();
                $user->notify(new AssignedToEngineer($workOrder, 'You have been assigned as a lead technician for a new work order with number #'.$workOrder->wo_number.' ('.$workOrder->title.')'));
            }

            return response()->json([
                'error'      => false,
                'work_order' => $workOrder,
                'message'    => 'New work order created successfully!'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not create a new work order. Try Again!'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function show($workOrder)
    {
        $user = Auth::user();

        if($user->role == "Regular Technician" || $user->role == "Limited Technician"){
            $work_order = WorkOrder::with("user", "users", "priority", "asset", "purchase_orders", "fault_category")
            ->where("id", $workOrder)->where(function($query) use ($user){
                $query->whereHas("users", function($q) use($user){
                    $q->where('additional_workers', $user->id);
                })->orWhere("assigned_to", $user->id);
            })->first();
        }else if($user->role == "Admin"){
            $work_order = WorkOrder::with("user", "users", "priority", "asset", "purchase_orders", "fault_category")
            ->where("id", $workOrder)->first();
        }

        if($work_order == null){
            return abort(404);
        }

        $hospital = Hospital::with("parts", "priorities", "fault_categories")->where("id", $user->hospital_id)->first();
        return view("work-order-details", compact("work_order", "hospital", "user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkOrder $workOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        //
        $workOrder->title = $request->title;
        $workOrder->due_date = date('Y-m-d', strtotime($request->due_date));
        $workOrder->priority_id = $request->priority_id;
        $workOrder->fault_category_id = $request->fault_category_id;
        $workOrder->description = $request->description;
        
        if($request->assigned_to != null){
            $workOrder->assigned_to = $request->assigned_to;

            if($request->authenticated_user != $request->assigned_to){
                $user = User::where('id', $request->assigned_to)->first();
                $user->notify(new AssignedToEngineer($workOrder, 'You have been assigned as a lead technician for work order with number #'.$workOrder->wo_number.' ('.$workOrder->title.')'));
            }
        }

        if($workOrder->assigned_to != null && $workOrder->status == 5) {
            $workOrder->status = 4;
        }

        if($workOrder->update()){
            return response()->json([
                "error" => false,
                "message" => "Work order updated"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Could not update work order"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkOrder  $workOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrder $workOrder)
    {
        //
    }

    public function availableTechnicians($workOrder){
        $engineers = User::whereDoesntHave('work_order_teams', function($query) use ($workOrder){
            $query->where("work_order_id", $workOrder);
        })->whereDoesntHave('work_orders', function($query) use ($workOrder){
            $query->where("id", $workOrder);
        })->where(function($q){
            $q->where("role", 'LIKE', "%Technician%")->orWhere("role", "=", "Admin");
        })->get();

        return response()->json([
            "engineers" => $engineers
        ]);
    }

    public function assignTeam(WorkOrder $workOrder, Request $request){
        $request->validate([
            "user_ids" => "required"
        ]);

        $workOrder->users()->attach($request->user_ids);
        
        $users = User::whereIn("id", $request->user_ids)->where("id", "<>", $request->authenticated_user)->get();
        Notification::send($users, new AssignedToEngineer($workOrder));

        return response()->json([
            "error" => false,
            "message" => "Technicians assigned to work order"
        ]);
    }

    public function assignAsset(WorkOrder $workOrder, Request $request){
        $request->validate([
            "asset_id" => "required"
        ]);

        $workOrder->asset_id = $request->asset_id;
        
        if($workOrder->save()){
            return response()->json([
                "error" => false,
                "message" => "Asset assigned to work order"
            ]);
        }
        
        return response()->json([
            "error" => true,
            "message" => "Could not assign asset to work order"
        ]);
    }

    public function getActivities(WorkOrder $workOrder){
        $activities = $workOrder->user_messages()->latest()->get();
        return response()->json($activities);
    }

    public function recordActivity(WorkOrder $workOrder, Request $request){
        $request->validate([
            "user_id" => "required",
            "activity" => "required"
        ]);
        $workOrder->user_messages()->attach($request->user_id, ["action_taken" => $request->activity]);

        return response()->json([
            "error" => false,
            "message" => "Activity logged"
        ]);
    }

    public function comment(WorkOrder $workOrder, Request $request){
        $request->validate([
            "user_id" => "required",
            "comment" => "required"
        ]);
        $comment = new Comment();
        $comment->user_id = $request->user_id;
        $comment->comment = $request->comment;
        $comment->work_order_id = $workOrder->id;
        $comment->save();

        return response()->json([
            "error" => false,
            "comment" => $comment,
            "message" => "Comment added"
        ]);
    }

    public function getComments(WorkOrder $workOrder){
        return response()->json($workOrder->comments()->with("user")->get());
    }

    public function getSpareParts(WorkOrder $workOrder){
        return response()->json($workOrder->parts()->latest()->get());
    }

    public function attachSpareParts(WorkOrder $workOrder, Request $request){
        $request->validate([
            "part_id" => "required",
            "quantity" => "required"
        ]);

        $workOrder->parts()->attach($request->part_id, ["quantity" => $request->quantity]);

        return response()->json([
            "error" => false,
            "message" => "Part added"
        ]);
    }

    public function updateStatus(Workorder $workOrder, Request $request){
        $workOrder->status = $request->status;
        if($workOrder->update()){
            $users = User::where([['role', 'Admin'], ['hospital_id', $workOrder->hospital_id]])->get();
            switch($request->status){
                case 1:
                    Notification::send($users, new WorkOrderStatus($workOrder, "Status of work order #".$workOrder->wo_number.' ('.$workOrder->title.') has been marked as closed'));
                    break;
                case 2: 
                    Notification::send($users, new WorkOrderStatus($workOrder, "Status of work order #".$workOrder->wo_number.' ('.$workOrder->title.') has been marked as in progress'));
                    break;
                case 3: 
                    Notification::send($users, new WorkOrderStatus($workOrder, "Status of work order #".$workOrder->wo_number.' ('.$workOrder->title.') has been marked as hold'));
                    break;
                case 4: 
                    Notification::send($users, new WorkOrderStatus($workOrder, "Status of work order #".$workOrder->wo_number.' ('.$workOrder->title.') has been marked as opened'));
                    break;
                case 5: 
                    Notification::send($users, new WorkOrderStatus($workOrder, "Status of work order #".$workOrder->wo_number.' ('.$workOrder->title.') has been marked as pending'));
                    break;
                default: 
                    break;
            }

            return response()->json([
                'error' => false,
                'message' => "Work order status updated"
            ]);
        }
        return response()->json([
            'error' => true,
            "message" => "Could not update work order"
        ]);
    }
    
    public function complete(WorkOrder $workOrder, Request $request)
    {
        $request->validate([
            'approved_by' => 'required'
        ]);

        $workOrder->finish();
        $workOrder->approved_by = $request->approved_by;
        
        if($workOrder->save()){
            $users = User::whereHas('work_order_teams', function($q) use($workOrder){
                $q->where('work_order_id', $workOrder->id);
            })->orWhereHas('work_orders', function($query) use($workOrder) {
                $query->where('id', $workOrder->id);
            })->get();
            
            Notification::send($users, new WorkOrderStatus($workOrder));

            return response()->json([
                "error" => false,
                "message" => "Work order has been marked as completed"
            ]);
        }

        return response()->json([
            "error" => true,
            "message" => "Could not mark work order as completed"
        ]);
    }
}
