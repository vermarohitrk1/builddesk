<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Login successful',
            //     'data' => [
            //         'redirect' => '/dashboard',
            //         'redirect_delay' => 500
            //     ]
            // ]);
            return redirect()->intended('/dashboard');
        }

        // return response()->json([
        //     'status' => 'error',
        //     'message' => 'The provided credentials do not match our records.',
        //     'data' => [
        //         'errors' => ['email' => 'Invalid credentials']
        //     ]
        // ], 422);
        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
