<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set Content Security Policy header to allow Midtrans
        $cspHeader = "default-src 'self'; " .
                     "script-src 'self' 'unsafe-inline' 'unsafe-eval' *.sandbox.midtrans.com *.midtrans.com; " .
                     "style-src 'self' 'unsafe-inline' *.sandbox.midtrans.com *.midtrans.com; " .
                     "img-src 'self' data: https: blob:; " .
                     "connect-src 'self' *.sandbox.midtrans.com *.midtrans.com; " .
                     "frame-src 'self' *.sandbox.midtrans.com *.midtrans.com; " .
                     "child-src 'self' *.sandbox.midtrans.com *.midtrans.com;";

        $response->headers->set('Content-Security-Policy', $cspHeader);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');

        return $response;
    }
}
