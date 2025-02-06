<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsGa
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

        // Cek apakah jabatan user adalah "SPV GA" atau "COO"
        $allowedRoles = ['SPV GA', 'COO'];
        if (in_array(Auth::user()->Jabatan, $allowedRoles)) {
            return $next($request);
        }

        // Jika tidak memiliki jabatan yang sesuai, redirect ke halaman user PR Index
        return redirect()->route('user.pr-index')->with('error', 'Anda tidak memiliki akses.');
    }
}
