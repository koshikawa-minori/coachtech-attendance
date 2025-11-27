@extends('layouts.app')
@section('title', '勤怠詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance/attendance_detail.css') }}">
@endpush

@section('content')
<main>
    <div class="attendance-detail">
        <h1 class="attendance-detail__title">勤怠詳細</h1>

        <form class="detail__form" method="POST" action="{{ route('attendance.detail.request', ['attendance' => $attendance->id]) }}" novalidate>
            @csrf
            <div class="detail__group">
                <label class="detail__label">名前</label>
                <div class="detail__display">
                    {{ optional($attendance->user)->name }}
                </div>
            </div>

            <div class="detail__group">
                <label class="detail__label">日付</label>
                <div class="detail__year">
                    {{ optional(\Carbon\Carbon::parse($attendance->work_date))->format('Y年') }}
                </div>

                <div class="detail__date">
                    {{ optional(\Carbon\Carbon::parse($attendance->work_date))->format('m月d日') }}
                </div>
            </div>

            <div class="detail__group">
                <label class="detail__label" for="clock_in_at">出勤・退勤</label>
                <input class="detail__input" id="clock_in_at" type="time" name="clock_in_at"
                value="{{ optional(\Carbon\Carbon::parse($attendance->clock_in_at))->format('H:i') }}" required>
                <span>～</span>
                <input class="detail__input" id="clock_out_at" type="time" name="clock_out_at"
                value="{{ optional(\Carbon\Carbon::parse($attendance->clock_out_at))->format('H:i') }}" required>
                @error('clock_in_at')
                    <p class="detail__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="detail__group">
                <label class="detail__label" for="break_start_at_1">休憩</label>
                <input class="detail__input" id="break_start_at_1" type="time" name="break_start_at_1"
                value="{{ optional(\Carbon\Carbon::parse($attendance->breakTimes->get(0)?->break_start_at))->format('H:i') }}" required>
                <span>～</span>
                <input class="detail__input" id="break_end_at_1" type="time" name="break_end_at_1"
                value="{{ optional(\Carbon\Carbon::parse($attendance->breakTimes->get(0)?->break_end_at))->format('H:i') }}" required>
                @error('break_start_at_1')
                    <p class="detail__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="detail__group">
                <label class="detail__label" for="break_start_at_2">休憩２</label>
                <input class="detail__input" id="break_start_at_2" type="time" name="break_start_at_2"
                value="{{ optional(\Carbon\Carbon::parse($attendance->breakTimes->get(1)?->break_start_at))->format('H:i') }}" required>
                <span>～</span>
                <input class="detail__input" id="break_end_at_2" type="time" name="break_end_at_2"
                value="{{ optional(\Carbon\Carbon::parse($attendance->breakTimes->get(1)?->break_end_at))->format('H:i') }}" required>
                @error('break_start_at_2')
                    <p class="detail__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="detail__group">
                <label class="detail__label" for="note">備考</label>
                <textarea class="detail__input" id="note" name="note">{{ old('note') }}</textarea>
                @error('note')
                    <p class="detail__error">{{ $message }}</p>
                @enderror
            </div>

            <button class="detail__button" type="submit">修正</button>
        </form>
    </div>
</main>
@endsection
