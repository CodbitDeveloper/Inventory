<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Config;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        Config::set('auth.providers.users.model', \App\Admin::class);
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }


    /**
     * ----------------------------------
     * Regional administrator login page
     * ----------------------------------
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * -----------------------------------------
     * Login for regional administrator
     * ------------------------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function login(Request $request)
    {
        //Validate the form data
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|min:6'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'active' => 1,
        ];

        //Attempt to log the user in
        if(Auth::guard('admin')->attempt($credentials, $request->remember)){
            //if successful, then redirect to intended location
            //dd(Auth::guard('admin')->user()->id);
        
            return redirect()->intended(route('admin.dashboard'));
        }
        
        //if unsuccessful, the redirect back to login page with the form data
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'active' => 'This account has been deactived.'
            ]);
    }

    /**
     * -----------------------------------
     * Logout for regional administrator
     * -----------------------------------
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin');
    }
}
