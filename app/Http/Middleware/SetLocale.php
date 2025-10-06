<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales
     */
    protected array $supportedLocales = ['en', 'ar'];

    /**
     * Default locale
     */
    protected string $defaultLocale = 'en';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $locale = $this->getLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Add locale to response header
        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    /**
     * Get locale from request
     */
    protected function getLocale(Request $request): string
    {
        // Priority 1: Query parameter (?lang=ar)
        if ($request->has('lang') && in_array($request->query('lang'), $this->supportedLocales)) {
            return $request->query('lang');
        }

        // Priority 2: Accept-Language header
        $headerLocale = $request->header('Accept-Language');
        if ($headerLocale) {
            // Parse Accept-Language header (e.g., "en-US,en;q=0.9,ar;q=0.8")
            $locale = substr($headerLocale, 0, 2);
            if (in_array($locale, $this->supportedLocales)) {
                return $locale;
            }
        }

        // Priority 3: User preference (if authenticated)
        if ($request->user() && isset($request->user()->locale)) {
            $userLocale = $request->user()->locale;
            if (in_array($userLocale, $this->supportedLocales)) {
                return $userLocale;
            }
        }

        // Priority 4: Default locale
        return $this->defaultLocale;
    }
}
