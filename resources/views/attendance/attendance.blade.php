@extends('layouts.app')
@section('title', '勤務登録')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/attendance.css') }}">
@endpush

@section('content')
<div class="attendance">
    <div class="attendance__container">
        @if ($status === 'before_work')
            <h1 class="attendance__title">勤務外</h1>

            <div class="attendance__day">
                {{ $today }} ({{ $weekday }})
            </div>

            <div class="attendance__time">
                {{ $currentTime }}
            </div>

            <form method="POST" action="{{ route('attendance.store') }}">
                @csrf
                <input type="hidden" name="action_type" value="clock_in">
                <button class="attendance__button--inOut" type="submit">出勤</button>
            </form>

        @elseif ($status === 'working')
            <h1 class="attendance__title">出勤中</h1>

            <div class="attendance__day">
                {{ $today }} ({{ $weekday }})
            </div>

            <div class="attendance__time">
                {{ $currentTime }}
            </div>

            <div class="attendance__button">
                <form method="POST" action="{{ route('attendance.store')}}">
                    @csrf
                    <input type="hidden" name="action_type" value="clock_out">
                    <button class="attendance__button--inOut" type="submit">退勤</button>
                </form>

                <form method="POST" action="{{ route('attendance.store')}}">
                    @csrf
                    <input type="hidden" name="action_type" value="break_start">
                    <button class="attendance__button--break" type="submit">休憩入</button>
                </form>
            </div>

        @elseif ($status === 'on_break')
            <h1 class="attendance__title">休憩中</h1>

            <div class="attendance__day">
                {{ $today }} ({{ $weekday }})
            </div>

            <div class="attendance__time">
                {{ $currentTime }}
            </div>

            <form method="POST" action="{{ route('attendance.store')}}">
                @csrf
                <input type="hidden" name="action_type" value="break_end">
                <button class="attendance__button--break" type="submit">休憩戻</button>
            </form>

        @elseif ($status === 'after_work')
            <h1 class="attendance__title">退勤済</h1>

            <div class="attendance__day">
                {{ $today }} ({{ $weekday }})
            </div>

            <div class="attendance__time">
                {{ $currentTime }}
            </div>

            <p class="attendance__text">お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection
