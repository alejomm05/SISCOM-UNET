<?php

// app/Http/Controllers/Auth/WebLoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WebLoginController extends Controller
{
    // Muestra el formulario de login (GET /admin/login)
    public function showLoginForm()
    {
        if (Auth::guard('admin_web')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login'); 
    }

    // Procesa el login (POST /admin/login)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin_web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard')); 
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales son incorrectas.'], 
        ]);
    }

    // Cierra la sesión (POST /admin/logout)
    public function logout(Request $request)
    {
        Auth::guard('admin_web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}