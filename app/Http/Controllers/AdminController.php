<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;
use App\Hospital;
use App\District;
use App\Requests;
use Auth;
use Mail;
use Notification;
use App\Notifications\AdminFormUpdate;
use App\Region;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view('admin.admin');
    }

    /**
     * --------------------------------
     * View all regional administrators
     * --------------------------------
     */
    public function viewAll()
    {
        $admin = Auth::guard('admin')->user();

        $admins = Admin::with('region')->get();
        return view('admin.users')->with('admins', $admins)->with('admin', $admin);
    }

    /**
     * ----------------------------
     * Get regional admin profile
     * ----------------------------
     */
    public function profile()
    {
        $region = Region::where('id', '=', Auth::guard('admin')->user()->region_id)->first();

        return view('admin.admin-profile')->with('region', $region); 
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
            'email'        => 'required|string',
            'role'         => 'required|string',
            'region_id'    => 'required|string'
        ]);
        
        $admin = new Admin();

        $admin->id = md5($request->email.microtime());
        $admin->firstname = $request->firstname;
        $admin->lastname = $request->lastname;
        $admin->email = $request->email;
        $admin->phone_number = $request->phone_number;
        $admin->password = $request->password;
        $admin->region_id = $request->region_id;
        $admin->role = $request->role;

        if($admin->save()){
            if( $admin->role != "superuser"){
                $region = $admin->region()->first();

                $result = false;
                $data = array("admin" => $admin, "region" => $region);
                $to_name = ucwords($request->firstname.' '.$request->lastname);
                $to_email = $admin->email;
     
                Mail::send('email_templates.new_admin', $data, function($message) use($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                            ->subject('Welcome To The Team');
                    $message->from('noreply@tynkerbox.com', 'TynkerBox');
                });
     
                if(count(Mail::failures()) > 0) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Could not send the mail. Try again!'
                    ]);
                } else {
                    return response()->json([
                        'error' => false,
                        'message' => 'Admin profile completion link was sent successfully'
                    ]);
                }
            }else{
                return response()->json([
                    'error' => false,
                    'data' => $admin,
                    'message' => 'Admin created successfully'
                ], 201);
            }
           
        }

        return response()->json([
            'error' => true,
            'message' => 'Could not create admin'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return response()->json($admin, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'phone_number' => 'required'
        ]);
        
        $admin = Admin::where('id', $request->admin)->first();
        $status = true;

        if($request->password_reset == 'yes'){
           $request->validate([
               'new_password' => 'required|confirmed'
           ]);

           if(Hash::check($request->old_password, $admin->password)) {
               $admin->password = $request->new_password;
           }
        }
        
        $admin->firstname = $request->firstname;
        $admin->lastname  = $request->lastname;
        $admin->phone_number = $request->phone_number;

        if($admin->update()){
            $status = false;
        }
       
        return response()->json([
            'error'   => $status,
            'message' => !$status ? 'Profile updated' : 'Could not update profile'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        //
    }

    /**
     * -----------------------------------------
     * Get all biomedical engineers in a region
     * ------------------------------------------
     */
    public function showEngineers(){
        $admin = Auth::guard('admin')->user();

        if(strtolower($admin->role) == 'admin') {
            $engineers = Admin::where([['role', '=', 'Biomedical Engineer'], ['region_id', '=', $admin->region_id]])->get();
            return view('admin.all-engineers')->with('engineers', $engineers)->with('admin', $admin);
        } else {
            return abort(403);
        }
        
    }

    /**
     * ------------------------------
     * Add a new biomedical enigneer
     * ------------------------------
     * 
     *@return view
     */
    public function addEngineer(){
        $admin = Auth::guard('admin')->user();

        if(strtolower($admin->role) == 'admin') {
            return view('admin.add-engineer', compact('admin'));
        } else {
            return abort(403);
        }
    }
 
    /**
     * ---------------------------------
     * Activate regional user account
     * ---------------------------------
     * 
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function activate(Admin $admin) 
    {
        $admin->active = 1;

        if($admin->save()){
            return response()->json([
                'data'    => $admin,
                'message' => 'Account activated',
                'error'   => false
            ]);
        }else{
            return response()->json([
                'message' => 'Account deactivated',
                'error'   => true
            ]);
        }
    }

    /**
     * ---------------------------------
     * Deactivate regional user account
     * ---------------------------------
     * 
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Admin $admin)
    {
        $admin->active = 0;

        if($admin->save()){
            return response()->json([
                'data'    => $admin,
                'message' => 'Account deactivated',
                'error'   => false
            ]);
        }else{
            return response()->json([
                'message' => 'Account activated',
                'error'   => true
            ]);
        }
    }

    /**
     *------------------------------- -----------------
     * Complete profile registration for regional user
     * ------------------------------------------------
     * 
     * @param  $id
     * @return view
     */
    public function completeProfile($id) 
    {
        $admin = Admin::with('region')->where([['id', $id], ['completed', 0]])->first();

        if($admin == null) {
            abort(404);
        } 

        return view('admin.complete-profile', compact('admin'));
    }

    /**
     * ---------------------------------------------
     * Store regional user profile details
     * ---------------------------------------------
     * 
     * @param  \App\Admin  $admin
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function complete(Admin $admin, Request $request) 
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
            'phone_number' => 'required|string'
        ]);

        $admin = Admin::where('id', $request->id)->first();

        if($admin == null) {
            return response('Forbidden request', 503);
        }
        
        $admin->firstname = $request->firstname;
        $admin->lastname = $request->lastname;
        $admin->password = $request->password;
        $admin->phone_number = $request->phone_number;
        $admin->completed = 1;

        if($admin->save()) {
            $admins = Admin::where([['role', 'Admin'], ['region_id', $admin->region_id]])->get();

            Notification::send($admins, new AdminFormUpdate($admin));

            return response()->json([
                'error' => false,
                'data' => $admin,
                'message' => 'Admin profile completed successfully'
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'Could not complete the admin profile'
        ]);
    }

    /**
     * -----------------------------------
     * Edit regional user account details
     * -----------------------------------
     * 
     * @param  \App\Admin  $admin
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editAdmin(Admin $admin, Request $request)
    {
        $request->validate([
            'role' => 'required'
        ]);

        $status = true;

        $admin->firstname = $request->firstname;
        $admin->lastname = $request->lastname;
        $admin->phone_number = $request->phone_number;
        $admin->role = $admin->role;

        if($admin->update()) {
            $status = false;
        }

        return response()->json([
            'error' => $status,
            'message' => !$status ? 'Admin account updated' : 'Could not update admin account. Try again!'
        ]);
    }

    /**
     * -----------------------------------------------
     * Reset Password for regional biomedical engineer
     * -----------------------------------------------
     * 
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Admin $admin)
    {
        $admin->password = '123456789';

        if($admin->save()) {
            $data = array("user" => $admin);

            $to_name = ucwords($admin->firstname.' '.$admin->lastname);
            $to_email = $admin->email;

            Mail::send('email_templates.reset_password', $data, function($message) use($to_name, $to_email) {
                $message->to($to_email, $to_name)
                        ->subject('Password Reset');
                $message->from('noreply@tynkerbox', 'TynkerBox');   
            });

           if(count(Mail::failures()) > 0) {
               return response()->json([
                   'error' => true,
                   'message' => 'Could not send email. Try again!'
               ]);
           } else {
               return response()->json([
                   'error' => false,
                   'message' => 'Email has been sent successfully.'
               ]);
           }
        }

        return response()->json([
            'error' => true,
            'data' => $admin,
            'message' => false ? 'Password has been reset' : 'Password could not be reset'
        ]);
    }
}
