<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = \App\Models\User::where($loginField, $request->login)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'login' => 'No account found with that email or username.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        if (! $user->hasPasswordSetup()) {
            throw ValidationException::withMessages([
                'login' => 'Your account setup is not complete. Please check your email for the invitation link.',
            ]);
        }

        if (! Auth::attempt([$loginField => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => 'The password you entered is incorrect.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
