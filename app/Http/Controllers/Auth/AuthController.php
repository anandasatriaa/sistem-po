<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Log input yang diterima
        Log::info('Login attempt:', [
            'username' => $request->username,
            'password' => $request->password
        ]);

        // Cek apakah username dan password cocok dengan yang ada di database menggunakan model Eloquent
        $user = User::where('uname', $request->username)
            ->where('pwd', $request->password)
            ->first();

        if ($user) {
            // Menyimpan user ke session
            Auth::login($user);

            // Log pengguna yang berhasil login
            Log::info('Login successful:', [
                'username' => $request->username,
                'user_id' => $user->ID
            ]);

            Log::info('Session ID:', [session()->getId()]);

            // Redirect berdasarkan level user
            if ($user->lvl == 1) {
                return redirect()->route('admin.dashboard-index');
            } elseif ($user->lvl == 2 || $user->lvl == 3 || $user->lvl == 4) {
                return redirect()->route('spv.pr-status');
            } else {
                return redirect()->route('user.pr-index');
            }
        } else {
            // Log jika login gagal
            Log::warning('Login failed for username:', [
                'username' => $request->username
            ]);

            // Jika login gagal
            return back()->withErrors([
                'login' => 'Username atau password salah.',
            ]);
        }
    }
}
