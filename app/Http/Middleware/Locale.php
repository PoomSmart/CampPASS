<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Session;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            $raw_locale = Session::get('locale');
            if (in_array($raw_locale, config('app.locales')))
                $locale = $raw_locale;
            else
                $locale = config('app.locale');
            App::setLocale($locale);
            setlocale(LC_TIME, "{$locale}.utf8");
        }
        return $next($request);
    }
}