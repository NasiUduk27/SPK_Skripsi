<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // <--- THIS IS THE CORRECT IMPORT

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / logout.
     *
     * @var string
     */
    protected $redirectTo = '/home'; // Or '/login' if you want login redirect there

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has logged out.
     *
     * @param  \Illuminate\Http\Request  $request // <--- ENSURE THIS TYPE HINT USES THE CONCRETE CLASS
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        // Example: Redirect to the 'home' route after logout with a status message
        return redirect()->route('home')->with('status', 'You have been logged out!');

        // If you had it as 'return redirect('/login')' previously, just change it to 'return redirect('/home')'
        // return redirect('/home');
    }
}