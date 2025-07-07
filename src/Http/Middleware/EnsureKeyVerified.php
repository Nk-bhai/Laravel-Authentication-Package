<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\UserModel as UserModel;

class EnsureKeyVerified
{
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $key = session('session_key');

    //     // Allow access to `/key` if key is not verified
    //     if ($request->is('key') || $request->routeIs('system.auth.key')) {
    //         if ($key) {
    //             $data = Http::get('http://192.168.6.50:8005/api/superadmin/' . $key);

    //             if ($data->ok() && $data['verified']) {
    //                 return redirect()->route('system.auth.login')->with('message', 'Key already verified');
    //             }
    //         }

    //         return $next($request);
    //     }

    //     if ($request->is('database') || $request->routeIs('system.auth.database')) {
    //         if (session()->has('show_database_page')) {
    //             return $next($request);
    //         } else {
    //             return redirect()->route('system.auth.login')->with('error', 'Unauthorized access to database setup');
    //         }
    //     }

    //     //  Key must be set and verified to proceed
    //     if (!$key) {
    //         return redirect()->route('system.auth.key')->with('error', 'Key verification required');
    //     }

    //     $data = Http::get('http://192.168.6.50:8005/api/superadmin/' . $key);

    //     if (!$data->ok() || !$data['verified']) {
    //         return redirect()->route('system.auth.key')->with('error', 'Key verification required');
    //     }

    //     //  Allow access to login routes before login
    //     if (!session()->has('user_logged_in')) {
    //         if (
    //             $request->is('login') ||
    //             $request->routeIs('system.auth.login')
    //         ) {
    //             return $next($request);
    //         }

    //         return redirect()->route('system.auth.login')->with('error', 'Login required');
    //     }
    //     // || session('show_database_page')

    //     //  Key is verified AND user is logged in — allow
    //     return $next($request);
    // }


    public function handle(Request $request, Closure $next)
    {
        // Check for "Remember Me" cookies to restore session
        if ($request->is('UserTable')) {
            // UserModel
            $tokenss = Cookie::get('remember_tokenss');
            $emailss = Cookie::get('login_emailss');
            $user = UserModel::where('email', $emailss)
            ->where('remember_token', $tokenss)
            ->first();
            
            
            if ($user) {
                session(['user_logged_in' => true]);
                session(['login_email' => $emailss]);

            }

        }   

        if (!session()->has('user_logged_in') && Cookie::has('remember_token')) {
            $token = Cookie::get('remember_token');
            $email = Cookie::get('login_email');

            // First, check in the 'users' table
            $user = DB::table('users')
                ->where('email', $email)
                ->where('remember_token', $token)
                ->first();

            if ($user) {
                session(['user_logged_in' => true]);
                // session(['login_email' => $email]);

            }
        }
        // if (!session()->has('user_logged_in') && Cookie::has('remember_token')) {
        //     $token = Cookie::get('remember_token');
        //     $email = Cookie::get('login_email');

        //     // UserModel
        //     $tokenss = Cookie::get('remember_tokenss');
        //     $emailss = Cookie::get('login_emailss');

        //     // First, check in the 'users' table
        //     $user = DB::table('users')
        //         ->where('email', $email)
        //         ->where('remember_token', $token)
        //         ->first();

        //     // If not found, check in the UserModel 
        //     if (!$user) {
        //         $user = UserModel::where('email', $emailss)
        //             ->where('remember_token', $tokenss)
        //             ->first();
        //     }

        //     if ($user) {
        //         session(['user_logged_in' => true]);
        //         // session(['login_email' => $email]);

        //     }
        // }
        $purchase_code = session('purchase_code');
        $clientIp = $request->ip();
        // if(!empty($purchase_code)){
        //     $response = Http::get("http://192.168.6.50:8005/api/superadmin/purchase_code/{$purchase_code}");
        // }elseif(!empty($clientIp)){
        //     $response = Http::get("http://192.168.6.50:8005/api/superadmin/get/{$clientIp}");
        // }

        // Get verification status using client IP

        $response = Http::get("http://192.168.6.50:8005/api/superadmin/get/{$clientIp}");
        if ($response->ok()) {
            $data = $response->json();

            // Handle purchase code route access
            if ($request->is('purchase_code') || $request->routeIs('system.auth.purchase_code')) {
                if ($request->isMethod('post')) {
                    return $next($request);
                }
                // Block access to purchase_code route if already verified
                if (!empty($data['purchase_code_verified']) && $data['purchase_code_verified'] == 1) {
                    return redirect()->route('system.auth.key')->with('message', 'Purchase code already verified');
                }
                // Allow access to purchase_code route if not verified
                return $next($request);
            }

            // Block ALL other routes if purchase code is not verified
            if (empty($data['purchase_code_verified']) || $data['purchase_code_verified'] != 1) {
                return redirect()->route('system.auth.purchase_code')->with('error', 'Purchase code verification required');
            }
        } else {
            // If API call fails, redirect to purchase_code for safety
            return redirect()->route('system.auth.purchase_code')->with('error', 'Unable to verify purchase code status');
        }


        $key = session('session_key');
        // dd($key);

        // Allow access to `/key` if key is not verified
        if ($request->is('key') || $request->routeIs('system.auth.key')) {
            if ($key) {
                $data = Http::get('http://192.168.6.50:8005/api/superadmin/' . $key);

                if ($data->ok() && $data['verified']) {
                    return redirect()->route('system.auth.login')->with('message', 'Key already verified');
                }
            }

            return $next($request);
        }


        // Allow access to `/database` page if session flag is set
        if ($request->is('database') || $request->routeIs('system.auth.database')) {
            if (session()->has('show_database_page')) {
                return $next($request);
            } else {
                return redirect()->route('system.auth.login')->with('error', 'Unauthorized access to database setup');
            }
        }

        $clientIp = $request->ip();
        $data = Http::get('http://192.168.6.50:8005/api/superadmin/get/' . $clientIp);

        // if key is deactived by super admin
        if (!$data->ok() || !$data['verified']) {
            // if ($purchaseCodeVerified) {
            // dd("hello");
            session()->flush();

            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
            // }
            // return redirect()->route('system.auth.purchase_code')->with('error', 'Purchase code verification required');
        }

        // Allow access to login routes before login
        if (!session()->has('user_logged_in')) {
            if (
                $request->is('login') ||
                $request->routeIs('system.auth.login')
            ) {
                return $next($request);
            }

            return redirect()->route('system.auth.login')->with('error', 'Login required');
        }

        // Key is verified AND user is logged in — allow
        return $next($request);
    }


}
