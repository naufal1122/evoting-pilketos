<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'password' => 'required'
        ]);

        $inputPassword = $request->password;

        // 1. Cari user berdasarkan password plaintext (format NIS siswa)
        $user = User::where('password', $inputPassword)->first();

        // 2. Jika tidak ditemukan, cek apakah password dicocokkan dengan hash Bcrypt (akun Admin)
        if (!$user) {
            foreach (User::all() as $u) {
                if (Hash::check($inputPassword, $u->password)) {
                    $user = $u;
                    break;
                }
            }
        }

        if ($user) {
            Auth::login($user);
            if ($user->role === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Selamat Datang Admin');
            }
            return redirect()->route('home')->with('success', 'Login Berhasil');
        } else {
            return redirect()->route('login')->with('warning', 'Password / NIS Salah');
        }
    }
}
