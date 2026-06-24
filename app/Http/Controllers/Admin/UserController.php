<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SetPasswordInvitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $users = User::with('role')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('full_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'search'));
    }

    public function create()
    {
        return redirect()->route('admin.users.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required|string|max:50|unique:users,username|alpha_dash',
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:users,email',
            'role_id'   => 'required|exists:roles,id',
        ]);

        $token = Str::random(64);

        $user = User::create([
            ...$validated,
            'is_active'                 => true,
            'password_setup_token'      => $token,
            'password_setup_expires_at' => now()->addHours(48),
        ]);

        Mail::to($user->email)->send(new SetPasswordInvitation($user, $token));

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->full_name} created. An invitation email has been sent to {$user->email}.");
    }

    public function edit(User $user)
    {
        return redirect()->route('admin.users.index');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'username'  => "required|string|max:50|unique:users,username,{$user->id}|alpha_dash",
            'full_name' => 'required|string|max:100',
            'email'     => "required|email|max:150|unique:users,email,{$user->id}",
            'role_id'   => 'required|exists:roles,id',
        ];

        if ($user->id !== auth()->id()) {
            $rules['is_active'] = 'nullable|boolean';
        }

        $validated = $request->validate($rules);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->full_name} updated successfully.");
    }

    public function resendInvitation(User $user)
    {
        $token = Str::random(64);

        $user->update([
            'password_setup_token'      => $token,
            'password_setup_expires_at' => now()->addHours(48),
        ]);

        Mail::to($user->email)->send(new SetPasswordInvitation($user, $token));

        return back()->with('success', "Invitation email resent to {$user->email}.");
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $label = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$user->full_name} has been {$label}.");
    }

    public function resetPassword(User $user)
    {
        $token = Str::random(64);

        $user->update([
            'password_setup_token'      => $token,
            'password_setup_expires_at' => now()->addHours(48),
        ]);

        Mail::to($user->email)->send(new SetPasswordInvitation($user, $token));

        return back()->with('success', "Password reset link sent to {$user->email}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->full_name} has been deleted.");
    }
}
