@extends('layouts.app')

@section('title', '管理者ログイン')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endpush

@section('content')
<main class="login">
    <h1 class="login__title">管理者ログイン</h1>

    <form class="login__form" method="POST" action="{{ route('admin.login') }}" novalidate>
        @csrf

        <div class="login__group">
            <label class="login__label" for="email">メールアドレス</label>
            <input id="email" class="login__input" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <p class="login__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="login__group">
            <label class="login__label" for="password">パスワード</label>
            <input id="password" class="login__input" type="password" name="password" required>
            @error('password')
                <p class="login__error">{{ $message }}</p>
            @enderror
        </div>

        <button class="login__button" type="submit">管理者ログインする</button>
    </form>
</main>
@endsection
