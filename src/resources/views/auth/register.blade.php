@extends('layouts.app')

@section('content')
    <div class="form-container">
        <h2 class="form-title">会員登録</h2>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" autofocus>
                @if ($errors->has('name'))
                    <div class="form-error">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}">
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

            <div class="form-group">
                <label for="password_confirmation">確認用パスワード</label>
                <input id="password_confirmation" type="password" name="password_confirmation">
                @if ($errors->has('password_confirmation'))
                    <div class="form-error">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>

            <button type="submit" class="btn-primary">登録する</button>
        </form>

        <p class="form-link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>
    </div>
@endsection