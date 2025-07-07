<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user && is_null($user->email_verified_at)) {
            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                session()->put('unauthenticated_user', true);
                return redirect()->route('verification.notice');
            }
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            session()->forget('unauthenticated_user');
            // if (session()->pull('just_registered')) {
            //     return redirect('/mypage/profile');
            // }
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->withInput();
    }
}