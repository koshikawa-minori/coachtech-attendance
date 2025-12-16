@extends('layouts.app')
@section('title', '修正申請承認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/request/admin_approval.css') }}">
@endpush

@section('content')
<div class="request">
    <div class="request-list">
        <h1 class="request-list__title">勤怠詳細</h1>

        {{--@php
            $isReadOnly = $attendance->attendanceCorrection && $attendance->attendanceCorrection->status == false;
        @endphp
        <form class="detail__form" method="POST" action="{{ route('admin.requests.approve', ['id' => $attendance->id]) }}" novalidate>
            @csrf
            <div class="detail__card">
                <div class="detail__group">
                    <label class="detail__label">名前</label>
                    <div class="detail__display">
                        {{ optional($attendance->user)->name }}
                    </div>
                </div>

                <div class="detail__group">
                    <label class="detail__label">日付</label>
                    <div class="detail__date">
                        <div class="detail__year">
                            {{ optional($attendance->work_date)->format('Y年') }}
                        </div>

                        <div class="detail__days">
                            {{ optional($attendance->work_date)->format('m月d日') }}
                        </div>
                    </div>
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="clock_in_at">出勤・退勤</label>
                    <input class="detail__input" id="clock_in_at" type="time" name="clock_in_at"
                    value="{{ old('clock_in_at', optional($attendance->clock_in_at)->format('H:i')) }}" {{ $isReadOnly ? 'disabled' : '' }} required>
                    <span>～</span>
                    <input class="detail__input" id="clock_out_at" type="time" name="clock_out_at"
                    value="{{ old('clock_out_at', optional($attendance->clock_out_at)->format('H:i')) }}" {{ $isReadOnly ? 'disabled' : '' }} required>
                    @error('clock_in_at')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                    @error('clock_out_at')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="break_start_0">休憩</label>
                    <input class="detail__input" id="break_start_0" type="time" name="breaks[0][start]"
                        value="{{ old('breaks.0.start', optional($attendance->breakTimes->get(0)?->break_start_at)->format('H:i')) }}" {{ $isReadOnly ? 'disabled' : '' }} required>
                    <span>～</span>
                    <input class="detail__input" id="break_end_0" type="time" name="breaks[0][end]"
                        value="{{ old('breaks.0.end', optional($attendance->breakTimes->get(0)?->break_end_at)->format('H:i')) }}" {{ $isReadOnly ? 'disabled' : '' }} required>
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="break_start_1">休憩２</label>
                    <input class="detail__input" id="break_start_1" type="time" name="breaks[1][start]"
                        value="{{ old('breaks.1.start', optional($attendance->breakTimes->get(1)?->break_start_at)->format('H:i')) }}" {{ $isReadOnly ? 'disabled' : '' }}>
                    <span>～</span>
                    <input class="detail__input" id="break_end_1" type="time" name="breaks[1][end]"
                        value="{{ old('breaks.1.end', optional($attendance->breakTimes->get(1)?->break_end_at)->format('H:i')) }}" {{ $isReadOnly ? 'disabled' : '' }}>
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="note">備考</label>
                    <textarea class="detail__input detail__textarea" id="note" name="note" {{ $isReadOnly ? 'disabled' : '' }}>{{ old('note') }}</textarea>
                </div>
            </div>
            <button class="detail__button" type="submit">承認</button>
        </form>--}}
    </div>
</div>
@endsection