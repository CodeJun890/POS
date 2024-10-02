<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function viewLogin() {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return view('Auth.login'); // Show login page if user is not authenticated
        }

        // Redirect based on role
        switch ($user->role) {
            case 'manager':
                return redirect()->route('manager-dashboard.get');
            case 'cashier':
                return redirect()->route('cashier-dashboard.get');
            default:
                return view('Auth.login');
        }
    }

    public function postLogin(Request $request) {
        // Validate email and password fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if the credentials are correct
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            switch ($user->role) {
                case 'manager':
                    $redirectRoute = 'manager-dashboard.get';
                    $authenticatedMessage = 'Welcome to City Burgers POS System';
                    break;
                case 'cashier':
                    $redirectRoute = 'cashier-dashboard.get';
                    $authenticatedMessage = 'Welcome to City Burgers POS System';
                    break;
                default:
                    $redirectRoute = 'login.get'; // Fallback route or home route
                    $authenticatedMessage = 'Welcome! You have been logged in successfully.';
            }

            return redirect()->intended(route($redirectRoute))
                             ->with('authenticated', $authenticatedMessage);
        } else {
            return redirect()->back()->with('error', 'Wrong email or password. Please try again.');
        }
    }

    public function logout(){
        // Clear the session data
        \Illuminate\Support\Facades\Session::flush();

        // Log the user out
        Auth::logout();

        // Regenerate the session ID
        request()->session()->regenerate();

        // Redirect to the login page
        return redirect()->route('login.get');
    }
}
