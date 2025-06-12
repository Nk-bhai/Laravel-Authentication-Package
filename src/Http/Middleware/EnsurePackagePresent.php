<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePackagePresent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!class_exists('Nk\SystemAuth\SystemAuthServiceProvider')) {
            return redirect()->route('system.auth.key')->with('error', 'System authorization package missing');
        }

        return $next($request);
    }
}