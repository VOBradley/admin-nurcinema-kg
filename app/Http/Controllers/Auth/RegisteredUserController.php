<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $users = User::all();
        return Inertia::render('Auth/Register', [
            'users' => $users
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->name = $request->post('name');
            $user->email = $request->post('email');
            $user->is_admin = $request->post('is_admin');
            $user->name = $request->post('name');
            if ($request->post('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect(route('register', absolute: false));
    }
}
