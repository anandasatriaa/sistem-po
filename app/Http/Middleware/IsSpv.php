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
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect ke login jika belum login
        }

        $Jabatan = strtoupper(trim(Auth::user()->Jabatan));
        $allowedRoles = ['SPV GA', 'COO'];
        $allowedRoles = array_map('strtoupper', $allowedRoles);

        if (in_array($Jabatan, $allowedRoles)) {
            return redirect()->route('ga.pr-index');
        }

        // Jika jabatan tidak termasuk pengecualian, lanjutkan dengan pengecekan level
        if (in_array(Auth::user()->lvl, [2, 3, 4])) {
            return $next($request);
        }

        // Jika level atau jabatan tidak sesuai, redirect ke halaman lain
        return redirect()->route('user.pr-index');
    }
}
