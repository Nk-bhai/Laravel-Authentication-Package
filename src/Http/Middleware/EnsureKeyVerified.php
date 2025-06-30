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
    //             $data = Http::get('http://192.168.12.79:8005/api/superadmin/' . $key);

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

    //     $data = Http::get('http://192.168.12.79:8005/api/superadmin/' . $key);

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

        // Get session data
        $purchaseCode = session('purchase_code');
        $key = session('session_key');
        $clientIp = $request->ip();

        // Check purchase code verification status
        $purchaseCodeVerified = false;
        $keyData = null;

        if ($purchaseCode) {
            // Try purchase_code from session
            $purchaseCodeData = Http::get("http://192.168.12.79:8005/api/superadmin/purchase_code/{$purchaseCode}");
            $purchaseCodeVerified = $purchaseCodeData->ok() && isset($purchaseCodeData['purchase_code_verified']) && $purchaseCodeData['purchase_code_verified'];
        } elseif ($key) {
            // Fallback to key-based check
            $keyData = Http::get("http://192.168.12.79:8005/api/superadmin/{$key}");
            $purchaseCodeVerified = $keyData->ok() && isset($keyData['purchase_code_verified']) && $keyData['purchase_code_verified'];
        } else {
            // Fallback to client IP
            $ipData = Http::get("http://192.168.12.79:8005/api/superadmin/get/{$clientIp}");
            $purchaseCodeVerified = $ipData->ok() && isset($ipData['purchase_code_verified']) && $ipData['purchase_code_verified'];
        }

        if ($request->is('purchase_code') || $request->routeIs('system.auth.purchase_code')) {
            if ($purchaseCodeVerified) {
                // If purchase code is verified, redirect to login or key page
                return redirect()->route('system.auth.login')->with('message', 'Purchase code already verified');
            }
            // Allow access to purchase code page if not verified or no data exists
            return $next($request);
        }

        // If purchase code is not verified, block all other routes
        if (!$purchaseCodeVerified) {
            return redirect()->route('system.auth.purchase_code')->with('error', 'Purchase code verification required');
        }

        $key = session('session_key');

        // Allow access to `/key` if key is not verified
        if ($request->is('key') || $request->routeIs('system.auth.key')) {
            if ($key) {
                $data = Http::get('http://192.168.12.79:8005/api/superadmin/' . $key);

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
        $data = Http::get('http://192.168.12.79:8005/api/superadmin/get/' . $clientIp);

        // if key is deactived by super admin
        if (!$data->ok() || !$data['verified']) {
            if ($purchaseCodeVerified) {
            // dd("hello");
            session()->flush();

            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
            }
            return redirect()->route('system.auth.purchase_code')->with('error', 'Purchase code verification required');
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



