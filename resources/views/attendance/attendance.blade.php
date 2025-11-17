@extends('layouts.app')
@section('title', '勤務登録')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<main>
    <div class="attendance">
        <div class="attendance__container">
            <h1 class="attendance__title">勤務登録画面（仮）</h1>
            <p class="attendance__text">
                メール認証が完了したあとに遷移してくる画面です。<br>
                いまは遷移確認用として仮配置しています。
            </p>
        </div>
    </div>
</main>
@endsection
