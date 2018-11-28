<?php

namespace Remachinon\Http\Middleware;

use Closure;

class IpCheck
{
    public function handle($request, Closure $next)
    {
        // @todo this can be highly improved. It's just a temporary patch
        $ip = $request->ip();
        $whitelist = explode(',', config('app.allow_registry_from'));
        if (in_array($ip, $whitelist, true)) {
            return $next($request);
        }
        // Try to find whitelisted wildcarded IPs
        foreach ($whitelist as $i) {
            $wildcardPos = strpos($i, "*");
            if ($wildcardPos !== false && substr($ip, 0, $wildcardPos) . "*" === $i) {
                return $next($request);
            }
        }
        return redirect('home');
    }
}