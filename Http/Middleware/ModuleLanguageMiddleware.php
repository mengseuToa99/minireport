<?php

namespace Modules\MiniReportB1\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class ModuleLanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        $locale = 'en';  // Default locale

        // Check request first (for direct language changes)
        if ($request->has('lang') && in_array($request->lang, ['en', 'kh', 'km'])) {
            $locale = $request->lang;
            session()->put('user.language', $locale);
            
            // If user is logged in, update their preference
            if ($user = auth()->user()) {
                $user->update(['your_language' => $locale]);
            }
        }
        // Then check session
        elseif (session()->has('user.language')) {
            $locale = session()->get('user.language');
        }
        // Finally check user's stored preference
        elseif ($user = auth()->user()) {
            $locale = $user->your_language ?: $locale;
        }

        // Apply locale
        App::setLocale($locale);
        
        // Make sure session has the current locale
        session()->put('user.language', $locale);

        return $next($request);
    }
}
