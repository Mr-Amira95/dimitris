<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SetPasswordController extends Controller
{
    public function showSetPasswordForm(string $token)
    {
        $user = User::where('password_setup_token', $token)->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'token' => 'This invitation link is invalid.',
            ]);
        }

        if ($user->password_setup_expires_at && now()->isAfter($user->password_setup_expires_at)) {
            return redirect()->route('login')->withErrors([
                'token' => 'This invitation link has expired. Please contact the administrator to resend it.',
            ]);
        }

        return view('auth.set-password', compact('token', 'user'));
    }

    public function setPassword(Request $request, string $token)
    {
        $request->validate([
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = User::where('password_setup_token', $token)->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'token' => 'This invitation link is invalid.',
            ]);
        }

        if ($user->password_setup_expires_at && now()->isAfter($user->password_setup_expires_at)) {
            return redirect()->route('login')->withErrors([
                'token' => 'This invitation link has expired. Please contact the administrator.',
            ]);
        }

        $user->update([
            'password'                  => Hash::make($request->password),
            'password_setup_token'      => null,
            'password_setup_expires_at' => null,
        ]);

        return redirect()->route('login')->with('status', 'Your password has been set. You can now sign in.');
    }
}
