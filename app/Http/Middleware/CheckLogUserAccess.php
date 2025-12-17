<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogUserAccess
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
    if (
        auth()->check() &&
        auth()->user()->loguser === 'TIDAK'
    ) {
        abort(403, 'Anda tidak memiliki akses ke log');
    }

    return $next($request);
}

}
