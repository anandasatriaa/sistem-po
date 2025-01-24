<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSpv
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user level 2, 3, atau 4
        if (in_array(Auth::user()->lvl, [2, 3, 4])) {
            return $next($request);
        }

        // Jika tidak memiliki level yang sesuai, redirect ke halaman lain
        return redirect()->route('user.pr-index');
    }
}
