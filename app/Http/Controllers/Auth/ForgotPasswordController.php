<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($loginField, $request->login)->first();

        if (! $user) {
            return back()->withErrors([
                'login' => 'No account found with that email or username.',
            ])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'login' => 'Your account has been deactivated. Please contact the administrator.',
            ])->withInput();
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

        return back()->with('status', 'If an account exists, we\'ve sent a password reset link to the email address on file.');
    }
}
