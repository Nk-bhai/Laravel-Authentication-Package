<?php

namespace Nk\SystemAuth\Http\Controllers;

use App\Http\Controllers\Controller;
// use Http;
use App\Models\UserModel;
use Artisan;
use DB;
// use Config;
use Exception;
use Hash;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ViewErrorBag;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Log;
use Schema;
use Str;
use Throwable;
use Nk\SystemAuth\Services\KeyVerificationService;




class AuthController extends Controller
{


    public function showPurchaseCodePage()
    {


        return view('system-auth::purchaseCode');
    }

    public function PurchaseCode(Request $request)
    {
        $clientIp = $request->ip();

        // Check if purchase code is already verified
        $response = Http::get("http://192.168.12.79:8005/api/superadmin/get/{$clientIp}");

        if ($response->ok() && !empty($response['purchase_code_verified']) && $response['purchase_code_verified'] == 1) {
            return redirect()->route('system.auth.key')->with('message', 'Purchase code already verified');
        }
        
        $purchase_code = $request->input('purchase_code');

        // $response = Http::withToken('LjILXgU18qFsOG3pqtFDMKvcyRVGtP64')->get('https://api.envato.com/v3/market/author/sale?code=' .$purchase_code);
        // if(!$response->ok()){
        // dd("hello");
        $generatedKey = $this->generateFormattedNumber();
        $generatedEmail = $this->generateRandomEmail();
        $response = Http::post("http://192.168.12.79:8005/api/superadmin/purchase_code_verify/{$purchase_code}", [
            'purchase_code' => $purchase_code,
            'key' => $generatedKey,
            'email' => $generatedEmail,
            'ip_address' => $request->ip(),
        ]);

        // }else{
        //     dd("Not valid");

        // }
        if ($response->status() === 200 && isset($response['message'])) {
            session(['email' => $generatedEmail]);
            session(['key' => $generatedKey]);
            session(['purchase_code' => $purchase_code]);
            return redirect()->route('system.auth.key');
            // }

        }

        // Handle error if purchase code is already present
        if ($response->status() === 200 && isset($response['error'])) {
            return back()->withErrors(['purchase_code' => $response['error']]);
        }

        return back()->withErrors(['purchase_code' => 'An unexpected error occurred.']);

    }


    protected function generateFormattedNumber()
    {
        // Generate a 12-digit number as a string, padded with zeros if necessary
        $number = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);

