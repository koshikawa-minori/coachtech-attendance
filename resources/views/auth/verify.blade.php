@extends('layouts.app')
@section('title', 'メール認証のご案内')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/verify.css')}}">
@endpush

@section('content')
<div class="verify">
    <div class="verify__group">
        <h1 class="verify__title">登録時に認証メールを送信しました。</h1>
        <p class="verify__text">
            メール内のリンクを開いて認証を完了してください。<br>
            認証が完了すると勤務登録画面が開きます。
        </p>

        <div class="verify__actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="verify__button">認証メールを再送する</button>
            </form>
        </div>
    </div>
</div>
@endsection
