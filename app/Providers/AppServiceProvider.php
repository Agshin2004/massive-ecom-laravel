<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Observers\UserObserver;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //* Observers
        User::observe(UserObserver::class);

        //* Custom gates
        Gate::define('is-admin', function (User $user) {
            // user is passed by defualt, if we wanna add some other model to
            // check can pass them as well
            return $user->role === 'admin';
        });

        // Rate Limiter; WILL BE USED VIA throttle:api MIDDLEWARE
        RateLimiter::for('api', function (Request $request) {
            // Illuminate\Http\Request -> actual instance that represents CURRENT HTTP REQUEST
            return Limit::perMinute(40)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    // throwing exception so global middleware will catch it
                    // tried return response() but didnt work since we get message from default
                    // exception handler but if  we returned response() we would need to make some tweaks
                    throw new \Exception('Too many attempts, try again later.', 429);
                });
        });

        // Registering policies; actually could omit this since <MODEL>Policy name convention is usedbut did
        // it for clarity
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
