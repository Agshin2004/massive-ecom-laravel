<?php

namespace App\Http\Middleware;

use App\Enums\SellerStatus;
use Closure;
use App\Enums\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        if ($request->user()->role !== Role::Seller->value) {
            throw new AuthorizationException('Unauthorized');
        }

        if ($request->user()->seller->status !== SellerStatus::Approved->value) {
            throw new AuthorizationException('Seller is not approved by user');
        }



        return $next($request);
    }
}
