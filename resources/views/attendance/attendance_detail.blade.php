@extends('layouts.app')
@section('title', '勤怠詳細')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/attendance/attendance_detail.css') }}">
@endpush

@section('content')
<div class="attendance">
    <div class="attendance-detail">
        <h1 class="attendance-detail__title">勤怠詳細</h1>

        <form class="detail__form" method="POST" action="{{ route('attendance.detail.request', ['attendance' => $attendance->id]) }}" novalidate>
            @csrf
            <div class="detail__card">
                <div class="detail__group">
                    <div class="detail__label">名前</div>
                    <div class="detail__display">
                        {{ $attendance->user?->name }}
                    </div>
                </div>

                <div class="detail__group">
                    <div class="detail__label">日付</div>
                    <div class="detail__date">
                        <div class="detail__year">
                            {{ $attendance->work_date?->format('Y年') ?? '' }}
                        </div>

                        <div class="detail__days">
                            {{ $attendance->work_date?->format('m月d日') ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="clock_in_at">出勤・退勤</label>
                    <input class="detail__input" id="clock_in_at" type="time" name="clock_in_at"
                        value="{{ old('clock_in_at', $displayClockIn) }}" {{ $isReadOnly ? 'disabled' : '' }} required>
                    <span>～</span>
                    <input class="detail__input" id="clock_out_at" type="time" name="clock_out_at"
                        value="{{ old('clock_out_at', $displayClockOut) }}" {{ $isReadOnly ? 'disabled' : '' }} required>
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
                        value="{{ old('breaks.0.start', $displayBreaks[0]['start']) }}" {{ $isReadOnly ? 'disabled' : '' }}>
                    <span>～</span>
                    <input class="detail__input" id="break_end_0" type="time" name="breaks[0][end]"
                        value="{{ old('breaks.0.end', $displayBreaks[0]['end']) }}" {{ $isReadOnly ? 'disabled' : '' }}>
                    @error('breaks.0.start')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                    @error('breaks.0.end')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="break_start_1">休憩２</label>
                    <input class="detail__input" id="break_start_1" type="time" name="breaks[1][start]"
                        value="{{ old('breaks.1.start', $displayBreaks[1]['start']) }}" {{ $isReadOnly ? 'disabled' : '' }}>
                    <span>～</span>
                    <input class="detail__input" id="break_end_1" type="time" name="breaks[1][end]"
                        value="{{ old('breaks.1.end', $displayBreaks[1]['end']) }}" {{ $isReadOnly ? 'disabled' : '' }}>
                    @error('breaks.1.start')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                    @error('breaks.1.end')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="detail__group">
                    <label class="detail__label" for="note">備考</label>
                    <textarea class="detail__input detail__textarea" id="note" name="note" {{ $isReadOnly ? 'disabled' : '' }}>{{ old('note', $displayNote) }}</textarea>
                    @error('note')
                        <p class="detail__error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @if ($isReadOnly)
                <p class="detail__message">*承認待ちのため修正はできません。</p>
            @else
                <button class="detail__button" type="submit">修正</button>
            @endif
        </form>
    </div>
</div>
@endsection
