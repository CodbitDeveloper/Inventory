<?php

namespace App\Http\Controllers;

use App\AssetCategory;
use App\FaultCategory;
use App\Priority;
use App\PartCategory;

use Illuminate\Http\Request;
use Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Display all categories 
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role == 'Admin' || $user->role == 'Regular Technician') {
            $asset_categories = AssetCategory::with('parent', 'children')->where('hospital_id', $user->hospital_id)->get();
            $fault_categories = FaultCategory::where('hospital_id', $user->hospital_id)->get();
            $part_categories = PartCategory::where('hospital_id', $user->hospital_id)->get();
            $priority_categories = Priority::where('hospital_id', $user->hospital_id)->get();

            function filter_parents($item){
                return $item->children->count() > 0;
            }

            $parent_categories = $asset_categories->filter(function($item){
                return $item->parent_id == null;
            })->values();

            return view('categories', compact("asset_categories", "fault_categories", "priority_categories", "parent_categories", "part_categories", "user"));
        } else {
            abort(403);
        }
    }
}
