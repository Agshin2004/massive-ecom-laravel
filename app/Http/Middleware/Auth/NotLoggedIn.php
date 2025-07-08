<?php

namespace App\Http\Middleware\Auth;

use App\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        if ($request->header('authorization')) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}
