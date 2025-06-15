<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EnsureKeyVerified
{

    public function handle(Request $request, Closure $next): Response
    {
        $key = session('session_key');

        // ✅ STEP 1: Allow access to `/key` if key is not verified
        if ($request->is('key') || $request->routeIs('system.auth.key')) {
            // Allow this only if key is not already verified
            if ($key) {
                $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);

                if ($data->ok() && $data['verified']) {
                    return redirect()->route('system.auth.login')->with('message', 'Key already verified');
                }
            }

            return $next($request); // Allow access to key page if not yet verified
        }

        // ✅ STEP 2: Key must be set and verified to proceed
        if (!$key) {
            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
        }

        $data = Http::get('http://127.0.0.1:8000/api/superadmin/' . $key);

        if (!$data->ok() || !$data['verified']) {
            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
        }

        // ✅ STEP 3: Allow access to login routes before login
        if (!session()->has('user_logged_in')) {
            if (
                $request->is('login') ||
                $request->routeIs('system.auth.login') ||
                $request->routeIs('system.auth.login.attempt')
            ) {
                return $next($request); // Allow access to login pages
            }

            return redirect()->route('system.auth.login')->with('error', 'Login required');
        }

        // ✅ STEP 4: Key is verified AND user is logged in — allow
        return $next($request);
    }

}



