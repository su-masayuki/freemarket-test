<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
