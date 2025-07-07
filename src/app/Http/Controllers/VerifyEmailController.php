<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/mypage/profile');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect('/mypage/profile');
    }
}