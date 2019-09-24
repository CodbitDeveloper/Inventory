<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    /**
     * -------------------------------
     * Mark all notifications as read 
     * -------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead(Request $request){
        if(Auth::guard('admin')->user() != null){
            Auth::guard('admin')->user()->unreadNotifications->markAsRead();
            return response()->json([
                'error' => false,
                'message' => 'Notifications marked as read'
            ]);
        }else if(Auth::user() != null){
            Auth::user()->unreadNotifications->markAsRead();
            return response()->json([
                'error' => false,
                'message' => 'Notifications marked as read'
            ]);
        }else{
            return response()->json([
                'error' => true,
                'message' => 'No authenticated user'
            ]);
        }
    }
}
