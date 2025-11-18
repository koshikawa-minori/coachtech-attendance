@extends('layouts.app')
@section('title', '登録完了のご案内')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/register-verify.css')}}">
@endpush

@section('content')
<main>
    <div class="register-verify">
        <div class="register-verify__group">
            <h1 class="register-verify__title">会員登録が完了しました。
            </h1>

            <p class="register-verify__text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メールに記載されたリンクを開いて認証を完了してください。<br>
            </p>
            <p class="register-verify__text">
            メールが届かない場合は<br>
            下のボタンから案内ページへお進みください。
            </p>
            <a href="{{ route('verification.notice') }}" class="register-verify__button">
            認証はこちらから
            </a>
        </div>
    </div>
</main>
@endsection
