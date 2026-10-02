<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        if (Auth::user()->role === 'admin') {
            Alert::info('Info', 'Dialihkan ke Dashboard Admin');
            return redirect()->route('dashboard');
        }

        Alert::warning('Peringatan', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        return redirect()->route('home');
    }
}
