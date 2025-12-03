@extends('layouts.app')

@section('title', '管理者ログイン')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/auth/admin_login.css')}}">
@endpush

@section('content')
<main>
    <div class="login">
        <h1 class="login__title">ログイン</h1>

        <form class="login__form" method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <input type="hidden" name="login_type" value="admin">

            <div class="login__group">
                <label class="login__label" for="email">メールアドレス</label>
                <input class="login__input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <p class="login__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="login__group">
                <label class="login__label" for="password">パスワード</label>
                <input class="login__input" id="password" type="password" name="password" required>
                @error('password')
                    <p class="login__error">{{ $message }}</p>
                @enderror
            </div>

            <button class="login__button" type="submit">管理者ログインする</button>
        </form>
    </div>
</main>
@endsection
