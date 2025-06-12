<?php

namespace Nk\SystemAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nk\SystemAuth\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showKeyPage()
    {
        return view('system-auth::key');
    }

    public function verifyKey(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'max:14'],
        ]);
        $key = $request->input('key');
        $superAdmin = SuperAdmin::where('key' , '=' , $key)->first();
        if (!$superAdmin || $superAdmin->key !== $request->input('key')) {
            return redirect()->back()->with('error', 'Invalid Key');
        }
        
        // session(['key_verified' => true]);
        $request->session()->put('key_verified' , "true");
        // dd(session('key_verified'));
        return redirect()->route('system.auth.login');
    }

    public function showLoginPage()
    {
        return view('system-auth::login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $superAdmin = SuperAdmin::where('email', $request->input('email'))->first();
        if (!$superAdmin || !Hash::check($request->input('password'), $superAdmin->password)) {
            return redirect()->back()->with('error', 'Invalid Credentials');
        }

        session(['email' => $superAdmin->email]);
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('system.auth.key');
    }
}