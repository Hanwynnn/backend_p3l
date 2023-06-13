<?php
namespace App\Http\Middleware;
use Closure;
class RedirectNotPegawai
{
    public function handle($request, Closure $next, $guard="pegawai")
    {
        if(!auth()->guard($guard)->check()) {
            return redirect(route('login'));
        }
        return $next($request);
    }
}
