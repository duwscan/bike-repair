<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;

class CustomAuthenticateMiddleware extends \Filament\Http\Middleware\Authenticate
{
    protected function redirectTo($request): ?string
    {
        return Filament::getPanel('admin')->getLoginUrl();
    }
}
