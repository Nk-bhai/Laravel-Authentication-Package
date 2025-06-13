<?php

namespace Nk\SystemAuth\Http\Controllers;

use App\Http\Controllers\Controller;
// use Http;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Nk\SystemAuth\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\SuperAdminApiController;
use Redirect;


class AuthController extends Controller
{
    public function showKeyPage()
    {
        return view('system-auth::key');
        // return redirect()->route('key');
    }

    // public function verifyKey(Request $request)
    // {
    //     $request->validate([
    //         'key' => ['required', 'max:14'],
    //     ]);

    //     $key = $request->input('key');
    //     $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);
    //     // return $data['key'];

    //     // $data = (\src\Http\Controllers\SuperAdminApiController::class)->show($key);  
    //     //  $controller = new SuperAdminApiController();
    //     // $data = $controller->show($key);

    //     if ($data['key'] !== $key) {
    //         return redirect()->route('system.auth.key')->with('error', 'Invalid Key');
    //     }


    //     $request->session()->put('key_verified', true);
    //     $request->session()->put('session_key', $key);
    //     // dd(session('session_key'));
    //     // dd("Hi");
    //     return redirect()->route('system.auth.login');
    // }


    public function verifyKey(Request $request)
    {
        $request->validate([
            'key' => ['required', 'max:14'],
        ]);

        $key = $request->input('key');

        // Fetch key details from the API
        $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);

        if (!$data->ok() || $data['key'] !== $key) {
            return redirect()->route('system.auth.key')->with('error', 'Invalid Key');
        }

        if ($data['verified']) {
            return redirect()->route('system.auth.login')->with('error', 'Key already verified');
        }

        // Mark as verified via API
        Http::post('http://127.0.0.1:8000/api/superadmin/verify/' . $key);

        $request->session()->put('key_verified', true);
        $request->session()->put('session_key', $key);

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
        $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . session('session_key'));
        
        if ($data['email'] !== $request->input('email') || !Hash::check($request->input('password'), $data['password'])) {
            return redirect()->back()->with('error', 'Invalid Credentials');

        }

        $request->session()->put('user_logged_in', true);
        return redirect()->route('dashboard');
    }


}