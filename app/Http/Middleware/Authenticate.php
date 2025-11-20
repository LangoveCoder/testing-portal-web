<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Check the current URL to determine which login page to redirect to
            if ($request->is('super-admin/*')) {
                return route('super-admin.login');
            }
            
            if ($request->is('college/*')) {
                return route('college.login');
            }
            
            // Default to home page
            return route('home');
        }

        return null;
    }
}