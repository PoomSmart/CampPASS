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
            // TODO: It requires another page refresh to make Thai localization works
            // Worse, each refresh will alternate between English and Thai (if Thai has been selected)
            \Carbon\Carbon::setLocale($locale);
            setlocale(LC_TIME, "{$locale}.utf8");
        }
        return $next($request);
    }
}