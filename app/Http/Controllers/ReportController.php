<?php

namespace App\Http\Controllers;
use DB;
use Auth;

use App\WorkOrder;
use App\PreventiveMaintenance;
use App\Asset;
use App\User;

use Carbon\CarbonPeriod;
use Carbon\Carbon;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * ---------------------
     * Dsiplay report
     * ---------------------
     * 
     * @return view
     */
    public function index(){ 
        $user = Auth::user();
        if($user->role == 'Admin' || $user->role == 'Regular Technician' || $user->role == 'Hospital Head') {
            return view('reports', compact("user"));
        } else {
            abort(403);
        }
    }

    /**
     * 
     */
    public function workOrderIndex(Request $request){
        $request->validate([
            "hospital_id" => "required"
        ]);

        $work_orders = WorkOrder::select("status", DB::raw("COUNT(id) as kount"))->where("hospital_id", $request->hospital_id)->groupBy("status")->get();
        
        $labels = ["Pending", "Open", "In progress", "On hold", "Closed"];
        $data = [0, 0, 0, 0, 0];

        foreach($work_orders as $work_order){
            switch($work_order->status){
                case 5:
                    $data[0] = $work_order->kount;
                    break;
                case 4:
                    $data[1] = $work_order->kount;
                    break;
                case 3:
                    $data[2] = $work_order->kount;
                    break;
                case 2:
                    $data[3] = $work_order->kount;
                    break;
                case 1:
                    $data[4] = $work_order->kount;
                    break;
                default: 
                    break;
            }
        }

        $datasets = array(array("name" => "Work orders", "data" => $data));

        return response()->json([
            "labels" => $labels,
            "datasets" => $datasets
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function workOrderByStatus(Request $request){
        $request->validate([
            "type" => "required",
            "hospital_id" => "required"
        ]);

        if($request->type == "cost"){
            $quantifier = "SUM(cost) as cost";
        }else{
            $quantifier = "COUNT(id) as kount";
        }
        
        
        if($request->interval == null){
           $statuses = ["Closed", "On hold", "In progress", "Open", "Pending"];
           $results = WorkOrder::select("status", DB::raw($quantifier))->where("hospital_id", $request->hospital_id)->groupBy("status")->get();
           $data = [0,0,0,0,0]; 
           
           foreach($results as $result){
                switch($result->status){
                    case 5:
                        $data[4] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 4:
                        $data[3] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 3:
                        $data[2] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 2:
                        $data[1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $data[0] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $statuses,
                "datasets" => array(array("name" => "Work orders", "data" => $data)),
                "timespan" => "all time",
                "type" => ""
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "month"){
            $request->validate([
                "date" => "required"
            ]);

            $results = WorkOrder::select("status", DB::raw($quantifier.', MONTH(created_at) as month'))
            ->whereYear("created_at", "=", $request->date)->where("hospital_id", $request->hospital_id)->groupBy("status", "month")->get();

            
            $labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $open = $pending = $in_progress = $on_hold = $closed = [0,0,0,0,0,0,0,0,0,0,0,0];

            foreach($results as $result){
                switch($result->status){
                    case 5:
                        $pending[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 4:
                        $open[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 3:
                        $in_progress[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 2:
                        $on_hold[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $closed[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Closed" , "data" => $closed),
                    array("name" => "On hold" , "data" => $on_hold),
                    array("name" => "In progress" , "data" => $in_progress),
                    array("name" => "Open" , "data" => $open),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $request->date,
                "type" => "Monthly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
            
        }else if($request->interval == "year"){
            
            $from = date('Y-m-d', strtotime($request->from));
            $to = date('Y-m-d', strtotime($request->to));
            
            
            $results = WorkOrder::select("status", DB::raw($quantifier.', DATE_FORMAT(created_at, "%Y") as year'))
            ->whereDate("created_at", ">=", $from)->whereDate("created_at", "<=", $to)->where("hospital_id", $request->hosptial_id)->groupBy("status", "year")->get();
            
            $labels = $this->yearsBetween($from, $to);
            $open = $pending = $in_progress = $on_hold = $closed = array();

            foreach($labels as $key => $label){
                $open[$key] = $pending[$key] = $in_progress[$key] = $on_hold[$key] = $closed[$key] = 0;
                foreach($results as $result){
                    if($result->year == $label){
                        switch($result->status){
                            case 5:
                                $pending[$key] = $result->kount != null ? $result->kount : $result->cost;
                                break;
                            case 4:
                                $open[$key] = $result->kount != null ? $result->kount : $result->cost;
                                break;
                            case 3:
                                $in_progress[$key] = $result->kount != null ? $result->kount : $result->cost;
                                break;
                            case 2:
                                $on_hold[$key] = $result->kount != null ? $result->kount : $result->cost;
                                break;
                            case 1:
                                $closed[$key] = $result->kount != null ? $result->kount : $result->cost;
                                break;
                            default: 
                                break;
                        }
                    }
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Closed" , "data" => $closed),
                    array("name" => "On hold" , "data" => $on_hold),
                    array("name" => "In progress" , "data" => $in_progress),
                    array("name" => "Open" , "data" => $open),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $from.' to '.$to,
                "type" => "Yearly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }else if($request->interval == "quarter"){
            $results = WorkOrder::select("status", DB::raw($quantifier.', QUARTER(created_at) as quarter'))
            ->whereYear("created_at", "=", $request->date)->where("hospital_id", $request->hospital_id)->groupBy("status", "quarter")->get();

            $labels = ["January - March ".$request->date, "April - June ".$request->date, "July - September ".$request->date, "October - December ".$request->date];
            $open = $pending = $in_progress = $on_hold = $closed = [0,0,0,0];
            
            foreach($results as $result){
                switch($result->status){
                    case 5:
                        $pending[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 4:
                        $open[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 3:
                        $in_progress[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 2:
                        $on_hold[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $closed[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                    default: 
                        break;
                }
            }

            $labels = array_unique($labels);
            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Closed" , "data" => $closed),
                    array("name" => "On hold" , "data" => $on_hold),
                    array("name" => "In progress" , "data" => $in_progress),
                    array("name" => "Open" , "data" => $open),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $request->date,
                "type" => "Quarterly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }else if($request->interval == "daily"){
            $request->validate([
                "date" => "required"
            ]);

            $date = $request->date;
            

            $daysInMonth = Carbon::parse($date)->daysInMonth;

            $results = WorkOrder::select("status", DB::raw($quantifier.', DAY(created_at) as day'))
            ->whereRaw(DB::raw("DATE_FORMAT(created_at, '%M %Y') = '$date'"))->where("hospital_id", $request->hospital_id)->groupBy("status", "day")->get();

            $labels = $this->createArrayOfDays($daysInMonth);
            $open = $pending = $in_progress = $on_hold = $closed = $this->createNEmptyArray($daysInMonth);

            foreach($results as $result){
                switch($result->status){
                    case 5:
                        $pending[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 4:
                        $open[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 3:
                        $in_progress[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 2:
                        $on_hold[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $closed[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Closed", "data" => $closed),
                    array("name" => "On hold" , "data" => $on_hold),
                    array("name" => "In progress" , "data" => $in_progress),
                    array("name" => "Open" , "data" => $open),
                    array("name" => "Pending", "data" => $pending)
                ),
                "timespan" => $request->date,
                "type" => "Daily"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
        
    }

    public function workOrderByDepartment(Request $request){
        $request->validate([
            "type" => "required",
            "hospital_id" => "required"
        ]);

        if($request->type == "cost"){
            $quantifier = "SUM(cost) as cost";
        }else{
            $quantifier = "COUNT(id) as kount";
        }
        
        
        if($request->interval == null){
           $results = WorkOrder::select("department_id", DB::raw($quantifier))->with("department")->where("hospital_id", $request->hospital_id)->groupBy("department_id")->get();
           
           $labels = array();
           $data = array();
           $total = 0;

           foreach($results as $result){
                
                if($result->department == null){
                    $labels[] = "N/A";
                }else{
                    $labels[] = $result->department->name;
                }

                $data[] = $result->kount != null ? $result->kount : $result->cost;
                $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(array("name" => "Work orders", "data" => $data)),
                "timespan" => "all time",
                "type" => "",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "month"){
            $request->validate([
                "date" => "required"
            ]);

            $results = WorkOrder::select("department_id", DB::raw($quantifier.', MONTH(created_at) as mth'))->with("department")
            ->whereYear("created_at", "=", $request->date)->where("hospital_id", $request->hospital_id)->groupBy("department_id", "mth")->get();
            
            $labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $departments = array_keys($results->groupBy("department_id")->toArray());
            
            $data = array();
            $total = 0;

            foreach($departments as $department){
                $temp = array(0,0,0,0,0,0,0,0,0,0,0);
                
                foreach($results as $result){

                    if($result->department_id == null){
                        $result->department_id = "";
                    }else if($result->department_id == $department){
                        $tag = $result->department->name;
                    }

                    if($result->department_id == $department){
                        $temp[$result->mth - 1] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($department == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Monthly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
            
        }else if($request->interval == "year"){
            
            $from = date('Y-m-d', strtotime($request->from));
            $to = date('Y-m-d', strtotime($request->to));
            
            
            $results = WorkOrder::select("department_id", DB::raw($quantifier.', YEAR(created_at) as year'))->with("department")
            ->whereDate("created_at", ">=", $from)->whereDate("created_at", "<=", $to)->where("hospital_id", $request->hospital_id)->groupBy("department_id", "year")->get();
            
            $labels = $this->yearsBetween($from, $to);
            $start = $labels[0];
            $blueprint = $this->createNEmptyArray(count($labels));

            $departments = array_keys($results->groupBy("department_id")->toArray());

            $data = array();
            $total = 0;

            foreach($departments as $department){
                $temp = $blueprint;
                
                foreach($results as $result){
                    $index = $result->year - $start;

                    if($result->department_id == null){
                        $result->department_id = "";
                    }else if($result->department_id == $department){
                        $tag = $result->department->name;
                    }

                    if($result->department_id == $department){
                        $temp[$index] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($department == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $from.' to '.$to,
                "type" => "Year",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "quarter"){
            $results = WorkOrder::select("department_id", DB::raw($quantifier.', QUARTER(created_at) as quarter'))->with("department")
            ->whereYear("created_at", "=", $request->date)->where("hospital_id", $request->hospital_id)->groupBy("department_id", "quarter")->get();

            $labels = ["January - March ".$request->date, "April - June ".$request->date, "July - September ".$request->date, "October - December ".$request->date];
            $departments = array_keys($results->groupBy("department_id")->toArray());
            
            $data = array();
            $total = 0;
            
            foreach($departments as $department){
                $temp = [0, 0, 0, 0];

                foreach($results as $result){
                    if($result->department_id == null){
                        $result->department_id = "";
                    }else if($result->department_id == $department){
                        $tag = $result->department->name;
                    }

                    if($result->department_id == $department){
                        $temp[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($department == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            $labels = array_unique($labels);
            
            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Quarterly",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "daily"){
            $request->validate([
                "date" => "required"
            ]);

            $date = $request->date;

            $results = WorkOrder::select("department_id", DB::raw($quantifier.', DAY(created_at) as day'))->with("department")
            ->whereRaw(DB::raw("DATE_FORMAT(created_at, '%M %Y') = '$date'"))->where("hospital_id", $request->hospital_id)->groupBy("department_id", "day")->get();

            $daysInMonth = Carbon::parse($date)->daysInMonth;
            
            $blueprint = $this->createNEmptyArray($daysInMonth);
            $labels = $this->createArrayOfDays($daysInMonth);
            $departments = array_keys($results->groupBy("department_id")->toArray());

            $data = array();
            $total = 0;

            foreach($departments as $department){
                $temp = $blueprint;
                $tag = "";

                foreach($results as $result){
                    if($result->department_id == null){
                        $result->department_id = "";
                    }else if($result->department_id == $department){
                        $tag = $result->department->name;
                    }

                    if($result->department_id == $department){
                        $temp[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($department == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Daily",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
    }

    public function workOrderByUnit(Request $request){
        $request->validate([
            "type" => "required",
            "hospital_id" => "required"
        ]);

        if($request->type == "cost"){
            $quantifier = "SUM(cost) as cost";
        }else{
            $quantifier = "COUNT(id) as kount";
        }
        
        
        if($request->interval == null){
           $results = WorkOrder::select("unit_id", DB::raw($quantifier))->where("hospital_id", $request->hospital_id)->with("unit")->groupBy("unit_id")->get();
           
           $labels = array();
           $data = array();
           $total = 0;

           foreach($results as $result){
                
                if($result->unit == null){
                    $labels[] = "N/A";
                }else{
                    $labels[] = $result->unit->name;
                }

                $data[] = $result->kount != null ? $result->kount : $result->cost;
                $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(array("name" => "Work orders", "data" => $data)),
                "timespan" => "all time",
                "type" => "",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "month"){
            $request->validate([
                "date" => "required"
            ]);

            $results = WorkOrder::select("unit_id", DB::raw($quantifier.', MONTH(created_at) as mth'))->with("unit")
            ->whereYear("created_at", "=", $request->date)->where("hospital_id", $request->hospital_id)->groupBy("unit_id", "mth")->get();
            
            $labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $units = array_keys($results->groupBy("unit_id")->toArray());
            
            $data = array();
            $total = 0;

            foreach($units as $unit){
                $temp = array(0,0,0,0,0,0,0,0,0,0,0);
                $tag = "";

                foreach($results as $result){

                    if($result->unit_id == null){
                        $result->unit_id = "";
                    }else if($result->unit_id == $unit && $tag == ""){
                        $tag = $result->unit->name;
                    }

                    if($result->unit_id == $unit){
                        $temp[$result->mth - 1] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($unit == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Monthly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
            
        }else if($request->interval == "year"){
            
            $from = date('Y-m-d', strtotime($request->from));
            $to = date('Y-m-d', strtotime($request->to));
            
            
            $results = WorkOrder::select("unit_id", DB::raw($quantifier.', YEAR(created_at) as year'))->with("unit")
            ->whereDate("created_at", ">=", $from)->whereDate("created_at", "<=", $to)->where("hospital_id", $request->hospital_id)->groupBy("unit_id", "year")->get();
            
            $labels = $this->yearsBetween($from, $to);
            $start = $labels[0];
            $blueprint = $this->createNEmptyArray(count($labels));

            $units = array_keys($results->groupBy("unit_id")->toArray());

            $data = array();
            $total = 0;

            foreach($units as $unit){
                $temp = $blueprint;
                
                foreach($results as $result){
                    $index = $result->year - $start;

                    if($result->unit_id == null){
                        $result->unit_id = "";
                    }else if($result->unit_id == $unit){
                        $tag = $result->unit->name;
                    }

                    if($result->unit_id == $unit){
                        $temp[$index] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($unit == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Year",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "quarter"){
            $results = WorkOrder::select("unit_id", DB::raw($quantifier.', QUARTER(created_at) as quarter'))->with("unit")
            ->whereYear("created_at", "=", $request->date)->where("hospital_id", $request->hospital_id)->groupBy("unit_id", "quarter")->get();

            $labels = ["January - March ".$request->date, "April - June ".$request->date, "July - September ".$request->date, "October - December ".$request->date];
            $units = array_keys($results->groupBy("unit_id")->toArray());
            
            $data = array();
            $total = 0;
            
            foreach($units as $unit){
                $temp = [0, 0, 0, 0];

                foreach($results as $result){
                    if($result->unit_id == null){
                        $result->unit_id = "";
                    }else if($result->unit_id == $unit){
                        $tag = $result->unit->name;
                    }

                    if($result->unit_id == $unit){
                        $temp[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($unit == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            $labels = array_unique($labels);
            
            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Quarterly",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "daily"){
            $request->validate([
                "date" => "required"
            ]);

            $date = $request->date;

            $results = WorkOrder::select("unit_id", DB::raw($quantifier.', DAY(created_at) as day'))->with("unit")
            ->whereRaw(DB::raw("DATE_FORMAT(created_at, '%M %Y') = '$date'"))->where("hospital_id", $request->hospital_id)->groupBy("unit_id", "day")->get();

            $daysInMonth = Carbon::parse($date)->daysInMonth;
            
            $blueprint = $this->createNEmptyArray($daysInMonth);
            $labels = $this->createArrayOfDays($daysInMonth);
            $units = array_keys($results->groupBy("unit_id")->toArray());

            $data = array();
            $total = 0;

            foreach($units as $unit){
                $temp = $blueprint;
                
                foreach($results as $result){
                    if($result->unit_id == null){
                        $result->unit_id = "";
                    }else if($result->unit_id == $unit){
                        $tag = $result->unit->name;
                    }

                    if($result->unit_id == $unit){
                        $temp[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        $total = $result->kount != null ? $total + $result->kount : $total + $result->cost;
                    }
                }

                if($unit == ""){
                    $tag = "N/A";
                }
                
                $data[] = array("name" => $tag, "type" => "line", "data" => $temp);
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Daily",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
    }

    public function workOrderByApproval(Request $request){
        $request->validate([
            "type" => "required"
        ]);

        if($request->type == "cost"){
            $quantifier = "SUM(cost) as cost";
        }else{
            $quantifier = "COUNT(id) as kount";
        }
        
        
        if($request->interval == null){
           $statuses = [ "Pending", "Approved" ];
           $results = WorkOrder::select("is_complete", DB::raw($quantifier))->groupBy("is_complete")->get();
           $data = [0,0]; 
           
           foreach($results as $result){
                switch($result->status){
                    case 0:
                        $data[0] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $data[1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                }
            }

            return response()->json([
                "labels" => $statuses,
                "datasets" => array(array("name" => "Work orders", "data" => $data)),
                "timespan" => "all time",
                "type" => ""
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);

        }else if($request->interval == "month"){
            $request->validate([
                "date" => "required"
            ]);

            $results = WorkOrder::select("is_complete", DB::raw($quantifier.', MONTH(created_at) as month'))
            ->whereYear("created_at", "=", $request->date)->groupBy("is_complete", "month")->get();

            
            $labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $approved = $pending = [0,0,0,0,0,0,0,0,0,0,0,0];

            foreach($results as $result){
                switch($result->is_complete){
                    case 0:
                        $pending[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $approved[$result->month - 1] = $result->kount != null ? $result->kount : $result->cost;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Approved" , "data" => $approved),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $request->date,
                "type" => "Monthly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
            
        }else if($request->interval == "year"){
            
            $from = date('Y-m-d', strtotime($request->from));
            $to = date('Y-m-d', strtotime($request->to));
            
            
            $results = WorkOrder::select("is_complete", DB::raw($quantifier.', YEAR(created_at) as year'))
            ->whereDate("created_at", ">=", $from)->whereDate("created_at", "<=", $to)->groupBy("status", "year")->get();
            
            $labels = $this->yearsBetween($from, $to);
            $start = $labels[0];

            $approved = $pending = $this->createNEmptyArray(count($labels));

            foreach($results as $result){
                $index = $result->year - $start;

                switch($result->status){
                    case 0:
                        $pending[$index] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $approved[$index] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Approved" , "data" => $approved),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $from.' to '.$to,
                "type" => "Yearly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }else if($request->interval == "quarter"){
            $results = WorkOrder::select("is_complete", DB::raw($quantifier.', QUARTER(created_at) as quarter'))
            ->whereYear("created_at", "=", $request->date)->groupBy("is_complete", "quarter")->get();

            $labels = ["January - March ".$request->date, "April - June ".$request->date, "July - September ".$request->date, "October - December ".$request->date];
            $approved = $pending = [0,0,0,0];
            
            foreach($results as $result){
                switch($result->is_complete){
                    case 0:
                        $pending[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $open[$result->quarter - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                }
            }

            $labels = array_unique($labels);
            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Approved" , "data" => $approved),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $request->date,
                "type" => "Quarterly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }else if($request->interval == "daily"){
            $request->validate([
                "date" => "required"
            ]);

            $date = $request->date;
            

            $daysInMonth = Carbon::parse($date)->daysInMonth;

            $results = WorkOrder::select("is_complete", DB::raw($quantifier.', DAY(created_at) as day'))
            ->whereRaw(DB::raw("DATE_FORMAT(created_at, '%M %Y') = '$date'"))->groupBy("is_complete", "day")->get();

            $labels = $this->createArrayOfDays($daysInMonth);
            $approved = $pending = $this->createNEmptyArray($daysInMonth);

            foreach($results as $result){
                switch($result->is_complete){
                    case 0:
                        $pending[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    case 1:
                        $approved[$result->day - 1] = $result->kount != null ? $result->kount : $result->cost;
                        break;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Approved", "data" => $approved),
                    array("name" => "Pending" , "data" => $pending)
                ),
                "timespan" => $request->date,
                "type" => "Daily"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
        
    }
    
    private function monthsBetween($start, $end){
        $months = array();

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $months[] = $month->format('F Y');
        }
        return $months;
    }

    private function yearsBetween($start, $end){
        $years = array();

        foreach (CarbonPeriod::create($start, '1 year', $end) as $year) {
            $years[] = $year->format('Y');
        }
        return $years;
    }

    private function createNEmptyArray($length){
        $temp = array();
        
        for($i = 0; $i < $length; $i++){
            $temp[] = 0;
        } 

        return $temp;
    }

    public function getMonths(Request $request){
        $months = WorkOrder::select(DB::raw("DISTINCT(DATE_FORMAT(created_at, '%M %Y')) as month"))->where("hospital_id", $request->hospital_id)->get();
        return response()->json($months);
    }

    public function getYears(Request $request){
        $years = WorkOrder::select(DB::raw("DISTINCT(YEAR(created_at)) as year"))->where("hospital_id", $request->hospital_id)->get();
        return response()->json($years);
    }

    public function getPmMonths(Request $request){
        $months = PreventiveMaintenance::select(DB::raw("DISTINCT(DATE_FORMAT(created_at, '%M %Y')) as month"))
        ->whereHas("pm_schedule", function($q)use($request){
            $q->where("hospital_id", $request->hospital_id);
        })->get();
        return response()->json($months);
    }

    public function getPmYears(Request $request){
        $years = PreventiveMaintenance::select(DB::raw("DISTINCT(YEAR(created_at)) as year"))
        ->whereHas("pm_schedule", function($q)use($request){
            $q->where("hospital_id", $request->hospital_id);
        })->get();
        return response()->json($years);
    }

    public function createArrayOfDays($lengthOfMonth){
        $temp = array();
        
        for($i = 1; $i <= $lengthOfMonth; $i++){
            $temp[] = $i;
        } 

        return $temp;
    }

    public function getPms(Request $request){
        $request->validate([
            "hospital_id" => "required"
        ]);

        if($request->interval == null){
            $statuses = ["Approved", "Pending", "Declined"];
            $results = PreventiveMaintenance::select("is_completed as status", DB::raw("COUNT(id) as kount"))
            ->whereHas("pm_schedule", function($q)use($request){
                $q->where("hospital_id", $request->hospital_id);
            })->groupBy("status")->get();
            $data = [0,0,0]; 
            
            foreach($results as $result){
                 switch($result->status){
                     case 2:
                        $data[1] = $result->kount;
                        break;
                     case 1:
                        $data[0] = $result->kount;
                        break;
                     case 0:
                        $data[2] = $result->kount;
                        break;
                     default: 
                        break;
                 }
             }
 
             return response()->json([
                 "labels" => $statuses,
                 "datasets" => array(array("name" => "Work orders", "data" => $data)),
                 "timespan" => "all time",
                 "type" => ""
             ])->setEncodingOptions(JSON_NUMERIC_CHECK);
 
         }else if($request->interval == "month"){
            $request->validate([
                "date" => "required"
            ]);

            $results = PreventiveMaintenance::select("is_completed as status", DB::raw("COUNT(id) as kount, MONTH(created_at) as mth"))
            ->whereYear("created_at", "=", $request->date)->whereHas("pm_schedule", function($q)use($request){
                $q->where("hospital_id", $request->hospital_id);
            })->groupBy("status", "mth")->get();
            
            $labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $statuses = ["Approved", "Pending", "Declined"];
            
            $approvals = $pendings = $declines = array(0,0,0,0,0,0,0,0,0,0,0);

            $total = 0;

            foreach($results as $result){
                switch($result->status){
                    case 0:
                        $declines[$result->mth - 1] = $result->kount;
                        $total+= $result->kount;
                        break;
                    case 1: 
                        $approvals[$result->mth - 1] = $result->kount;
                        $total+= $result->kount;
                        break;
                    case 2:
                        $pendings[$result->mth - 1] = $result->kount;
                        $total+= $result->kount;
                        break;
                    default:
                        break;
                } 
            }

            $data = array(
                array(
                    "name" => "Pending",
                    "type" => "line",
                    "data" => $pendings
                ),
                array(
                    "name" => "Approved",
                    "type" => "line",
                    "data" => $approvals
                ),
                array(
                    "name" => "Declined",
                    "type" => "line",
                    "data" => $declines
                ),
            );

            return response()->json([
                "labels" => $labels,
                "datasets" => $data,
                "timespan" => $request->date,
                "type" => "Monthly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
         }else if($request->interval == "year"){
            $from = date('Y-m-d', strtotime($request->from));
            $to = date('Y-m-d', strtotime($request->to));
            
            
            $results = PreventiveMaintenance::select("is_completed as status", DB::raw('COUNT(id) as kount, YEAR(created_at) as year'))
            ->whereDate("created_at", ">=", $from)->whereDate("created_at", "<=", $to)->whereHas("pm_schedule", function($q)use($request){
                $q->where("hospital_id", $request->hospital_id);
            })->groupBy("status", "year")->get();
            
            $labels = $this->yearsBetween($from, $to);
            $start = $labels[0];

            $approved = $pending = $declined = $this->createNEmptyArray(count($labels));

            foreach($results as $result){
                $index = $result->year - $start;

                switch($result->status){
                    case 0:
                        $declined[$index] = $result->kount;
                        break;
                    case 1:
                        $approved[$index] = $result->kount;
                        break;
                    case 2:
                        $pending[$index] = $result->kount;
                        break;
                    default: 
                        break;
                }
            }

            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Pending" , "data" => $pending),
                    array("name" => "Approved" , "data" => $approved),
                    array("name" => "Declined" , "data" => $declined)
                ),
                "timespan" => $from.' to '.$to,
                "type" => "Yearly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
         }else if($request->interval == "quarter"){
            $results = PreventiveMaintenance::select("is_completed as status", DB::raw('COUNT(id) as kount, QUARTER(created_at) as quarter'))
            ->whereYear("created_at", "=", $request->date)->whereHas("pm_schedule", function($q)use($request){
                $q->where("hospital_id", $request->hospital_id);
            })->groupBy("status", "quarter")->get();

            $labels = ["January - March ".$request->date, "April - June ".$request->date, "July - September ".$request->date, "October - December ".$request->date];
            $approved = $pending = $declined = [0,0,0,0];
            $total = 0;
            
            foreach($results as $result){
                switch($result->status){
                    case 0:
                        $declined[$result->quarter - 1] = $result->kount;
                        $total+= $result->kount;
                        break;
                    case 1:
                        $approved[$result->quarter - 1] = $result->kount;
                        $total+= $result->kount;
                        break;
                    case 2:
                        $pending[$result->quarter - 1] = $result->kount;
                        $total+= $result->kount;
                        break;
                    default:
                        break;
                }
            }

            $labels = array_unique($labels);
            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Approved" , "data" => $approved),
                    array("name" => "Pending" , "data" => $pending),
                    array("name" => "Declined" , "data" => $declined)
                ),
                "timespan" => $request->date,
                "type" => "Quarterly"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
         }else if($request->interval == "daily"){
            $request->validate([
                "date" => "required"
            ]);

            $date = $request->date;

            $results = PreventiveMaintenance::select("is_completed as status", DB::raw('COUNT(id) as kount, DAY(created_at) as day'))
            ->whereRaw(DB::raw("DATE_FORMAT(created_at, '%M %Y') = '$date'"))->whereHas("pm_schedule", function($q)use($request){
                $q->where("hospital_id", $request->hospital_id);
            })->groupBy("status", "day")->get();

            $daysInMonth = Carbon::parse($date)->daysInMonth;
            
            $labels = $this->createArrayOfDays($daysInMonth);
            $approved = $pending = $declined = $this->createNEmptyArray($daysInMonth);
            $total = 0;

            foreach($results as $result){
                switch($result->status){
                    case 0:
                        $declined[$result->day - 1] = $result->kount;
                        $total += $result->kount;
                        break;
                    case 1:
                        $approved[$result->day - 1] = $result->kount;
                        $total += $result->kount;
                        break;
                    case 2:
                        $pending[$result->day - 1] = $result->kount;
                        $total += $result->kount;
                        break;
                    default:
                        break;
                }
            }


            return response()->json([
                "labels" => $labels,
                "datasets" => array(
                    array("name" => "Approved" , "data" => $approved),
                    array("name" => "Pending" , "data" => $pending),
                    array("name" => "Declined" , "data" => $declined)
                ),
                "timespan" => $request->date,
                "type" => "Daily",
                "total" => $total
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
         }else if($request->interval == "type"){
            $request->validate([
                "date" => "required"
            ]);

            $date = $request->date;

            /*$results = PreventiveMaintenance::select("pm_schedule_id as id", DB::raw("COUNT(id) as y"))
            ->with(array('pm_schedule'=>function($query){
                $query->select('title as name');
            }))->whereYear('created_at', '=', $date)->groupBy("id")->get();*/
            
            $results = DB::select("SELECT preventive_maintenances.id as id, pm_schedules.title as name, COUNT(preventive_maintenances.id) as y FROM pm_schedules, preventive_maintenances where pm_schedules.id= preventive_maintenances.pm_schedule_id and pm_schedules.deleted_at is null and preventive_maintenances.deleted_at is null and YEAR(preventive_maintenances.created_at) = '$date' and pm_schedules.hospital_id = '$request->hospital_id' group by id");

            return response()->json([
                "datasets" => $results,
                "timespan" => $request->date,
                "type" => "Type"
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
         }
    }

    public function equipmentReport(Request $request){
        $request->validate([
            "hospital_id" => "required",
            "type" => "required"
        ]);

        if($request->type == "status"){
            if($request->group == null){
                $results = Asset::select(DB::raw("COUNT(id) as y, status as name"))->where("hospital_id", $request->hospital_id)->groupBy("status")->get();
                return response()->json([
                    "datasets" => $results,
                    "type" => "Equipment by status",
                    "chart" => "pie"
                ]);
            }else if($request->group == "department"){
                $results = Asset::select(DB::raw("COUNT(id) as y, department_id, status"))->with("department")->where("hospital_id", $request->hospital_id)->groupBy("status", "department_id")->get();
                
                $departments = $results->pluck("department_id")->unique();

                $labels = array();
                $good = $bad = $this->createNEmptyArray($departments->count());
                foreach($departments as $key => $department){
                    $found = false;
                    foreach($results as $result){
                        if($result->department_id == $department){
                            if(!$found){
                                if($department != null){
                                    $labels[] = $result->department->name;
                                }else{
                                    $labels[] = "N/A";
                                }
                                $found = true;

                                switch($result->status){
                                    case "Good":
                                        $good[$key] = $result->y;
                                        break;
                                    case "Bad":
                                        $bad[$key] = $result->y;
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }
                return response()->json([
                    "labels" => $labels,
                    "datasets" => array(
                        array("name" => "Good", "data" => $good),
                        array("name" => "Bad", "data" => $bad)
                    ),
                    "type" => "Equipment by department and status",
                    "chart" => "line"
                ]);
            }else if($request->group == "unit"){
                $results = Asset::select(DB::raw("COUNT(id) as y, unit_id, status"))->with("unit")->where("hospital_id", $request->hospital_id)->groupBy("status", "unit_id")->get();
                $units = $results->pluck("unit_id")->unique();
                $labels = array();

                $good = $bad = $this->createNEmptyArray($units->count());

                foreach($units as $key => $unit){
                    $found = false;
                    foreach($results as $result){
                        if($result->unit_id == $unit){
                            if(!$found){
                                if($unit != null){
                                    $labels[] = $result->unit->name;
                                }else{
                                    $labels[] = "N/A";
                                }
                                $found = true;

                                switch($result->status){
                                    case "Good":
                                        $good[$key] = $result->y;
                                        break;
                                    case "Bad":
                                        $bad[$key] = $result->y;
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }
                return response()->json([
                    "labels" => $labels,
                    "datasets" => array(
                        array("name" => "Good", "data" => $good),
                        array("name" => "Bad", "data" => $bad)
                    ),
                    "type" => "Equipment by unit and status",
                    "chart" => "line"
                ]);
            }else if($request->group == "category"){
                $results = Asset::select(DB::raw("COUNT(id) as y, asset_category_id, status"))->with("asset_category")->where("hospital_id", $request->hospital_id)->groupBy("status", "asset_category_id")->get();

                $categories = $results->pluck("asset_category_id")->unique();
                $labels = array();

                $good = $bad = $this->createNEmptyArray($categories->count());

                foreach($categories as $key => $category){
                    $found = false;
                    foreach($results as $result){
                        if($result->asset_category_id == $category){
                            if(!$found){
                                if($category != null){
                                    $labels[] = $result->asset_category->name;
                                }else{
                                    $labels[] = "N/A";
                                }
                                $found = true;

                                switch($result->status){
                                    case "Good":
                                        $good[$key] = $result->y;
                                        break;
                                    case "Bad":
                                        $bad[$key] = $result->y;
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }

                return response()->json([
                    "labels" => $labels,
                    "datasets" => array(
                        array("name" => "Good", "data" => $good),
                        array("name" => "Bad", "data" => $bad)
                    ),
                    "type" => "Equipment by category and status",
                    "chart" => "line"
                ]);
            }
        }else if($request->type == "availability"){
            if($request->group == null){
                $results = Asset::select(DB::raw("COUNT(id) as y, availability as name"))->where("hospital_id", $request->hospital_id)->groupBy("availability")->get();
                return response()->json([
                    "datasets" => $results,
                    "type" => "Equipment by availability",
                    "chart" => "pie"
                ]);
            }else if($request->group == "department"){
                $results = Asset::select(DB::raw("COUNT(id) as y, department_id, availability"))->with("department")->where("hospital_id", $request->hospital_id)->groupBy("availability", "department_id")->get();
                $labels = array();

                $departments = $results->pluck("department_id")->unique();
                $operational = $non_operational = $this->createNEmptyArray($results->count());
                
                foreach($departments as $key => $department){
                    $found = false;
                    foreach($results as $result){
                        if($result->department_id == $department){
                            if(!$found){
                                if($department != null){
                                    $labels[] = $result->department->name;
                                }else{
                                    $labels[] = "N/A";
                                }
                                $found = true;

                                switch($result->availability){
                                    case "Operational":
                                        $operational[$key] = $result->y;
                                        break;
                                    case "Non operational":
                                        $non_operational[$key] = $result->y;
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }
                
                return response()->json([
                    "labels" => $labels,
                    "datasets" => array(
                        array("name" => "Operational", "data" => $operational),
                        array("name" => "Non operational", "data" => $non_operational)
                    ),
                    "type" => "Equipment by department and availability",
                    "chart" => "line"
                ]);
            }else if($request->group == "unit"){
                $results = Asset::select(DB::raw("COUNT(id) as y, unit_id, availability"))->with("unit")->where("hospital_id", $request->hospital_id)->groupBy("availability", "unit_id")->get();
                $labels = array();

                $units = $results->pluck("unit_id")->unique();
                $operational = $non_operational = $this->createNEmptyArray($results->count());
                
                foreach($units as $key => $unit){
                    $found = false;
                    foreach($results as $result){
                        if($result->unit_id == $unit){
                            if(!$found){
                                if($unit != null){
                                    $labels[] = $result->unit->name;
                                }else{
                                    $labels[] = "N/A";
                                }
                                $found = true;

                                switch($result->availability){
                                    case "Operational":
                                        $operational[$key] = $result->y;
                                        break;
                                    case "Non operational":
                                        $non_operational[$key] = $result->y;
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }
                
                return response()->json([
                    "labels" => $labels,
                    "datasets" => array(
                        array("name" => "Operational", "data" => $operational),
                        array("name" => "Non operational", "data" => $non_operational)
                    ),
                    "type" => "Equipment by unit and availability",
                    "chart" => "line"
                ]);
            }else if($request->group == "category"){
                $results = Asset::select(DB::raw("COUNT(id) as y, asset_category_id, availability"))->with("asset_category")->where("hospital_id", $request->hospital_id)->groupBy("availability", "asset_category_id")->get();

                $categories = $results->pluck("asset_category_id")->unique();
                $labels = array();

                $operational = $non_operational = $this->createNEmptyArray($categories->count());

                foreach($categories as $key => $category){
                    $found = false;
                    foreach($results as $result){
                        if($result->asset_category_id == $category){
                            if(!$found){
                                if($category != null){
                                    $labels[] = $result->asset_category->name;
                                }else{
                                    $labels[] = "N/A";
                                }
                                $found = true;

                                switch($result->availability){
                                    case "Operational":
                                        $operational[$key] = $result->y;
                                        break;
                                    case "Non operational":
                                        $non_operational[$key] = $result->y;
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }

                return response()->json([
                    "labels" => $labels,
                    "datasets" => array(
                        array("name" => "Operational", "data" => $operational),
                        array("name" => "Non operational", "data" => $non_operational)
                    ),
                    "type" => "Equipment by category and availability",
                    "chart" => "line"
                ]);
            }
        }
    }

    public function technicianReport(Request $request){
        $request->validate([
            "from" => "required",
            "to" => "required",
            "hospital_id" => "required"
        ]);
        
        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to));

        $result = User::where("role", "Admin")->orWhere("role", "Limited Technician")->orWhere("role", "Regular Technician")->withCount(["work_orders" => function($q) use ($from, $to){
            $q->whereDate("work_orders.created_at", ">=", $from)->whereDate("work_orders.created_at", "<=", $to);
        }])->withCount(["work_order_teams" => function($q) use ($from, $to){
            $q->whereDate("teams.created_at", ">=", $from)->whereDate("teams.created_at", "<=", $to);
        }])->get();
        
        return response()->json($result);
    }
}

