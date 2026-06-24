<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'email'     => 'required|email|max:150|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if ($user->password && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
