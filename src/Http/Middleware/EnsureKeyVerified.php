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

        //  Key must be set and verified to proceed
        if (!$key) {
            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
        }

        $data = Http::get('http://192.168.12.79:8005/api/superadmin/' . $key);

        if (!$data->ok() || !$data['verified']) {
            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
        }
        
        //  Allow access to login routes before login
        if (!session()->has('user_logged_in')) {
            if (
                $request->is('login') ||
                $request->routeIs('system.auth.login') 
            ) {
                return $next($request); 
            }

            return redirect()->route('system.auth.login')->with('error', 'Login required');
        }
// || session('show_database_page')
           
        //  Key is verified AND user is logged in — allow
        return $next($request);
    }

}



