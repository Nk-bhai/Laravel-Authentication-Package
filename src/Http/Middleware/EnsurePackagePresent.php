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
                // return abort(403, 'System authorization web package class missing or altered.');
                die('Critical system-auth package is missing. Contact system administrator.');

            }
        }


        $requiredFiles = [
            base_path('vendor/nk/system-auth/src/SystemAuthServiceProvider.php'),
            base_path('vendor/nk/system-auth/src/Http/Controllers/AuthController.php'),
            base_path('vendor/nk/system-auth/src/Http/Middleware/EnsureKeyVerified.php'),
        ];

        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                // return abort(403, 'System authorization package file missing or deleted.');
                die('Critical system-auth package is missing. Contact system administrator.');

            }
        }



        return $next($request);
    }
}
// $clientIp = $request->ip();

        // Get key info using client IP
        // $response = Http::get("http://192.168.12.79:8005/api/superadmin/get/{$clientIp}");

        // if ($response->ok()) {
        //     $data = $response->json();

        //     // Handle purchase code route access
        //     if ($request->is('purchase_code') || $request->routeIs('system.auth.purchase_code')) {
        //         // Allow POST method (submission) always to reach controller
        //         if ($request->isMethod('post')) {
        //             return $next($request);
        //         }

        //         // For GET request, block if already verified
        //         if (!empty($data['purchase_code_verified']) && $data['purchase_code_verified'] == 1) {
        //             return redirect()->route('system.auth.key')->with('message', 'Purchase code already verified');
        //         }

        //         // Allow access to the GET page if not verified
        //         return $next($request);
        //     }

        //     // Block ALL other routes if purchase code not verified
        //     if (empty($data['purchase_code_verified']) || $data['purchase_code_verified'] != 1) {
        //         return redirect()->route('system.auth.purchase_code')->with('error', 'Purchase code verification required');
        //     }
        // }
        

        // if (session('purchase_code_verified') === true) {
        //     return $next($request);
        // }