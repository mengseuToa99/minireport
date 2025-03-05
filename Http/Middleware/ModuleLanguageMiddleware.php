<?php

namespace Modules\MiniReportB1\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class ModuleLanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        $locale = 'en';  // Default locale

        if ($user = auth()->user()) {
            $locale = $user->your_language ?: $locale;  // Override with user's preferred language
        }

        App::setLocale($locale);

        return $next($request);
    }
}
