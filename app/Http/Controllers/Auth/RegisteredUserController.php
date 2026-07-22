<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
class RegisteredUserController extends Controller
{
    public function create()
    {
        $roles = Role::where('show_in_registration', true)->orderBy('name')->get(['name']);
        return Inertia::render('Auth/Register', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roleNames = Role::where('show_in_registration', true)->pluck('name')->toArray();
        $validRoles = array_merge(['teacher'], $roleNames);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'job_title' => ['required', 'string', 'in:'.implode(',', $validRoles)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->job_title === 'teacher' ? 'teacher' : $request->job_title,
            'job_title' => $request->job_title,
        ]);

        $user->assignRole($user->role);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
