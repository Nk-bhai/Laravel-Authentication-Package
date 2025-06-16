<?php

namespace Nk\SystemAuth\Http\Controllers;

use App\Http\Controllers\Controller;
// use Http;
use App\Models\UserModel;
use DB;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\AdminController;
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

    public function verifyKey(Request $request)
    {
        $request->validate([
            'key' => ['required', 'max:14'],
        ]);

        $key = $request->input('key');
        $clientIp = $request->ip(); // Get user's IP

        // 1. Fetch key details from the API
        $response = Http::get("http://192.168.12.127:8005/api/superadmin/{$key}");

        if (!$response->ok() || $response['key'] !== $key) {
            return redirect()->route('system.auth.key')->with(['error' => 'Invalid Key', 'key_value' => $key]);
        }

        $keyData = $response->json();

        // 2. Check if IP is already used
        if (!empty($keyData['ip_address']) && $keyData['ip_address'] !== $clientIp) {
            return redirect()->route('system.auth.key')->with('error', 'Key is already used by another IP');
        }

        if ($keyData['verified']) {
            return redirect()->route('system.auth.login')->with('error', 'Key already verified');
        }
        // 3. Mark as verified & store IP via API
        Http::post("http://192.168.12.127:8005/api/superadmin/verify/{$key}", [
            'ip_address' => $clientIp,
        ]);

        // 4. Set session
        $request->session()->put('key_verified', true);
        $request->session()->put('session_key', $key);

        // Check if database is already set in backend
       
        if (!empty($keyData['database']) && $keyData['database'] !== 'system') {
            // dd("Hello");
            // Database already set, go directly to login
            return redirect()->route('system.auth.login')->with('message', 'Key verified, please log in.');
        }

        // If not set, show database setup page
        $request->session()->put('show_database_page', true);
        return redirect()->route('system.auth.database');

    }

    public function showLoginPage()
    {
        return view('system-auth::login');
    }
    public function database(Request $request)
    {
        $database_name = $request->input('database_name');
        session(['database_name' => $database_name]);

        Http::post("http://192.168.12.127:8005/api/superadmin/save/" . session('session_key'), [
            'database_name' => $database_name,
        ]);


        session()->forget('show_database_page');

        return redirect()->route('system.auth.login');
    }


    public function showDatabasePage(Request $request)
    {
        if (!session('show_database_page')) {
            return redirect()->route('system.auth.login')->with('error', 'Unauthorized access to database setup');
        }

        return view('system-auth::database');
    }


    public function login(Request $request)
    {
        // Validate the input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        try {
            // 1. First, check using the API
            $response = Http::get('http://192.168.12.127:8005/api/superadmin/' . session('session_key'));

            if ($response->ok()) {
                $data = $response->json();

                if ($data && isset($data['email'], $data['password'])) {
                    if ($data['email'] === $email && Hash::check($password, $data['password'])) {
                        $request->session()->put('user_logged_in', true);
                        return redirect()->route('dashboard');
                    }
                }
            }

            // 2. If API check fails, fall back to the admin method
            $adminController = app(AdminController::class);
            $adminAuthenticated = $adminController->admin($request);

            if ($adminAuthenticated) {
                $request->session()->put('user_logged_in', true);
                return redirect()->route('UserTable'); // Redirect to UserTable if admin method authenticates
            }

            // 3. If both checks fail, redirect back with an error
            return redirect()->back()->with(['error' => 'Invalid email or password.', 'loginemail' => $email, 'loginpassword' => $password]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

}