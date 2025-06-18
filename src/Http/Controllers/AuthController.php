<?php

namespace Nk\SystemAuth\Http\Controllers;

use App\Http\Controllers\Controller;
// use Http;
use App\Models\UserModel;
use Artisan;
use DB;
// use Config;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ViewErrorBag;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Log;
use Nk\SystemAuth\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\SuperAdminApiController;
use Redirect;
use Schema;
use Throwable;



class AuthController extends Controller
{
    public function showKeyPage()
    {
        return view('system-auth::key');

        // return redirect()->route('key');
    }

    public function verifyKey(Request $request)
    {
        // dd("hello");
        $request->validate([
            'key' => ['required', 'max:14'],
        ]);

        $key = $request->input('key');
        $clientIp = $request->ip(); // Get user's IP

        // 1. Fetch key details from the API
        $response = Http::get("http://192.168.12.143:8005/api/superadmin/{$key}");

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
        Http::post("http://192.168.12.143:8005/api/superadmin/verify/{$key}", [
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

    // public function database(Request $request)
    // {

    //     $request->validate([
    //         'database_name' => 'required|alpha_dash|max:20',
    //         'host_name' => 'required|string|max:255',
    //         'user_name' => 'required',
    //         'db_password' => 'sometimes|nullable|string|min:2|max:32', // optional password
    //     ]);

    //     // dd($request->all());
    //     updateEnv([
    //         'DB_HOST' => $request->input('host_name'),
    //         'DB_USERNAME' => $request->input('user_name'),
    //         'DB_PASSWORD' => $request->input('db_password'),
    //     ]);

    //     $database_name = $request->input('database_name');
    //     session(['database_name' => $database_name]);

    //     Http::post("http://192.168.12.143:8005/api/superadmin/save/" . session('session_key'), [
    //         'database_name' => $database_name,
    //     ]);
    //     // seeding
    //     // Step 1: Fetch API data
    //     // $seed_key = session('session_key');
    //     // $response = Http::get("http://192.168.12.143:8005/api/superadmin/{$seed_key}");

    //     // $keyData = $response->json();
    //     // $seed_email = $keyData['email'];
    //     // $seed_password = $keyData['password'];
    //     // $seed_email = "nk@gmail.com";
    //     // $seed_password = "Nk@12345";

    //     // Step 2: Set up dynamic DB connection
    //     $database_name = $request->input('database_name');
    //     DB::statement('CREATE DATABASE IF NOT EXISTS ' . $database_name);
    //     Config::set('database.connections.dynamic_db', [
    //         'driver' => 'mysql',
    //         'host' => $request->input('host_name'),
    //         'port' => 3306,
    //         'database' => $database_name,
    //         'username' => $request->input('user_name'),
    //         'password' => $request->input('db_password'),
    //         'charset' => 'utf8mb4',
    //         'collation' => 'utf8mb4_unicode_ci',
    //         'prefix' => '',
    //         'strict' => true,
    //         'engine' => null,
    //     ]);

    //     // DB::purge('dynamic_db');
    //     // DB::reconnect('dynamic_db');

    //     // try {
    //         // dd("Hi");
    //         // dd(DB::connection('dynamic_db')->getPdo());
    //     // } catch (\Exception $e) {
    //     //     dd("Database connection failed: " . $e->getMessage());
    //     // }

    //     // dd(DB::connection()->getDatabaseName());
    //     // // Run migrations on dynamic_db connection
    //     Artisan::call('migrate', [
    //         '--database' => 'dynamic_db',
    //         '--path' => base_path('nk-system-auth/database/migrations'),
    //         '--force' => true,
    //     ]);
    //     dd("i");
    //     dd("Hi");
    //     // // C:\nk-system-auth\database\migrations\create_users_table.php
    //     // // Insert Super Admin user
    //     // $seed_email = "nk@gmail.com";
    //     // $seed_password = "Nk@12345";

    //     // DB::connection('dynamic_db')->table('users')->insert([
    //     //     'email' => $seed_email,
    //     //     'password' => $seed_password,
    //     //     // 'created_at' => now(),
    //     //     // 'updated_at' => now(),
    //     // ]);
    //     // dd("Hello");


    //     session()->forget('show_database_page');

    //     return redirect()->route('system.auth.login');
    // }


    // public function database(Request $request)
    // {

    //     $request->validate([
    //         'database_name' => 'required|alpha_dash|max:20',
    //         'host_name' => 'required|string|max:255',
    //         'user_name' => 'required',
    //         'db_password' => 'sometimes|nullable|string|min:2|max:32', // optional password
    //     ]);

    //     // dd($request->all());
    //     updateEnv([
    //         'DB_HOST' => $request->input('host_name'),
    //         'DB_USERNAME' => $request->input('user_name'),
    //         'DB_PASSWORD' => $request->input('db_password'),
    //     ]);

    //     $database_name = $request->input('database_name');
    //     session(['database_name' => $database_name]);

    //     Http::post("http://192.168.12.143:8005/api/superadmin/save/" . session('session_key'), [
    //         'database_name' => $database_name,
    //     ]);
    //     $database_name = $request->input('database_name');


    //     session()->forget('show_database_page');
    //     echo "hi";exit;
    //     return redirect()->route('system.auth.login');
    // }

    public function database(Request $request)
    {

        $request->validate([
            'database_name' => 'required|alpha_dash|max:20',
            'host_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'db_password' => 'sometimes|nullable|string|min:2|max:32',
        ]);

        try {
            $result = updateEnv([
                'DB_HOST' => $request->input('host_name'),
                'DB_USERNAME' => $request->input('user_name'),
                'DB_PASSWORD' => $request->input('db_password') ?? '',
                'DB_DATABASE' => $request->input('database_name'),
            ]);

            config(['database.connections.mysql.host' => $request->input('host_name')]);
            config(['database.connections.mysql.database' => null]); // No database selected yet
            config(['database.connections.mysql.username' => $request->input('user_name')]);
            config(['database.connections.mysql.password' => $request->input('db_password') ?? '']);
            DB::purge('mysql');

            DB::connection('mysql')->getPdo();

            // Create database
            $database_name = $request->input('database_name');
            try {
                DB::statement("CREATE DATABASE IF NOT EXISTS `$database_name`");
                Log::info('Database created successfully: ' . $database_name);
            } catch (\Illuminate\Database\QueryException $e) {
                throw new Exception('Database creation failed: ' . $e->getMessage());
            }

            // Update config with new database
            config(['database.connections.mysql.database' => $database_name]);
            DB::purge('mysql');

            // Store database name in session
            session(['database_name' => $database_name]);

            // Run migrations
            try {
                DB::connection('mysql')->getPdo(); // Re-test connection
                Schema::connection('mysql')->create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('email')->unique();
                    $table->string('password');
                    $table->timestamps();
                });
            } catch (Throwable $e) {
                Log::error('Migration failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'code' => $e->getCode(),
                ]);
                throw new Exception('Migration failed: ' . $e->getMessage());
            }

            // Run seeder
            try {
                // $ip_address = $request->ip();
                // $response = Http::get("http://192.168.12.143:8005/api/superadmin/get/{$ip_address}");
                // $keyData = $response->json();
                
                DB::table('users')->insert([
                    // 'email' => $keyData['email'],
                    // 'password' => $keyData['password'],
                    'email' => "nk@gmail.com",
                    'password' => "Nk@12345",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                // Log::info('Seeding output');
            } catch (Throwable $e) {
                Log::error('Seeding failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'code' => $e->getCode(),
                ]);
                throw new Exception('Seeding failed: ' . $e->getMessage());
            }

            // Make API call
            Http::post("http://192.168.12.143:8005/api/superadmin/save/" . session('session_key'), [
                'database_name' => $database_name,
            ]);
            

            // Clear flag and redirect
            session()->forget('show_database_page');
            return redirect()->route('system.auth.login')->with('success', 'Database created successfully');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create database: ' . $e->getMessage()]);
        }
    }
    public function showDatabasePage(Request $request)
    {
        if (!session('show_database_page')) {
            return redirect()->route('system.auth.login')->with('error', 'Unauthorized access to database setup');
        }
        // dd("hello");

        return view('system-auth::database', [
            'errors' => session()->get('errors', new ViewErrorBag),
        ]);
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
            $response = Http::get('http://192.168.12.143:8005/api/superadmin/' . session('session_key'));

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