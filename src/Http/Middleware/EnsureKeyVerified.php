<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKeyVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('key') || $request->routeIs('system.auth.key')) {
            return $next($request);
        }

        // dump(session()->has('key_verified'));
        if (!session()->has('key_verified')) {
            return redirect()->route('system.auth.key')->with('error', 'Key verification required');
        }

        // If key verified, allow to proceed
        return $next($request);
    }
}
