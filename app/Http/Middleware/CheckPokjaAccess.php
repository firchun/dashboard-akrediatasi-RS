<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPokjaAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->isAdmin() || $user->isVerifikator()) {
            return $next($request);
        }

        $pokjaCode = $request->route('code');
        if ($pokjaCode && $user->pokja) {
            if ($user->pokja->code !== $pokjaCode) {
                abort(403, 'Anda hanya dapat mengakses pokja Anda sendiri.');
            }
        }

        return $next($request);
    }
}
