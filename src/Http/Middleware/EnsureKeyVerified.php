<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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


    public function handle(Request $request, Closure $next): Response
    {
        
        $sessionData = session()->all();

        // Option 2: Using request object (equivalent)
        // $sessionData = $request->session()->all();

        // Print to log (recommended)
        Log::info('Session Data:', $sessionData);
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

        if (!$data->ok() || !$data['verified']) {
            session()->flush();
            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
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



