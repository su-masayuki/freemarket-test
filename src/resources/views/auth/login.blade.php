@extends('layouts.app')

@section('content')
    <div class="form-container">
        <h2 class="form-title">ログイン</h2>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email', request()->input('email')) }}" autofocus>
                @if ($errors->has('email'))
                    <div class="form-error">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password">
                @if ($errors->has('password'))
                    <div class="form-error">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <button type="submit" class="btn-primary">ログインする</button>
        </form>

        <p class="form-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </p>
    </div>
@endsection