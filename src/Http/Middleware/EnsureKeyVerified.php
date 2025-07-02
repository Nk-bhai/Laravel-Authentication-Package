<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

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


// the purchase code has been verified then proceed to key page . Once the purchase code has been verified it should never get access again even if the browser is reopens or project is restart . For purchase code verification i set a column purchase_code_verified to 1 in database. Make sure that purchase code page should not get access again even if the browser is reopens or project is restart .  
