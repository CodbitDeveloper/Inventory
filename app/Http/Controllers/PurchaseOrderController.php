<?php

namespace App\Http\Controllers;

use App\PurchaseOrder;
use App\Hospital;
use App\User;
use App\Part;

use Mail;
use Notification;
use App\Notifications\PurchaseOrderStatus;

use App\OrderItem;
use Illuminate\Http\Request;

use PDF;

use Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $orders = PurchaseOrder::with("user", "order_items", "service_vendor")->where("hospital_id", $user->hospital_id)->get();
            return view("purchase-orders", compact("orders", "user"));
        } else {
            abort(403);
        }  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $hospital = Hospital::where("id", $user->hospital_id)->with("parts", "services")->first();
        $work_order = null;

        if($request->work_order != null){
            $work_order = $request->work_order;
        }
        return view("purchase-order-add", compact("hospital", "work_order", "user"));
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
            'title'             => 'required',
            'due_date'          => 'required',
            'service_vendor_id' => 'required',
            'added_by'          => 'required',
            'item_cost'         => 'required',
            'hospital_id'       => 'required',
        ]);

        $purchaseOrder = new PurchaseOrder();
        
        $purchaseOrder->id                = md5($request->title.microtime());
        $purchaseOrder->title             = $request->title;
        $purchaseOrder->service_vendor_id = $request->service_vendor_id;
        $purchaseOrder->added_by          = $request->added_by;
        $purchaseOrder->due_date          = date("Y-m-d", strtotime($request->due_date));
        $purchaseOrder->item_cost         = $request->item_cost;
        $purchaseOrder->sales_tax         = $request->sales_tax;
        $purchaseOrder->shipping_cost     = $request->shipping_cost;
        $purchaseOrder->other_cost        = $request->other_cost;
        $purchaseOrder->description       = $request->description;
        $purchaseOrder->shipping_method   = $request->shipping_method;
        $purchaseOrder->terms             = $request->terms;
        $purchaseOrder->notes             = $request->notes;
        $purchaseOrder->hospital_id       = $request->hospital_id;
        $purchaseOrder->hospital_name     = $request->hospital_name;
        $purchaseOrder->address           = $request->address;
        $purchaseOrder->contact_number    = $request->contact_number;
        $purchaseOrder->contact_name      = $request->contact_name;
        $purchaseOrder->work_order_id     = $request->work_order_id;
        $purchaseOrder->createLink();
 
        $last_po_number = PurchaseOrder::where('hospital_id', Auth::user()->hospital_id)->latest()->first();

        if($last_po_number == null) {
            $purchaseOrder->po_number = 1;
        } else {
            $purchaseOrder->po_number = $last_po_number->po_number + 1;
        }

        if(User::where([['id', $request->added_by], ['role', 'Hospital Head']])->first() != null) {
            $purchaseOrder->approve();
        } else {
            $purchaseOrder->status = 2;
        }

        if($purchaseOrder->save()) {
            if($request->orderItems != null) {
                $items = json_decode($request->orderItems, false);
                
                $orderItems = array();
                foreach($items as $item){
                    array_push($orderItems, array("purchase_order_id" => $purchaseOrder->id, 
                    "part_id" => $item->part_id, "quantity" => $item->quantity, "unit_cost" => $item->unit_cost, 
                    "part_name" => $item->name, "created_at" => date("Y-m-d H:i:s")));
                }
                OrderItem::insert($orderItems);
            }
            return response()->json([
                'error'   => false,
                'data'    => $purchaseOrder,
                'message' => 'Purchase order saved successfully!'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Could not save purchase order. Try Again!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show($purchaseOrder)
    {
        $user = Auth::user();

        $order = PurchaseOrder::with("service_vendor", "order_items")->where("id", $purchaseOrder)->first();
        $hospital = Hospital::where("id", $user->hospital_id)->with("parts", "services")->with(["users" => function($q){
            $q->where("role", "Hospital Head");
        }])->first();
        return view("purchase-details", compact("order", "hospital", "user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        //
        $purchaseOrder->title             = $request->title;
        $purchaseOrder->service_vendor_id = $request->service_vendor_id;
        $purchaseOrder->due_date          = date("Y-m-d", strtotime($request->due_date));
        $purchaseOrder->item_cost         = $request->item_cost;
        $purchaseOrder->sales_tax         = $request->sales_tax;
        $purchaseOrder->shipping_cost     = $request->shipping_cost;
        $purchaseOrder->other_cost        = $request->other_cost;
        $purchaseOrder->description       = $request->description;
        $purchaseOrder->shipping_method   = $request->shipping_method;
        $purchaseOrder->terms             = $request->terms;
        $purchaseOrder->notes             = $request->notes;
        $purchaseOrder->address           = $request->address;
        $purchaseOrder->contact_number    = $request->contact_number;
        $purchaseOrder->contact_name      = $request->contact_name;

        if($purchaseOrder->update()){
            $purchaseOrder->order_items()->delete();

            if($request->orderItems != null) {
                $items = json_decode($request->orderItems, false);
                
                $orderItems = array();
                foreach($items as $item){
                    array_push($orderItems, array("purchase_order_id" => $purchaseOrder->id, 
                    "part_id" => $item->part_id, "quantity" => $item->quantity, "unit_cost" => $item->unit_cost, 
                    "part_name" => $item->name, "created_at" => date("Y-m-d H:i:s")));
                }
                OrderItem::insert($orderItems);
            }

            return response()->json([
                'error'   => false,
                'data'    => $purchaseOrder,
                'message' => 'Purchase order updated successfully!'
            ]);
        }else{
            return response()->json([
                'error'   => true,
                'message' => 'Could not update purchase order. Try Again!'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * --------------------------------
     * Approve purchase order
     * --------------------------------
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function approve(PurchaseOrder $purchaseOrder, Request $request) 
    {
        $request->validate([
            'approved_by' => 'required'
        ]);

        $purchaseOrder->approve();

        $purchaseOrder->approved_by = $request->approved_by;
        
        if($purchaseOrder->save()){
            $user = User::where('id', $purchaseOrder->added_by)->first();
            $user->notify(new PurchaseOrderStatus($purchaseOrder));

            return response()->json([
                'error' => false,
                'message' => "Purchase order approved"
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => "Could not approve purchase order, try again!"
        ]);
    }

    
    /**
     * --------------------------------
     * Fulfill purchase order
     * --------------------------------
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function fulfill(Request $request, PurchaseOrder $purchaseOrder)
    {
        $order_items = $purchaseOrder->order_items()->get();

        $purchaseOrder->is_fulfilled = 1;

        if($purchaseOrder->save()){
            foreach($order_items as $item){
                if($item->part_id == null){
                    $part = new Part();
                    $part->name = $item->part_name;
                    $part->id = md5($part->part_name.microtime());
                    $part->quantity = $item->quantity;
                    $part->min_quantity = 0;
                    $part->hospital_id = $purchaseOrder->hospital_id;
                    $part->cost = $item->unit_cost;
                }else{
                    $part = Part::where('id', $item->part_id)->first();
                    $part->quantity += $item->quantity;
                }

                $part->save();
            }
            
            return response()->json([
                'error'   => false,
                'message' => 'Purchase order marked as fulfilled'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not mark purchase order as fulfilled'
        ]);

    }

    /**
     * --------------------------------
     * Decline purchase order
     * --------------------------------
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function decline(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->decline();

        if($purchaseOrder->save()) {
            $user = User::where('id', $purchaseOrder->added_by)->first();
            $user->notify(new PurchaseOrderStatus($purchaseOrder, 'Purchase Order with number #'.$purchaseOrder->po_number.' ('.$purchaseOrder->title.') has been declined'));
            
            return response()->json([
                'error'   => false,
                'message' => 'Purchase order declined'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not decline purchase order, try again!'
        ]);
    }

    
    /**
     * --------------------------------------
     * Send purchase order request URL (link)
     * --------------------------------------
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function sendLink(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $recipient = User::where('id', $request->user_id)->first();
        $sender = $purchaseOrder->user()->first();
        $count = $purchaseOrder->order_items()->count();
    
        $data = array('recipient' => $recipient, "sender" => $sender, "purchase_order" => $purchaseOrder, "notes" => $request->notes, "count" => $count);

        $to_name  = ucwords($recipient->firstname.' '.$recipient->lastname);
        $to_email = $recipient->email;

        Mail::send('email_templates.new_order', $data, function($message) use($to_name, $to_email){
            $message->to($to_email, $to_name)
                    ->subject('New Purchase Order Request');
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
                'message' => 'Email successfully sent'
            ]);
        }
    }

    /**
     * --------------------
     * Display approval
     * --------------------
     * 
     * @param $hash_link
     * @return view
     */
    public function approval($hash_link){
        $user = Auth::user();

        if($user->role != "Hospital Head"){
            return abort(403);
        }
        $order = PurchaseOrder::with("service_vendor", "order_items", "hospital")->where("hash_link", $hash_link)->first();

        if($order == null){
            return abort(404);
        }

        return view('purchase-approval', compact("order", "user"));
    }

    /**
     * ---------------------------------
     * Generate PDF for purchase order
     * ---------------------------------
     * 
     * @param $hashLink
     */
    public function generatePdf($hashLink){
        $user = Auth::user();
        $order = PurchaseOrder::with("service_vendor", "order_items", "hospital")->where("hash_link", $hashLink)->first();

        if($order == null){
            return abort(404);
        }
        
        if($user->role != "Hospital Head" && $order->approved_by != $user->id){
            return abort(403);
        }
        
        $pdf = PDF::loadView('purchase-report', compact("user", "order"));
        return $pdf->stream(microtime().'.pdf');
    }
}
 