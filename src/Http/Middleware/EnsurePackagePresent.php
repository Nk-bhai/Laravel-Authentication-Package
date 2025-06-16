<?php

namespace Nk\SystemAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePackagePresent
{
    public function handle(Request $request, Closure $next): Response
    {
        
        $requiredClasses = [
            'Nk\SystemAuth\SystemAuthServiceProvider',
            'Nk\SystemAuth\Http\Controllers\AuthController',
            'Nk\SystemAuth\Http\Middleware\EnsureKeyVerified',
        ];

        foreach ($requiredClasses as $class) {
            if (!class_exists($class)) {
                return abort(403, 'System authorization web package class missing or altered.');
            }
        }

        
        $requiredFiles = [
            base_path('vendor/nk/system-auth/src/SystemAuthServiceProvider.php'),
            base_path('vendor/nk/system-auth/src/Http/Controllers/AuthController.php'),
            base_path('vendor/nk/system-auth/src/Http/Middleware/EnsureKeyVerified.php'),
        ];

        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                return abort(403, 'System authorization package file missing or deleted.');
            }
        }

  

        return $next($request);
    }
}
