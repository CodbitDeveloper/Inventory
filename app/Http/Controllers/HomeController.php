<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\WorkOrder;
use App\PmSchedule;

use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        return view('home', compact('user'));
    }

    /**
     * --------------------------------------------
     * Get upcoming preventive maintenance schedule
     * ---------------------------------------------
     * 
     * @return \Illuminate\Http\Response
     */
    public function loadUpcoming(Request $request){
        $request->validate([
            "hospital_id" => "required"
        ]);

        $pms = PmSchedule::select("id", "due_date", "title")->whereDate("due_date", ">=", date('Y-m-d'))->where("hospital_id", $request->hospital_id)->get();
        $work_orders = WorkOrder::select("id", "due_date", "title")->whereDate("due_date", ">=", date('Y-m-d'))->where("hospital_id", $request->hospital_id)->get();
        
        $data = $work_orders->merge($pms);
        return response()->json($data);
    }
}
