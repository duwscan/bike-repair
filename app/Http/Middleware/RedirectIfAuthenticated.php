<?php

namespace App\Http\Middleware;

use App\Utils\FilamentUtils;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {

        $guards = empty($guards) ? [null] : $guards;
        if (Filament::auth()->check()) {
            FilamentUtils::setCurrentPanelByUserRole(Auth::user());
        }
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                if ($request->fullUrlIs() || $request->fullUrlIs(Filament::getCurrentPanel()->getRegistrationUrl())) {

                    return redirect(Filament::getCurrentPanel()->getHomeUrl());
                }
            }
        }

        return $next($request);
    }
}
