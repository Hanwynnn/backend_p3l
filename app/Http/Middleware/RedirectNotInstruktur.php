<?php
namespace App\Http\Middleware;
use Closure;
class RedirectNotIntruktur
{
    public function handle($request, Closure $next, $guard="instruktur")
    {
        if(!auth()->guard($guard)->check()) {
            return redirect(route('loginInstruktur'));
        }
        return $next($request);
    }
}
