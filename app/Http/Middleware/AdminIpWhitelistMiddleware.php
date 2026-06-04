<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIpWhitelistMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = explode(',', env('ADMIN_ALLOWED_IPS', '*'));
        
        // If * is provided, allow all IPs
        if (in_array('*', $allowedIps)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        if (!in_array($clientIp, $allowedIps)) {
            abort(403, 'Akses Ditolak: IP Address Anda (' . $clientIp . ') tidak diizinkan masuk ke panel Admin.');
        }

        return $next($request);
    }
}