        // Split into chunks of 4 and join with dashes
        return implode('-', str_split($number, 4));
    }

    function generateRandomEmail($domain = 'example.com')
    {
        $randomName = Str::random(10);
        return strtolower($randomName . '@' . $domain);
    }


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

        $clientIp = $request->ip(); // Get user's IP

        // 1. Fetch key details from the API
        $response = Http::get("http://192.168.12.79:8005/api/superadmin/{$key}");

        if (!$response->ok() || $response['key'] !== $key) {
            return redirect()->route('system.auth.key')->with(['error' => 'Invalid Key', 'key_value' => $key]);
        }

        $keyData = $response->json();
        // dd($keyData);

        // 2. Check if IP is already used
        if (!empty($keyData['ip_address']) && $keyData['ip_address'] !== $clientIp) {
            return redirect()->route('system.auth.key')->with('error', 'Key is already used by another IP');
        }

        if ($keyData['verified']) {
            return redirect()->route('system.auth.login')->with('error', 'Key already verified');
        }
        // 3. Mark as verified & store IP via API
        Http::post("http://192.168.12.79:8005/api/superadmin/verify/{$key}", [
            'ip_address' => $clientIp,
        ]);

        // 4. Set session
        $request->session()->put('key_verified', true);
        $request->session()->put('session_key', $key);

        // Check if database is already set in backend

        if (!empty($keyData['database']) && $keyData['database'] !== 'system') {
            // dd("hello");
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
        $request->validate([
            'database_name' => 'required|alpha_dash|max:20',
            'host_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'db_password' => 'sometimes|nullable|string|min:2|max:32',
        ]);

        $session_key = session('session_key');
        if (empty($session_key)) {
            return redirect()->back()->withErrors(['error' => 'Session key missing. Please restart the process.']);
        }

        $database_name = $request->input('database_name');

        try {
            // STEP 1: Update .env and DB config
            updateEnv([
                'DB_HOST' => $request->input('host_name'),
                'DB_USERNAME' => $request->input('user_name'),
                'DB_PASSWORD' => $request->input('db_password') ?? '',
                'DB_DATABASE' => $database_name,
            ]);

            config([
                'database.connections.mysql.host' => $request->input('host_name'),
                'database.connections.mysql.database' => null,
                'database.connections.mysql.username' => $request->input('user_name'),
                'database.connections.mysql.password' => $request->input('db_password') ?? '',
            ]);

            DB::purge('mysql');
            DB::connection('mysql')->getPdo();

            // STEP 2: Create the database
            try {
                DB::statement("CREATE DATABASE IF NOT EXISTS `$database_name`");
                Log::info("Database created: $database_name");
            } catch (\Illuminate\Database\QueryException $e) {
                throw new Exception('Database creation failed: ' . $e->getMessage());
            }


            // STEP 4: Reconnect with new DB name
            config(['database.connections.mysql.database' => $database_name]);
            DB::purge('mysql');
            DB::connection('mysql')->getPdo();

            // STEP 5: Store DB name in session
            session(['database_name' => $database_name]);

            // STEP 6: Run migration
            try {
                Schema::connection('mysql')->create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('email')->unique();
                    $table->string('password');
                    $table->timestamps();
                });
                Log::info("Migration complete for: $database_name");
            } catch (Throwable $e) {
                Log::error('Migration failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'code' => $e->getCode(),
                ]);
                throw new Exception('Migration failed: ' . $e->getMessage());
            }
            // sleep(0.9);
            // STEP 7: Seed data

            session()->forget('show_database_page');


            // STEP 8: Done
            return redirect()->route('system.auth.login')->with('success', 'Database created and initialized successfully.');

        } catch (Exception $e) {
            Log::error("Database setup failed: " . $e->getMessage());
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

        Log::info("seed running");
        $sessionKey = session('session_key');
        // dd($sessionKey);

        if (!$sessionKey) {
            // Fallback to IP-based lookup if session key is not present
            $clientIp = $request->ip();
            $response = Http::withoutVerifying()
                ->retry(3, 200)
                ->get("http://192.168.12.79:8005/api/superadmin/get/{$clientIp}");

            if (!$response->ok()) {
                return redirect()->route('system.auth.key')->with('error', 'Key could not be verified. Please verify again.');
            }

            $keyData = $response->json();
            $sessionKey = $keyData['key'] ?? null;
            session(['profile_logo' => $keyData['profile_logo']]);

            if (!$sessionKey) {
                return redirect()->route('system.auth.key')->with('error', 'Key data not found.');
            }

            session(['session_key' => $sessionKey]); // Re-store it in session
            session(['purchase_code' => $keyData['purchase_code']]); // Re-store it in session
        } else {
            $response = Http::withoutVerifying()
                ->retry(3, 200)
                ->get("http://192.168.12.79:8005/api/superadmin/key/{$sessionKey}");

            $keyData = $response->json();
            session(['profile_logo' => $keyData['profile_logo']]);
            session(['purchase_code' => $keyData['purchase_code']]);
        }

        $keyData = $response->json();
        // dd($keyData);
        if ($keyData['database'] == "system") {
            try {
                DB::table('users')->insert([
                    'email' => $keyData['email'],
                    'password' => $keyData['password'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("User seeded ");
            } catch (Throwable $e) {
                Log::error('Seeding failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'code' => $e->getCode(),
                ]);
                throw new Exception('Seeding failed: ' . $e->getMessage());
            }
        }
        // sleep(0.9);
        $email = $request->input('email');
        $password = $request->input('password');

        try {

            // 1. First check in seed database
            $user = DB::table('users')->where('email', $email)->first();

            // if ($user && Hash::check($password, $user->password)) {
            if ($user && $password == $user->password) {
                $database_name = env('DB_DATABASE');
                // dd($database_name);
                $sessionKey = session('session_key');
                Http::withoutVerifying()
                    ->retry(3, 200) // retry on fail
                    ->post("http://192.168.12.79:8005/api/superadmin/save/{$sessionKey}", [
                        'database_name' => $database_name,
                    ]);
                // Set session or login using Auth
                $request->session()->put('user_logged_in', true);


                return redirect()->route('dashboard');
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

        } catch (Exception $e) {
            Log::error("Database setup failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

}