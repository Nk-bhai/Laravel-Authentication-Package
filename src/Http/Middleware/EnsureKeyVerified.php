<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EnsureKeyVerified
{
//     public function handle(Request $request, Closure $next): Response
//     {
//         if ($request->is('key') || $request->routeIs('system.auth.key')) {
//             return $next($request);
//         }

//         if (!session()->has('key_verified')) {
//             // dump(session()->has('key_verified'));
//             return redirect()->route('system.auth.key')->with('error', 'Key verification required');
//         }

//         // If key verified, allow to proceed
//         return $next($request);
//     }



// public function handle(Request $request, Closure $next): Response
// {
//     if ($request->is('key') || $request->routeIs('system.auth.key')) {
//         $key = session('session_key');

//         if ($key) {
//             $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);

//             if ($data->ok() && $data['verified']) {
//                 return redirect()->route('system.auth.login')->with('message', 'Key already verified');
//             }
//         }

//         return $next($request);
//     }

//     $key = session('session_key');
//     if (!$key) {
//         return redirect()->route('system.auth.key')->with('error', 'Key verification required');
//     }

//     $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);

//     if (!$data->ok() || !$data['verified']) {
//         return redirect()->route('system.auth.key')->with('error', 'Key verification required');
//     }

//     return $next($request);
// }

public function handle(Request $request, Closure $next): Response
{
    $key = session('session_key');

    // 🔒 Check if key is verified via API
    if (!$key) {
        return redirect()->route('system.auth.key')->with('error', 'Key verification required');
    }

    $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);

    if (!$data->ok() || !$data['verified']) {
        return redirect()->route('system.auth.key')->with('error', 'Key verification required');
    }

    // 🔒 Allow only /login page if not logged in
    if (!session()->has('user_logged_in')) {
        // If trying to access anything other than login, redirect
        if (!$request->is('login') && !$request->routeIs('system.auth.login') && !$request->routeIs('system.auth.login.attempt')) {
            return redirect()->route('system.auth.login')->with('error', 'Login required');
        }
    }

    return $next($request);
}

}



