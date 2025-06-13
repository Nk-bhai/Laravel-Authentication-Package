<?php

namespace Nk\SystemAuth\Http\Controllers;

use App\Http\Controllers\Controller;
// use Http;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Nk\SystemAuth\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\SuperAdminApiController;


class AuthController extends Controller
{
    public function showKeyPage()
    {
        return view('system-auth::key');
    }

    public function verifyKey(Request $request)
    {
        $request->validate([
            'key' => ['required', 'max:14'],
        ]);

        $key = $request->input('key');
        $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);
        // return $data['key'];
        // dd($data);
        // return $data['email'];
        // $data = (\src\Http\Controllers\SuperAdminApiController::class)->show($key);  
        //  $controller = new SuperAdminApiController();
        // $data = $controller->show($key);
        if ($data['key'] !== $key) {
            dd("Hello");
            return redirect()->route('system.auth.key')->with('error', 'Invalid Key');
        }


        $request->session()->put('key_verified', true);
        $request->session()->put('session_key', $key);
        // dd(session('session_key'));
        // dd("Hi");
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
        // dd(session('session_key'));
        $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . session('session_key'));
        // return $data['email'];
        if ($data['email'] !== $request->input('email') || !Hash::check($request->input('password'), $data['password'])) {
            // dd("Hello");
            return redirect()->back()->with('error', 'Invalid Credentials');

        }
        // $superAdmin = SuperAdmin::where('email', $request->input('email'))->first();
        // if (!$superAdmin || !Hash::check($request->input('password'), $superAdmin->password)) {
        //     return redirect()->back()->with('error', 'Invalid Credentials');
        // }

        // session(['email' => $superAdmin->email]);
        return redirect()->route('dashboard');
    }


}